<?php

namespace App\Jobs;

use App\Models\JobApplication;
use App\Notifications\HighMatchApplicantNotification;
use App\Services\ResumeAnalysisService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AnalyzeJobApplication implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 90]; // bound retries, not infinite cost
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public JobApplication $application) {}

    public function middleware(): array
    {
        return [new RateLimited('ai-analysis')];
    }

    /**
     * Execute the job.
     */
    public function handle(ResumeAnalysisService $service): void
    {
        // Idempotency guard — protects against double-processing on retries/race conditions
        $this->application->refresh();
        if ($this->application->ai_status === 'completed') {
            return;
        }

        $this->application->update(['ai_status' => 'processing']);

        $result = $service->analyze($this->application);

        $this->application->update([
            'match_score'    => $result['match_score'],
            'ai_summary'     => $result['summary'],
            'ai_strengths'   => $result['strengths'],
            'ai_gaps'        => $result['gaps'],
            'ai_status'      => 'completed',
            'ai_analyzed_at' => now(),
            'ai_error'       => null,
        ]);

        if ($result['match_score'] >= 85) {
            $this->application->jobListing->user->notify(
                new HighMatchApplicantNotification($this->application)
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('AI resume analysis failed permanently', [
            'application_id' => $this->application->id,
            'error' => $exception->getMessage(),
        ]);

        // Graceful degradation: application itself was never blocked by this.
        $this->application->update([
            'ai_status' => 'failed',
            'ai_error'  => Str::limit($exception->getMessage(), 500),
        ]);
    }
}
