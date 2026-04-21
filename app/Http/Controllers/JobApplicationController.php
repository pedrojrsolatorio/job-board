<?php

namespace App\Http\Controllers;

use App\Models\{JobListing, JobApplication};
use App\Notifications\NewApplicationNotification;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\Request;

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
    public function forJob(JobListing $job)
    {
        $this->authorize('update', $job);
        $applications = $job->applications()->with('user')->latest()->paginate(20);
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
}
