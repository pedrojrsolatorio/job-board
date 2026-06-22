<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeJobApplication;
use App\Models\{JobListing, JobApplication};
use App\Notifications\NewApplicationNotification;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobApplicationController extends Controller
{
    // Job seeker: apply
    public function store(Request $request, JobListing $job)
    {
        // Check if already applied
        if ($job->applications()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You have already applied for this job.');
        }

        $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:2000'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ]);

        $resumePath = $request->file('resume')->store('resumes', 'private');

        $application = $job->applications()->create([
            'user_id' => auth()->id(),
            'cover_letter' => $request->cover_letter,
            'resume_path' => $resumePath,
        ]);

        // Notify employer
        $job->user->notify(new NewApplicationNotification($application));

        // AI screening runs in the background — never blocks the candidate's submission
        AnalyzeJobApplication::dispatch($application);

        return back()->with('success', 'Application submitted successfully!');
    }

    // Job seeker: my applications
    public function myApplications()
    {
        $applications = auth()->user()->applications()
            ->with('jobListing.user')
            ->latest()->paginate(10);
        return view('jobseeker.applications', compact('applications'));
    }

    // Employer: view applications for a job
    public function forJob(Request $request, JobListing $job)
    {
        $this->authorize('update', $job);

        $query = $job->applications()->with('user');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $request->get('sort') === 'score'
            ? $query->orderByDesc('match_score')
            : $query->latest();

        $applications = $query->paginate(20)->withQueryString();

        return view('employer.applications.index', compact('job', 'applications'));
    }

    // Employer: update application status
    public function updateStatus(Request $request, JobApplication $application)
    {
        $this->authorize('update', $application->jobListing);

        $request->validate(['status' => ['required', 'in:pending,reviewed,interview,rejected,hired']]);

        $application->update(['status' => $request->status]);

        // Notify job seeker
        $application->user->notify(new ApplicationStatusNotification($application));

        return back()->with('success', 'Application status updated.');
    }

    // Employer: download applicant resume
    public function downloadResume(JobApplication $application)
    {
        $this->authorize('update', $application->jobListing);

        $resumePath = $application->resume_path;
        if (!$resumePath) {
            abort(404);
        }

        $disk = Storage::disk('private');
        if (!$disk->exists($resumePath)) {
            abort(404);
        }

        $extension = pathinfo($resumePath, PATHINFO_EXTENSION);
        $downloadName = Str::slug($application->user->name) . '-resume' . ($extension ? '.' . $extension : '');

        return $disk->download($resumePath, $downloadName);
    }
}
