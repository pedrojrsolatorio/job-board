<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;

Route::get('/', [JobListingController::class, 'index'])->name('home');

Route::get('/jobs', [JobListingController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobListingController::class, 'show'])->name('jobs.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employer routes
    Route::middleware(['role:employer'])->group(function () {
        Route::resource('my-jobs', JobListingController::class)->except(['index', 'show']);
        Route::get('/my-jobs', [JobListingController::class, 'myJobs'])->name('my-jobs.index');
        Route::post('/payments/promote/{job}', [PaymentController::class, 'promote'])->name('payments.promote');
        Route::get('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
        Route::get('/applications/{job}', [JobApplicationController::class, 'forJob'])->name('applications.for-job');
        Route::patch('/applications/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('applications.update-status');
    });

    // Job seeker routes
    Route::middleware(['role:jobseeker'])->group(function () {
        Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply');
        Route::get('/my-applications', [JobApplicationController::class, 'myApplications'])->name('my-applications');
    });
});

Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

require __DIR__ . '/auth.php';
