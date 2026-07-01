@extends('layouts.app')
@section('title', isset($myJob) ? 'Edit Job Listing' : 'Post a Job')

@section('content')
    <div class="mx-auto max-w-3xl px-6 py-10 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('my-jobs.index') }}"
                class="mb-4 flex items-center gap-1.5 text-xs text-slate-600 transition-colors hover:text-slate-400">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
                Back to My Jobs
            </a>
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-[var(--gold)]">
                {{ isset($myJob) ? 'Edit Listing' : 'New Position' }}
            </p>
            <h1 class="font-display text-3xl font-bold text-white">
                {{ isset($myJob) ? 'Update Job Listing' : 'Post a Job' }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">Fill in the details below to attract the right candidates</p>
        </div>

        <form method="POST" action="{{ isset($myJob) ? route('my-jobs.update', $myJob) : route('my-jobs.store') }}"
            class="space-y-6">
            @csrf
            @if (isset($myJob))
                @method('PUT')
            @endif

            {{-- Basic Info Card --}}
            <div class="border-white/8 space-y-5 rounded-2xl border bg-[var(--navy-800)] p-6">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Basic Information</h2>

                {{-- Job Title --}}
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">
                        Job Title <span class="text-[var(--gold)]">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $myJob->title ?? '') }}"
                        placeholder="e.g. Senior Full Stack Engineer"
                        class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none">
                    @error('title')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category & Job Type --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">
                            Category <span class="text-[var(--gold)]">*</span>
                        </label>
                        <select name="category_id"
                            class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm text-white focus:outline-none">
                            <option value="">Select a category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $myJob->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">
                            Job Type <span class="text-[var(--gold)]">*</span>
                        </label>
                        <select name="job_type"
                            class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm text-white focus:outline-none">
                            @foreach (['full-time' => 'Full Time', 'part-time' => 'Part Time', 'remote' => 'Remote', 'contract' => 'Contract', 'internship' => 'Internship'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('job_type', $myJob->job_type ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('job_type')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Location --}}
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">
                        Location <span class="text-[var(--gold)]">*</span>
                    </label>
                    <input type="text" name="location" value="{{ old('location', $myJob->location ?? '') }}"
                        placeholder="e.g. New York, NY or Remote"
                        class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none">
                    @error('location')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Salary Card --}}
            <div class="border-white/8 space-y-5 rounded-2xl border bg-[var(--navy-800)] p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Compensation</h2>
                    <span class="text-xs text-slate-600">Optional — but increases applications by 40%</span>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">Minimum
                            Salary</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono text-sm text-slate-500">$</span>
                            <input type="number" name="salary_min"
                                value="{{ old('salary_min', $myJob->salary_min ?? '') }}" placeholder="50000"
                                class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] py-3 pl-8 pr-4 text-sm text-white placeholder-slate-600 focus:outline-none">
                        </div>
                        @error('salary_min')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">Maximum
                            Salary</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono text-sm text-slate-500">$</span>
                            <input type="number" name="salary_max"
                                value="{{ old('salary_max', $myJob->salary_max ?? '') }}" placeholder="80000"
                                class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] py-3 pl-8 pr-4 text-sm text-white placeholder-slate-600 focus:outline-none">
                        </div>
                        @error('salary_max')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- AI Generator Panel --}}
            <div
                class="border-[var(--gold)]/20 space-y-3 rounded-2xl border bg-gradient-to-r from-[var(--navy-800)] to-[var(--navy-700)] p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="bg-[var(--gold)]/10 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl">
                            <svg class="h-5 w-5 text-[var(--gold)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">AI Description Writer</p>
                            <p class="mt-0.5 text-xs text-slate-500">Fill in the title, type, and salary above, then let AI
                                draft a starting point.</p>
                        </div>
                    </div>
                    <button type="button" id="ai-generate-btn"
                        class="flex items-center gap-2 whitespace-nowrap rounded-xl bg-[var(--gold)] px-4 py-2.5 text-sm font-semibold text-[var(--navy-950)] transition-colors hover:bg-[var(--gold-lt)] disabled:cursor-not-allowed disabled:opacity-50">
                        <svg id="ai-generate-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span id="ai-generate-label">Generate with AI</span>
                    </button>
                </div>
                <p id="ai-generate-error" class="hidden text-xs text-red-400"></p>
            </div>

            {{-- Description Card --}}
            <div class="border-white/8 space-y-5 rounded-2xl border bg-[var(--navy-800)] p-6">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Job Description</h2>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">
                        Description <span class="text-[var(--gold)]">*</span>
                        <span class="ml-2 font-normal normal-case tracking-normal text-slate-600">Minimum 100
                            characters</span>
                    </label>
                    <textarea name="description" rows="12"
                        placeholder="Describe the role, responsibilities, requirements, and what makes this opportunity unique..."
                        class="input-gold w-full resize-y rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm leading-relaxed text-white placeholder-slate-600 focus:outline-none">{{ old('description', $myJob->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tags Card --}}
            <div class="border-white/8 space-y-4 rounded-2xl border bg-[var(--navy-800)] p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Skills & Tags</h2>
                    <span class="text-xs text-slate-600">Help candidates find your listing</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tags', isset($myJob) ? $myJob->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                class="peer hidden">
                            <span
                                class="peer-checked:border-[var(--gold)]/50 peer-checked:bg-[var(--gold)]/10 block rounded-lg border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:border-white/25 hover:text-slate-300 peer-checked:text-[var(--gold)]">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Settings Card --}}
            <div class="border-white/8 space-y-5 rounded-2xl border bg-[var(--navy-800)] p-6">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-[var(--gold)]">Listing Settings</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label
                            class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">Status</label>
                        <select name="status"
                            class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm text-white focus:outline-none">
                            <option value="active"
                                {{ old('status', $myJob->status ?? 'active') === 'active' ? 'selected' : '' }}>Active —
                                publicly visible</option>
                            <option value="draft"
                                {{ old('status', $myJob->status ?? '') === 'draft' ? 'selected' : '' }}>Draft — hidden
                                from public</option>
                            <option value="closed"
                                {{ old('status', $myJob->status ?? '') === 'closed' ? 'selected' : '' }}>Closed — no longer
                                accepting</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">Application
                            Deadline</label>
                        <input type="date" name="expires_at"
                            value="{{ old('expires_at', isset($myJob) && $myJob->expires_at ? $myJob->expires_at->format('Y-m-d') : '') }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="input-gold w-full rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm text-white focus:outline-none">
                        @error('expires_at')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('my-jobs.index') }}" class="text-sm text-slate-500 transition-colors hover:text-white">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center gap-2 rounded-xl bg-[var(--gold)] px-8 py-3.5 text-sm font-semibold text-[var(--navy-950)] transition-colors hover:bg-[var(--gold-lt)]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                    {{ isset($myJob) ? 'Update Job Listing' : 'Publish Job Listing' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('ai-generate-btn')?.addEventListener('click', async function() {
            const btn = this;
            const label = document.getElementById('ai-generate-label');
            const icon = document.getElementById('ai-generate-icon');
            const errorEl = document.getElementById('ai-generate-error');
            const descriptionField = document.querySelector('textarea[name="description"]');

            const title = document.querySelector('input[name="title"]')?.value.trim();
            const jobType = document.querySelector('select[name="job_type"]')?.value;
            const location = document.querySelector('input[name="location"]')?.value.trim();
            const salaryMin = document.querySelector('input[name="salary_min"]')?.value;
            const salaryMax = document.querySelector('input[name="salary_max"]')?.value;

            const skills = Array.from(document.querySelectorAll('input[name="tags[]"]:checked'))
                .map(el => el.nextElementSibling?.textContent.trim())
                .filter(Boolean);

            errorEl.classList.add('hidden');

            if (!title || !jobType) {
                errorEl.textContent = 'Please fill in the job title and job type first.';
                errorEl.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            label.textContent = 'Generating...';
            icon.classList.add('animate-spin');

            try {
                const response = await fetch('{{ route('ai.generate-job-description') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title,
                        job_type: jobType,
                        location,
                        salary_min: salaryMin || null,
                        salary_max: salaryMax || null,
                        skills,
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Something went wrong.');
                }

                descriptionField.value = data.description;
                descriptionField.dispatchEvent(new Event('input'));

            } catch (err) {
                errorEl.textContent = err.message;
                errorEl.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                label.textContent = 'Generate with AI';
                icon.classList.remove('animate-spin');
            }
        });
    </script>
@endpush
