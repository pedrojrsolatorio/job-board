<?php

namespace App\Http\Controllers;

use App\Services\JobDescriptionGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiAssistantController extends Controller
{
    public function generateJobDescription(Request $request, JobDescriptionGeneratorService $generator)
    {
        $data = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'job_type'   => ['required', 'in:full-time,part-time,remote,contract,internship'],
            'location'   => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'skills'     => ['nullable', 'array', 'max:10'],
            'skills.*'   => ['string', 'max:50'],
            'tone'       => ['nullable', 'in:professional,friendly,enthusiastic'],
        ]);

        // Cost control: identical requests within the hour return the cached
        // result instead of calling the API again (handles double-clicks, etc.)
        $cacheKey = 'job-desc:' . md5(auth()->id() . json_encode($data));

        try {
            $description = Cache::remember($cacheKey, now()->addHour(), function () use ($generator, $data) {
                return $generator->generate($data);
            });

            return response()->json([
                'success' => true,
                'description' => $description,
            ]);
        } catch (Throwable $e) {
            Log::warning('AI job description generation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'AI generation is temporarily unavailable. Please write the description manually or try again shortly.',
            ], 503);
        }
    }
}
