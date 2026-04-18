@extends('layouts.app')
@section('title', isset($myJob) ? 'Edit Job Listing' : 'Post a Job')

@section('content')
<div class="max-w-3xl mx-auto px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('my-jobs.index') }}" class="flex items-center gap-1.5 text-xs text-slate-600 hover:text-slate-400 transition-colors mb-4">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
            Back to My Jobs
        </a>
        <p class="text-xs font-semibold uppercase tracking-widest text-[var(--gold)] mb-1">
            {{ isset($myJob) ? 'Edit Listing' : 'New Position' }}
        </p>
        <h1 class="font-display text-3xl font-bold text-white">
            {{ isset($myJob) ? 'Update Job Listing' : 'Post a Job' }}
        </h1>
        <p class="text-slate-500 text-sm mt-1">Fill in the details below to attract the right candidates</p>
    </div>

    <form method="POST"
          action="{{ isset($myJob) ? route('my-jobs.update', $myJob) : route('my-jobs.store') }}"
          class="space-y-6">
        @csrf
        @if(isset($myJob)) @method('PUT') @endif

        {{-- Basic Info Card --}}
        <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 space-y-5">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Basic Information</h2>

            {{-- Job Title --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">
                    Job Title <span class="text-[var(--gold)]">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $myJob->title ?? '') }}"
                       placeholder="e.g. Senior Full Stack Engineer"
                       class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 input-gold focus:outline-none">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Category & Job Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">
                        Category <span class="text-[var(--gold)]">*</span>
                    </label>
                    <select name="category_id"
                            class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white input-gold focus:outline-none">
                        <option value="">Select a category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $myJob->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">
                        Job Type <span class="text-[var(--gold)]">*</span>
                    </label>
                    <select name="job_type"
                            class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white input-gold focus:outline-none">
                        @foreach(['full-time' => 'Full Time', 'part-time' => 'Part Time', 'remote' => 'Remote', 'contract' => 'Contract', 'internship' => 'Internship'] as $val => $label)
                            <option value="{{ $val }}" {{ old('job_type', $myJob->job_type ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('job_type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Location --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">
                    Location <span class="text-[var(--gold)]">*</span>
                </label>
                <input type="text" name="location" value="{{ old('location', $myJob->location ?? '') }}"
                       placeholder="e.g. New York, NY or Remote"
                       class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 input-gold focus:outline-none">
                @error('location') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Salary Card --}}
        <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 space-y-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Compensation</h2>
                <span class="text-xs text-slate-600">Optional — but increases applications by 40%</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Minimum Salary</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-mono">$</span>
                        <input type="number" name="salary_min" value="{{ old('salary_min', $myJob->salary_min ?? '') }}"
                               placeholder="50000"
                               class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl pl-8 pr-4 py-3 text-sm text-white placeholder-slate-600 input-gold focus:outline-none">
                    </div>
                    @error('salary_min') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Maximum Salary</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-mono">$</span>
                        <input type="number" name="salary_max" value="{{ old('salary_max', $myJob->salary_max ?? '') }}"
                               placeholder="80000"
                               class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl pl-8 pr-4 py-3 text-sm text-white placeholder-slate-600 input-gold focus:outline-none">
                    </div>
                    @error('salary_max') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Description Card --}}
        <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 space-y-5">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Job Description</h2>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">
                    Description <span class="text-[var(--gold)]">*</span>
                    <span class="text-slate-600 normal-case tracking-normal font-normal ml-2">Minimum 100 characters</span>
                </label>
                <textarea name="description" rows="12"
                          placeholder="Describe the role, responsibilities, requirements, and what makes this opportunity unique..."
                          class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 resize-y input-gold focus:outline-none leading-relaxed">{{ old('description', $myJob->description ?? '') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Tags Card --}}
        <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Skills & Tags</h2>
                <span class="text-xs text-slate-600">Help candidates find your listing</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                               {{ in_array($tag->id, old('tags', isset($myJob) ? $myJob->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                               class="hidden peer">
                        <span class="block px-3 py-1.5 rounded-lg border text-xs font-medium transition-colors
                                     border-white/10 text-slate-500
                                     peer-checked:border-[var(--gold)]/50 peer-checked:bg-[var(--gold)]/10 peer-checked:text-[var(--gold)]
                                     hover:border-white/25 hover:text-slate-300">
                            {{ $tag->name }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Settings Card --}}
        <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 space-y-5">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Listing Settings</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Status</label>
                    <select name="status"
                            class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white input-gold focus:outline-none">
                        <option value="active"  {{ old('status', $myJob->status ?? 'active') === 'active'  ? 'selected' : '' }}>Active — publicly visible</option>
                        <option value="draft"   {{ old('status', $myJob->status ?? '') === 'draft'  ? 'selected' : '' }}>Draft — hidden from public</option>
                        <option value="closed"  {{ old('status', $myJob->status ?? '') === 'closed' ? 'selected' : '' }}>Closed — no longer accepting</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Application Deadline</label>
                    <input type="date" name="expires_at"
                           value="{{ old('expires_at', isset($myJob) && $myJob->expires_at ? $myJob->expires_at->format('Y-m-d') : '') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white input-gold focus:outline-none">
                    @error('expires_at') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('my-jobs.index') }}"
               class="text-sm text-slate-500 hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="flex items-center gap-2 bg-[var(--gold)] text-[var(--navy-950)] font-semibold px-8 py-3.5 rounded-xl hover:bg-[var(--gold-lt)] transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                {{ isset($myJob) ? 'Update Job Listing' : 'Publish Job Listing' }}
            </button>
        </div>
    </form>
</div>
@endsection
