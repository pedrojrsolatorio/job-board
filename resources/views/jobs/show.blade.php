@extends('layouts.app')
@section('title', $job->title . ' at ' . ($job->user->company_name ?? 'Company'))

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-600 mb-8">
        <a href="{{ route('jobs.index') }}" class="hover:text-slate-400 transition-colors">Jobs</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-500">{{ $job->category->name }}</span>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-400">{{ $job->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ===== LEFT: JOB DETAILS ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Header Card --}}
            <div class="bg-[var(--navy-800)] border {{ $job->is_featured ? 'border-[var(--gold)]/30' : 'border-white/8' }} rounded-2xl p-8">
                <div class="flex items-start gap-5">

                    {{-- Company Logo --}}
                    <div class="flex-shrink-0 w-16 h-16 rounded-xl bg-[var(--navy-900)] border border-white/10 flex items-center justify-center overflow-hidden">
                        @if($job->user->company_logo)
                            <img src="{{ Storage::url($job->user->company_logo) }}" alt="{{ $job->user->company_name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-display font-bold text-[var(--gold)]">
                                {{ strtoupper(substr($job->user->company_name ?? 'C', 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div>
                                @if($job->is_featured)
                                    <span class="inline-block font-mono text-[10px] font-semibold uppercase tracking-wider badge-featured mb-2">⭐ Featured Position</span>
                                @endif
                                <h1 class="font-display text-2xl md:text-3xl font-bold text-white leading-tight">{{ $job->title }}</h1>
                                <p class="text-slate-400 mt-1">{{ $job->user->company_name ?? 'Company' }}</p>
                            </div>
                        </div>

                        {{-- Meta badges --}}
                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            @php
                                $typeColors = [
                                    'full-time'  => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    'part-time'  => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                    'remote'     => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'contract'   => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                    'internship' => 'bg-pink-500/10 text-pink-400 border-pink-500/20',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs border font-medium {{ $typeColors[$job->job_type] ?? '' }}">
                                {{ ucfirst(str_replace('-', ' ', $job->job_type)) }}
                            </span>
                            <span class="flex items-center gap-1.5 text-sm text-slate-400">
                                <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $job->location }}
                            </span>
                            <span class="flex items-center gap-1.5 text-sm text-slate-400">
                                <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Posted {{ $job->created_at->diffForHumans() }}
                            </span>
                            @if($job->expires_at)
                                <span class="flex items-center gap-1.5 text-sm text-slate-400">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Closes {{ $job->expires_at->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Job Description --}}
            <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-8">
                <h2 class="font-display text-xl font-semibold text-white mb-6">Job Description</h2>
                <div class="prose prose-invert prose-sm max-w-none
                            prose-headings:font-display prose-headings:text-white
                            prose-p:text-slate-400 prose-p:leading-relaxed
                            prose-li:text-slate-400
                            prose-strong:text-white
                            prose-a:text-[var(--gold)] prose-a:no-underline hover:prose-a:underline">
                    {!! nl2br(e($job->description)) !!}
                </div>
            </div>

            {{-- Tags --}}
            @if($job->tags->count())
                <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Skills & Technologies</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->tags as $tag)
                            <span class="px-3 py-1.5 rounded-lg bg-[var(--navy-900)] border border-white/8 text-sm text-slate-400 hover:border-[var(--gold)]/30 hover:text-[var(--gold)] transition-colors cursor-default">
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
            <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 sticky top-20">

                {{-- Salary --}}
                @if($job->salary_min || $job->salary_max)
                    <div class="mb-5 pb-5 border-b border-white/8">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Compensation</p>
                        <p class="font-mono text-2xl font-semibold text-white">
                            @if($job->salary_min && $job->salary_max)
                                ${{ number_format($job->salary_min) }} – ${{ number_format($job->salary_max) }}
                            @elseif($job->salary_min)
                                From ${{ number_format($job->salary_min) }}
                            @else
                                Up to ${{ number_format($job->salary_max) }}
                            @endif
                        </p>
                        <p class="text-xs text-slate-600 mt-0.5">per year</p>
                    </div>
                @endif

                {{-- Apply / Status --}}
                @auth
                    @if(auth()->user()->isJobSeeker())
                        @if($hasApplied)
                            <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-4 py-3 text-sm mb-4">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Application submitted
                            </div>
                        @else
                            <button onclick="document.getElementById('apply-modal').classList.remove('hidden')"
                                    class="w-full bg-[var(--gold)] text-[var(--navy-950)] font-semibold py-3.5 rounded-xl hover:bg-[var(--gold-lt)] transition-colors text-sm mb-4">
                                Apply for This Position
                            </button>
                        @endif
                    @elseif(auth()->user()->isEmployer())
                        <p class="text-xs text-slate-500 text-center mb-4">Employer accounts cannot apply for jobs.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center bg-[var(--gold)] text-[var(--navy-950)] font-semibold py-3.5 rounded-xl hover:bg-[var(--gold-lt)] transition-colors text-sm mb-4">
                        Sign In to Apply
                    </a>
                    <a href="{{ route('register') }}"
                       class="block w-full text-center bg-transparent border border-white/10 text-slate-400 font-medium py-3 rounded-xl hover:border-white/25 hover:text-white transition-colors text-sm">
                        Create Free Account
                    </a>
                @endauth

                {{-- Job details list --}}
                <div class="space-y-3 mt-4">
                    @foreach([
                        ['Category',    $job->category->name, 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                        ['Job Type',    ucfirst(str_replace('-', ' ', $job->job_type)), 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['Applications', $job->applications->count() . ' candidates', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
                    ] as [$label, $value, $icon])
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[var(--navy-900)] flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="{{ $icon }}"/></svg>
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
            <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">About the Company</h3>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-[var(--navy-900)] border border-white/10 flex items-center justify-center">
                        <span class="text-sm font-display font-bold text-[var(--gold)]">
                            {{ strtoupper(substr($job->user->company_name ?? 'C', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $job->user->company_name ?? 'Company' }}</p>
                        <p class="text-xs text-slate-500">Hiring Company</p>
                    </div>
                </div>
                @if($job->user->company_description)
                    <p class="text-xs text-slate-500 leading-relaxed">{{ Str::limit($job->user->company_description, 200) }}</p>
                @else
                    <p class="text-xs text-slate-600 italic">No company description provided.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== APPLY MODAL ===== --}}
@auth
    @if(auth()->user()->isJobSeeker() && !$hasApplied)
        <div id="apply-modal"
             class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
             onclick="if(event.target===this) this.classList.add('hidden')">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

            {{-- Modal --}}
            <div class="relative bg-[var(--navy-800)] border border-white/10 rounded-2xl p-8 w-full max-w-lg shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-display text-xl font-bold text-white">Apply Now</h2>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $job->title }} at {{ $job->user->company_name }}</p>
                    </div>
                    <button onclick="document.getElementById('apply-modal').classList.add('hidden')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-[var(--navy-900)] text-slate-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('jobs.apply', $job) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    {{-- Cover Letter --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Cover Letter <span class="text-slate-600 normal-case tracking-normal font-normal">(optional)</span></label>
                        <textarea name="cover_letter" rows="5"
                                  placeholder="Tell the employer why you're a great fit for this role..."
                                  class="w-full bg-[var(--navy-900)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 resize-none input-gold focus:outline-none">{{ old('cover_letter') }}</textarea>
                        @error('cover_letter') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Resume Upload --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Resume / CV <span class="text-[var(--gold)]">*</span></label>
                        <label class="flex items-center gap-4 border-2 border-dashed border-white/10 rounded-xl p-4 cursor-pointer hover:border-[var(--gold)]/30 transition-colors group"
                               id="resume-drop-zone">
                            <div class="w-10 h-10 rounded-lg bg-[var(--navy-900)] flex items-center justify-center flex-shrink-0 group-hover:bg-[var(--gold)]/10 transition-colors">
                                <svg class="w-5 h-5 text-slate-500 group-hover:text-[var(--gold)] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-white" id="file-name-label">Upload your resume</p>
                                <p class="text-xs text-slate-600 mt-0.5">PDF, DOC, DOCX — max 2MB</p>
                            </div>
                            <input type="file" name="resume" accept=".pdf,.doc,.docx" class="hidden"
                                   onchange="document.getElementById('file-name-label').textContent = this.files[0]?.name || 'Upload your resume'">
                        </label>
                        @error('resume') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-[var(--gold)] text-[var(--navy-950)] font-semibold py-3.5 rounded-xl hover:bg-[var(--gold-lt)] transition-colors text-sm">
                        Submit Application
                    </button>
                </form>
            </div>
        </div>

        @if($errors->has('resume') || $errors->has('cover_letter'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('apply-modal')?.classList.remove('hidden');
                });
            </script>
        @endif
    @endif
@endauth

@endsection
