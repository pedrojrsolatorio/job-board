<?php

namespace App\Services;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;

class ResumeAnalysisService
{
    // Cost control: hard caps on input/output size
    private const MAX_RESUME_CHARS = 6000;
    private const MAX_JOB_DESC_CHARS = 2000;
    // private const MODEL = 'claude-haiku-4-5-20251001'; // cheap + fast, fine for scoring
    private const MODEL = 'gemini-2.0-flash'; // check ai.google.dev for current free-tier model names, but run 'curl "https://generativelanguage.googleapis.com/v1beta/models?key=YOUR_API_KEY"' in terminal to see the available model
    private const MAX_TOKENS = 600;

    // public function analyze(JobApplication $application): array
    // {
    //     $resumeText = Str::limit($this->extractText($application->resume_path), self::MAX_RESUME_CHARS, '');
    //     $jobDescription = Str::limit($application->jobListing->description, self::MAX_JOB_DESC_CHARS, '');

    //     $prompt = $this->buildPrompt($application->jobListing->title, $jobDescription, $resumeText);

    //     $response = Http::withHeaders([
    //         'x-api-key' => config('services.anthropic.key'),
    //         'anthropic-version' => '2023-06-01',
    //         'content-type' => 'application/json',
    //     ])
    //         ->timeout(30)
    //         ->retry(2, 500, throw: false) // transient network hiccups, not API logic errors
    //         ->post('https://api.anthropic.com/v1/messages', [
    //             'model' => self::MODEL,
    //             'max_tokens' => self::MAX_TOKENS,
    //             'messages' => [
    //                 ['role' => 'user', 'content' => $prompt],
    //             ],
    //         ]);

    //     if ($response->failed()) {
    //         throw new RuntimeException('Anthropic API error: ' . $response->status() . ' ' . $response->body());
    //     }

    //     return $this->parseResponse($response->json('content.0.text'));
    // }

    public function analyze(JobApplication $application): array
    {
        $resumeText = Str::limit($this->extractText($application->resume_path), self::MAX_RESUME_CHARS, '');
        $jobDescription = Str::limit($application->jobListing->description, self::MAX_JOB_DESC_CHARS, '');

        $prompt = $this->buildPrompt($application->jobListing->title, $jobDescription, $resumeText);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . self::MODEL . ':generateContent?key=' . config('services.gemini.key');

        $response = Http::timeout(30)
            ->retry(2, 500, throw: false)
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => self::MAX_TOKENS,
                    'temperature' => 0.3,
                ],
            ]);

        // Http facade doesn't support a 5th query-array param directly — build the URL instead:
        if ($response->failed()) {
            throw new RuntimeException('Gemini API error: ' . $response->status() . ' ' . $response->body());
        }

        return $this->parseResponse($response->json('candidates.0.content.parts.0.text'));
    }

    private function extractText(string $path): string
    {
        $fullPath = Storage::disk('private')->path($path);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            if ($ext === 'pdf') {
                return (new PdfParser())->parseFile($fullPath)->getText();
            }

            if (in_array($ext, ['doc', 'docx'])) {
                $document = IOFactory::load($fullPath);
                $text = '';
                foreach ($document->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . ' ';
                        }
                    }
                }
                return $text;
            }
        } catch (\Throwable $e) {
            Log::warning('Resume text extraction failed', ['path' => $path, 'error' => $e->getMessage()]);
        }

        throw new RuntimeException("Unsupported or unreadable resume format: .{$ext}");
    }

    private function buildPrompt(string $jobTitle, string $jobDescription, string $resumeText): string
    {
        return <<<PROMPT
            You are helping an employer screen a job applicant. Compare the resume to the job below.

            JOB TITLE: {$jobTitle}

            JOB DESCRIPTION:
            {$jobDescription}

            RESUME:
            {$resumeText}

            Respond ONLY with valid JSON in this exact shape, no markdown fences, no commentary:
            {"match_score": <integer 0-100>, "summary": "<one or two sentence fit summary>", "strengths": ["<short phrase>"], "gaps": ["<short phrase>"]}
            PROMPT;
    }

    private function parseResponse(?string $text): array
    {
        if (!$text) {
            throw new RuntimeException('Empty AI response');
        }

        $clean = preg_replace('/```json|```/', '', $text);
        $data = json_decode(trim($clean), true);

        if (!is_array($data) || !isset($data['match_score'])) {
            throw new RuntimeException('Malformed AI response: ' . $text);
        }

        return [
            'match_score' => max(0, min(100, (int) $data['match_score'])),
            'summary'     => Str::limit((string) ($data['summary'] ?? ''), 500, ''),
            'strengths'   => array_slice((array) ($data['strengths'] ?? []), 0, 6),
            'gaps'        => array_slice((array) ($data['gaps'] ?? []), 0, 6),
        ];
    }
}
