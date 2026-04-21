<?php

namespace App\Http\Controllers;

use App\Models\{JobListing, Category, Tag};
use App\Http\Requests\{StoreJobListingRequest, UpdateJobListingRequest};
use Illuminate\Http\Request;

class JobListingController extends Controller
{
    // Public job listings page
    public function index(Request $request)
    {
        $query = JobListing::with(['user', 'category', 'tags'])->active();

        // Search
        if ($request->filled('q')) {
            $ids = JobListing::search($request->q)->keys();
            $query->whereIn('id', $ids);
        }

        // Filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('type')) {
            $query->where('job_type', $request->type);
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Featured first
        $query->orderByDesc('is_featured')->latest();

        $jobs = $query->paginate(10)->withQueryString();
        $categories = Category::withCount('jobListings')->get();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    // Single job page
    public function show(JobListing $job)
    {
        $job->load(['user', 'category', 'tags']);
        $hasApplied = auth()->check()
            ? $job->applications()->where('user_id', auth()->id())->exists()
            : false;
        return view('jobs.show', compact('job', 'hasApplied'));
    }

    // Employer: my jobs
    public function myJobs()
    {
        $jobs = auth()->user()->jobListings()->with('applications')->latest()->paginate(10);
        return view('employer.jobs.index', compact('jobs'));
    }

    // Employer: create form
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('employer.jobs.create', compact('categories', 'tags'));
    }

    // Employer: store job
    public function store(StoreJobListingRequest $request)
    {
        $job = auth()->user()->jobListings()->create(
            array_merge($request->validated(), ['status' => 'active'])
        );

        if ($request->has('tags')) {
            $job->tags()->sync($request->tags);
        }

        return redirect()->route('my-jobs.index')->with('success', 'Job posted successfully!');
    }

    // Employer: edit form
    public function edit(JobListing $myJob)
    {
        $this->authorize('update', $myJob);
        $categories = Category::all();
        $tags = Tag::all();
        return view('employer.jobs.edit', compact('myJob', 'categories', 'tags'));
    }

    // Employer: update job
    public function update(UpdateJobListingRequest $request, JobListing $myJob)
    {
        $this->authorize('update', $myJob);
        $myJob->update($request->validated());
        $myJob->tags()->sync($request->tags ?? []);
        return redirect()->route('my-jobs.index')->with('success', 'Job updated successfully!');
    }

    // Employer: delete
    public function destroy(JobListing $myJob)
    {
        $this->authorize('delete', $myJob);
        $myJob->delete();
        return redirect()->route('my-jobs.index')->with('success', 'Job deleted.');
    }
}
