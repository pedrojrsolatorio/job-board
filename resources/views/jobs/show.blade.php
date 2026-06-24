@extends('layouts.app')
@section('title', $job->title . ' at ' . ($job->user->company_name ?? 'Company'))

@section('content')
    <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-8 flex items-center gap-2 text-xs text-slate-600">
            <a href="{{ route('jobs.index') }}" class="transition-colors hover:text-slate-400">Jobs</a>
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-500">{{ $job->category->name }}</span>
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-400">{{ $job->title }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            {{-- ===== LEFT: JOB DETAILS ===== --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Header Card --}}
                <div
                    class="{{ $job->is_featured ? 'border-[var(--gold)]/30' : 'border-white/8' }} rounded-2xl border bg-[var(--navy-800)] p-8">
                    <div class="flex items-start gap-5">

                        {{-- Company Logo --}}
                        <div
                            class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-[var(--navy-900)]">
                            @if ($job->user->company_logo)
                                <img src="{{ Storage::url($job->user->company_logo) }}" alt="{{ $job->user->company_name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <span class="font-display text-2xl font-bold text-[var(--gold)]">
                                    {{ strtoupper(substr($job->user->company_name ?? 'C', 0, 1)) }}
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    @if ($job->is_featured)
                                        <span
                                            class="badge-featured mb-2 inline-block font-mono text-[10px] font-semibold uppercase tracking-wider">⭐
                                            Featured Position</span>
                                    @endif
                                    <h1 class="font-display text-2xl font-bold leading-tight text-white md:text-3xl">
                                        {{ $job->title }}</h1>
                                    <p class="mt-1 text-slate-400">{{ $job->user->company_name ?? 'Company' }}</p>
                                </div>
                            </div>

                            {{-- Meta badges --}}
                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                @php
                                    $typeColors = [
                                        'full-time' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'part-time' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'remote' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'contract' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                        'internship' => 'bg-pink-500/10 text-pink-400 border-pink-500/20',
                                    ];
                                @endphp
                                <span
                                    class="{{ $typeColors[$job->job_type] ?? '' }} rounded-full border px-3 py-1 text-xs font-medium">
                                    {{ ucfirst(str_replace('-', ' ', $job->job_type)) }}
                                </span>
                                <span class="flex items-center gap-1.5 text-sm text-slate-400">
                                    <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $job->location }}
                                </span>
                                <span class="flex items-center gap-1.5 text-sm text-slate-400">
                                    <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Posted {{ $job->created_at->diffForHumans() }}
                                </span>
                                @if ($job->expires_at)
                                    <span class="flex items-center gap-1.5 text-sm text-slate-400">
                                        <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="1.5">
                                            <path
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Closes {{ $job->expires_at->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Job Description --}}
                <div class="border-white/8 rounded-2xl border bg-[var(--navy-800)] p-8">
                    <h2 class="font-display mb-6 text-xl font-semibold text-white">Job Description</h2>
                    <div
                        class="prose prose-invert prose-sm prose-headings:font-display prose-headings:text-white prose-p:text-slate-400 prose-p:leading-relaxed prose-li:text-slate-400 prose-strong:text-white prose-a:text-[var(--gold)] prose-a:no-underline hover:prose-a:underline max-w-none">
                        {!! nl2br(e($job->description)) !!}
                    </div>
                </div>

                {{-- Tags --}}
                @if ($job->tags->count())
                    <div class="border-white/8 rounded-2xl border bg-[var(--navy-800)] p-6">
                        <h3 class="mb-4 text-sm font-semibold text-white">Skills & Technologies</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($job->tags as $tag)
                                <span
                                    class="border-white/8 hover:border-[var(--gold)]/30 cursor-default rounded-lg border bg-[var(--navy-900)] px-3 py-1.5 text-sm text-slate-400 transition-colors hover:text-[var(--gold)]">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- ===== RIGHT: SIDEBAR ===== --}}
            <div class="space-y-5">

                {{-- Apply Card --}}
                <div class="border-white/8 sticky top-20 rounded-2xl border bg-[var(--navy-800)] p-6">

                    {{-- Salary --}}
                    @if ($job->salary_min || $job->salary_max)
                        <div class="border-white/8 mb-5 border-b pb-5">
                            <p class="mb-1 text-xs uppercase tracking-widest text-slate-500">Compensation</p>
                            <p class="font-mono text-2xl font-semibold text-white">
                                @if ($job->salary_min && $job->salary_max)
                                    ${{ number_format($job->salary_min) }} – ${{ number_format($job->salary_max) }}
                                @elseif($job->salary_min)
                                    From ${{ number_format($job->salary_min) }}
                                @else
                                    Up to ${{ number_format($job->salary_max) }}
                                @endif
                            </p>
                            <p class="mt-0.5 text-xs text-slate-600">per year</p>
                        </div>
                    @endif

                    {{-- Apply / Status --}}
                    @auth
                        @if (auth()->user()->isJobSeeker())
                            @if ($hasApplied)
                                <div
                                    class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400">
                                    <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Application submitted
                                </div>
                            @else
                                <button onclick="document.getElementById('apply-modal').classList.remove('hidden')"
                                    class="mb-4 w-full rounded-xl bg-[var(--gold)] py-3.5 text-sm font-semibold text-[var(--navy-950)] transition-colors hover:bg-[var(--gold-lt)]">
                                    Apply for This Position
                                </button>
                            @endif
                        @elseif(auth()->user()->isEmployer())
                            <p class="mb-4 text-center text-xs text-slate-500">Employer accounts cannot apply for jobs.</p>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="mb-4 block w-full rounded-xl bg-[var(--gold)] py-3.5 text-center text-sm font-semibold text-[var(--navy-950)] transition-colors hover:bg-[var(--gold-lt)]">
                            Sign In to Apply
                        </a>
                        <a href="{{ route('register') }}"
                            class="block w-full rounded-xl border border-white/10 bg-transparent py-3 text-center text-sm font-medium text-slate-400 transition-colors hover:border-white/25 hover:text-white">
                            Create Free Account
                        </a>
                    @endauth

                    {{-- Job details list --}}
                    <div class="mt-4 space-y-3">
                        @foreach ([['Category', $job->category->name, 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'], ['Job Type', ucfirst(str_replace('-', ' ', $job->job_type)), 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'], ['Applications', $job->applications->count() . ' candidates', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0']] as [$label, $value, $icon])
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-[var(--navy-900)]">
                                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path d="{{ $icon }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-600">{{ $label }}</p>
                                    <p class="text-sm text-slate-300">{{ $value }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Company Card --}}
                <div class="border-white/8 rounded-2xl border bg-[var(--navy-800)] p-6">
                    <h3 class="mb-4 text-sm font-semibold text-white">About the Company</h3>
                    <div class="mb-3 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 bg-[var(--navy-900)]">
                            <span class="font-display text-sm font-bold text-[var(--gold)]">
                                {{ strtoupper(substr($job->user->company_name ?? 'C', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $job->user->company_name ?? 'Company' }}</p>
                            <p class="text-xs text-slate-500">Hiring Company</p>
                        </div>
                    </div>
                    @if ($job->user->company_description)
                        <p class="text-xs leading-relaxed text-slate-500">
                            {{ Str::limit($job->user->company_description, 200) }}</p>
                    @else
                        <p class="text-xs italic text-slate-600">No company description provided.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== APPLY MODAL ===== --}}
    @auth
        @if (auth()->user()->isJobSeeker() && !$hasApplied)
            <div id="apply-modal" class="fixed inset-0 z-50 flex hidden items-center justify-center p-4"
                onclick="if(event.target===this) this.classList.add('hidden')">

                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

                {{-- Modal --}}
                <div class="relative w-full max-w-lg rounded-2xl border border-white/10 bg-[var(--navy-800)] p-8 shadow-2xl">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="font-display text-xl font-bold text-white">Apply Now</h2>
                            <p class="mt-0.5 text-sm text-slate-500">{{ $job->title }} at {{ $job->user->company_name }}
                            </p>
                        </div>
                        <button onclick="document.getElementById('apply-modal').classList.add('hidden')"
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--navy-900)] text-slate-400 transition-colors hover:text-white">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('jobs.apply', $job) }}" enctype="multipart/form-data"
                        class="space-y-5">
                        @csrf

                        {{-- Cover Letter --}}
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">Cover
                                Letter <span
                                    class="font-normal normal-case tracking-normal text-slate-600">(optional)</span></label>
                            <textarea name="cover_letter" rows="5" placeholder="Tell the employer why you're a great fit for this role..."
                                class="input-gold w-full resize-none rounded-xl border border-white/10 bg-[var(--navy-900)] px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none">{{ old('cover_letter') }}</textarea>
                            @error('cover_letter')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Resume Upload --}}
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-widest text-slate-500">Resume /
                                CV <span class="text-[var(--gold)]">*</span></label>
                            <label
                                class="hover:border-[var(--gold)]/30 group flex cursor-pointer items-center gap-4 rounded-xl border-2 border-dashed border-white/10 p-4 transition-colors"
                                id="resume-drop-zone">
                                <div
                                    class="group-hover:bg-[var(--gold)]/10 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-[var(--navy-900)] transition-colors">
                                    <svg class="h-5 w-5 text-slate-500 transition-colors group-hover:text-[var(--gold)]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-white" id="file-name-label">Upload your resume</p>
                                    <p class="mt-0.5 text-xs text-slate-600">PDF, DOC, DOCX — max 2MB</p>
                                </div>
                                <input type="file" name="resume" accept=".pdf,.doc,.docx" class="hidden"
                                    onchange="document.getElementById('file-name-label').textContent = this.files[0]?.name || 'Upload your resume'">
                            </label>
                            @error('resume')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-[var(--gold)] py-3.5 text-sm font-semibold text-[var(--navy-950)] transition-colors hover:bg-[var(--gold-lt)]">
                            Submit Application
                        </button>
                    </form>
                </div>
            </div>

            @if ($errors->has('resume') || $errors->has('cover_letter'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        document.getElementById('apply-modal')?.classList.remove('hidden');
                    });
                </script>
            @endif
        @endif
    @endauth

@endsection
