<?php

namespace App\Providers;

use App\Models\JobListing;
use App\Policies\JobListingPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        Gate::policy(JobListing::class, JobListingPolicy::class);

        // Cost control: cap AI resume analyses to 30/minute app-wide,
        // so a burst of applications can't spike your API bill.
        RateLimiter::for('ai-analysis', function () {
            // return Limit::perMinute(30);
            return Limit::perMinute(10); // free tier is usually limited to ~15 RPM
        });

        // Cost control: each employer can generate at most 5 descriptions per minute
        RateLimiter::for('ai-job-description', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });
    }
}
