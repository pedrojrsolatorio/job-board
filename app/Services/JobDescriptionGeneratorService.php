<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class JobDescriptionGeneratorService
{
    // private const MODEL = 'gemini-2.0-flash'; // verify current free-tier model name at ai.google.dev
    private const MODEL = 'gemini-3-flash-preview';
    private const MAX_TOKENS = 500;

    public function generate(array $data): string
    {
        $prompt = $this->buildPrompt($data);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
            . self::MODEL . ':generateContent?key=' . config('services.gemini.key');

        $response = Http::timeout(20)
            ->retry(1, 300, throw: false)
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => self::MAX_TOKENS,
                    'temperature' => 0.6,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini API error: ' . $response->status() . ' ' . $response->body());
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$text || !is_string($text)) {
            throw new RuntimeException('Empty response from Gemini');
        }

        return trim($text);
    }

    private function buildPrompt(array $data): string
    {
        $title    = Str::limit($data['title'], 255, '');
        $jobType  = str_replace('-', ' ', $data['job_type']);
        $location = $data['location'] ?? 'Not specified';
        $skills   = !empty($data['skills']) ? implode(', ', array_slice($data['skills'], 0, 10)) : 'Not specified';
        $tone     = $data['tone'] ?? 'professional';

        $salaryLine = 'Not specified';
        if (!empty($data['salary_min']) || !empty($data['salary_max'])) {
            $min = $data['salary_min'] ?? null;
            $max = $data['salary_max'] ?? null;
            $salaryLine = $min && $max
                ? "\${$min} - \${$max} per year"
                : "Starting at $" . ($min ?? $max) . " per year";
        }

        return <<<PROMPT
            Write a job description for a job board posting. Output plain text only — no markdown asterisks, no headers with #, just clean paragraphs and simple line-based section labels.

            Job Title: {$title}
            Job Type: {$jobType}
            Location: {$location}
            Salary: {$salaryLine}
            Key Skills: {$skills}
            Tone: {$tone}

            Structure the description with these sections, each as a plain text label followed by content:
            About the Role
            Key Responsibilities (as a short list using a dash per line)
            Requirements (as a short list using a dash per line)

            Keep the total length under 250 words. Do not include placeholder text like [Company Name] — write generically about "our team" instead. Output only the description, no preamble like "Here is the description:".
            PROMPT;
    }
}
