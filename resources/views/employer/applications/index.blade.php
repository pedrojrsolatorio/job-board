@extends('layouts.app')
@section('title', 'Applications — ' . $job->title)

@section('content')
    <div class="mx-auto max-w-6xl px-6 py-10 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('my-jobs.index') }}"
                    class="mb-3 flex items-center gap-1.5 text-xs text-slate-600 transition-colors hover:text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to My Jobs
                </a>
                <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-[var(--gold)]">Applications</p>
                <h1 class="font-display text-2xl font-bold text-white">{{ $job->title }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $applications->total() }} total applicants</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Status tabs --}}
                <div class="border-white/8 flex items-center gap-1.5 rounded-xl border bg-[var(--navy-800)] p-1">
                    @foreach (['all' => 'All', 'pending' => 'Pending', 'interview' => 'Interview', 'hired' => 'Hired', 'rejected' => 'Rejected'] as $val => $lbl)
                        <a href="{{ route('applications.for-job', array_merge(['job' => $job->slug, 'sort' => request('sort')], $val !== 'all' ? ['status' => $val] : [])) }}"
                            class="{{ request('status', 'all') === $val ? 'bg-[var(--gold)] text-[var(--navy-950)]' : 'text-slate-500 hover:text-white' }} rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">
                            {{ $lbl }}
                        </a>
                    @endforeach
                </div>

                {{-- AI sort toggle --}}
                <a href="{{ route('applications.for-job', array_merge(['job' => $job->slug, 'status' => request('status')], ['sort' => request('sort') === 'score' ? 'recent' : 'score'])) }}"
                    class="{{ request('sort') === 'score' ? 'border-[var(--gold)]/50 bg-[var(--gold)]/10 text-[var(--gold)]' : 'border-white/10 text-slate-500 hover:text-white' }} flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-medium transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Sort by AI Match
                </a>
            </div>
        </div>

        {{-- Applications List --}}
        @if ($applications->count())
            <div class="space-y-3">
                @foreach ($applications as $app)
                    <div class="border-white/8 rounded-2xl border bg-[var(--navy-800)] p-6 transition-colors hover:border-white/15"
                        x-data="{ expanded: false }">
                        <div class="flex items-start gap-4">

                            {{-- Avatar --}}
                            <div
                                class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-[var(--navy-700)]">
                                <span
                                    class="font-display text-base font-bold text-[var(--gold)]">{{ strtoupper(substr($app->user->name, 0, 1)) }}</span>
                            </div>

                            {{-- Info --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-white">{{ $app->user->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $app->user->email }}</p>
                                        <p class="mt-0.5 text-xs text-slate-600">Applied
                                            {{ $app->created_at->diffForHumans() }}</p>
                                    </div>

                                    {{-- Status & Actions --}}
                                    <div class="flex flex-wrap items-center gap-3">
                                        @php
                                            $statusMap = [
                                                'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                                'reviewed' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                                'interview' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                                'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                                'hired' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            ];
                                        @endphp
                                        <span
                                            class="{{ $statusMap[$app->status] ?? '' }} rounded-full border px-2.5 py-1 text-xs font-medium">{{ ucfirst($app->status) }}</span>

                                        {{-- Status updater --}}
                                        <form action="{{ route('applications.update-status', $app) }}" method="POST"
                                            class="flex items-center gap-2">
                                            @csrf @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                class="input-gold rounded-lg border border-white/10 bg-[var(--navy-900)] px-2 py-1.5 text-xs text-slate-400 focus:outline-none">
                                                <option value="">Update status...</option>
                                                @foreach (['pending' => 'Mark Pending', 'reviewed' => 'Mark Reviewed', 'interview' => 'Invite to Interview', 'rejected' => 'Reject', 'hired' => 'Hire'] as $val => $lbl)
                                                    <option value="{{ $val }}"
                                                        {{ $app->status === $val ? 'selected' : '' }}>{{ $lbl }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>

                                        {{-- Download resume --}}
                                        <a href="{{ route('resumes.download', $app) }}"
                                            class="flex items-center gap-1.5 rounded-lg border border-white/10 px-3 py-1.5 text-xs text-slate-500 transition-colors hover:border-white/25 hover:text-white">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.5">
                                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Resume
                                        </a>

                                        {{-- Expand cover letter --}}
                                        @if ($app->cover_letter)
                                            <button @click="expanded = !expanded"
                                                class="flex items-center gap-1.5 rounded-lg border border-white/10 px-3 py-1.5 text-xs text-slate-500 transition-colors hover:border-white/25 hover:text-white">
                                                <svg class="h-3.5 w-3.5 transition-transform"
                                                    :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M19 9l-7 7-7-7" />
                                                </svg>
                                                <span x-text="expanded ? 'Hide' : 'Cover Letter'"></span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- ===== AI MATCH PANEL ===== --}}
                                <div class="mt-4 border-t border-white/5 pt-4">
                                    @if ($app->ai_status === 'completed')
                                        @php
                                            $scoreColor = match (true) {
                                                $app->match_score >= 85
                                                    => 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10',
                                                $app->match_score >= 60
                                                    => 'text-[var(--gold)] border-[var(--gold)]/30 bg-[var(--gold)]/10',
                                                default => 'text-slate-400 border-white/10 bg-white/5',
                                            };
                                        @endphp
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="{{ $scoreColor }} flex h-14 w-14 flex-shrink-0 flex-col items-center justify-center rounded-xl border">
                                                <span
                                                    class="font-mono text-lg font-bold leading-none">{{ $app->match_score }}</span>
                                                <span class="text-[9px] uppercase tracking-wider opacity-70">match</span>
                                            </div>
                                            <div class="flex-1">
                                                <p
                                                    class="mb-1 text-xs font-semibold uppercase tracking-widest text-slate-500">
                                                    AI Screening Summary</p>
                                                <p class="text-sm leading-relaxed text-slate-400">{{ $app->ai_summary }}
                                                </p>
                                                <div class="mt-2 flex flex-wrap gap-3">
                                                    @if (!empty($app->ai_strengths))
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach ($app->ai_strengths as $s)
                                                                <span
                                                                    class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-xs text-emerald-400">+
                                                                    {{ $s }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if (!empty($app->ai_gaps))
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach ($app->ai_gaps as $g)
                                                                <span
                                                                    class="rounded-md border border-red-500/20 bg-red-500/10 px-2 py-0.5 text-xs text-red-400">−
                                                                    {{ $g }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(in_array($app->ai_status, ['pending', 'processing']))
                                        <div class="flex items-center gap-2 text-xs text-slate-500">
                                            <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                            </svg>
                                            AI is analyzing this resume...
                                        </div>
                                    @elseif($app->ai_status === 'failed')
                                        <p class="text-xs italic text-slate-600">AI analysis unavailable for this
                                            application.</p>
                                    @endif
                                </div>

                                {{-- Cover Letter Expand --}}
                                @if ($app->cover_letter)
                                    <div x-show="expanded" x-transition
                                        class="border-white/8 mt-4 rounded-xl border bg-[var(--navy-900)] p-4"
                                        style="display:none">
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Cover
                                            Letter</p>
                                        <p class="text-sm leading-relaxed text-slate-400">{{ $app->cover_letter }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($applications->hasPages())
                <div class="mt-8">{{ $applications->links('vendor.pagination.custom') }}</div>
            @endif
        @else
            <div class="border-white/8 rounded-2xl border bg-[var(--navy-800)] py-24 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--navy-700)]">
                    <svg class="h-8 w-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                    </svg>
                </div>
                <p class="mb-1 font-semibold text-white">No applications yet</p>
                <p class="text-sm text-slate-500">Applications will appear here once candidates apply.</p>
            </div>
        @endif
    </div>
@endsection
