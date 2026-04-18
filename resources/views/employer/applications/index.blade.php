@extends('layouts.app')
@section('title', 'Applications — ' . $job->title)

@section('content')
<div class="max-w-6xl mx-auto px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8 flex-wrap gap-4">
        <div>
            <a href="{{ route('my-jobs.index') }}" class="flex items-center gap-1.5 text-xs text-slate-600 hover:text-slate-400 transition-colors mb-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
                Back to My Jobs
            </a>
            <p class="text-xs font-semibold uppercase tracking-widest text-[var(--gold)] mb-1">Applications</p>
            <h1 class="font-display text-2xl font-bold text-white">{{ $job->title }}</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $applications->total() }} total applicants</p>
        </div>

        {{-- Status filter tabs --}}
        <div class="flex items-center gap-1.5 bg-[var(--navy-800)] border border-white/8 rounded-xl p-1">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'interview' => 'Interview', 'hired' => 'Hired', 'rejected' => 'Rejected'] as $val => $lbl)
                <a href="{{ route('applications.for-job', array_merge(['job' => $job->slug], $val !== 'all' ? ['status' => $val] : [])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                          {{ (request('status', 'all') === $val) ? 'bg-[var(--gold)] text-[var(--navy-950)]' : 'text-slate-500 hover:text-white' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Applications List --}}
    @if($applications->count())
        <div class="space-y-3">
            @foreach($applications as $app)
                <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 transition-colors hover:border-white/15"
                     x-data="{ expanded: false }">
                    <div class="flex items-start gap-4">

                        {{-- Avatar --}}
                        <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-[var(--navy-700)] flex items-center justify-center">
                            <span class="text-base font-display font-bold text-[var(--gold)]">
                                {{ strtoupper(substr($app->user->name, 0, 1)) }}
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between flex-wrap gap-3">
                                <div>
                                    <p class="font-semibold text-white">{{ $app->user->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $app->user->email }}</p>
                                    <p class="text-xs text-slate-600 mt-0.5">Applied {{ $app->created_at->diffForHumans() }}</p>
                                </div>

                                {{-- Status & Actions --}}
                                <div class="flex items-center gap-3 flex-wrap">
                                    {{-- Current status badge --}}
                                    @php
                                        $statusMap = [
                                            'pending'   => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                            'reviewed'  => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            'interview' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                            'rejected'  => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            'hired'     => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs border font-medium {{ $statusMap[$app->status] ?? '' }}">
                                        {{ ucfirst($app->status) }}
                                    </span>

                                    {{-- Status updater --}}
                                    <form action="{{ route('applications.update-status', $app) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                                class="bg-[var(--navy-900)] border border-white/10 text-slate-400 text-xs rounded-lg px-2 py-1.5 input-gold focus:outline-none">
                                            <option value="">Update status...</option>
                                            @foreach(['pending' => 'Mark Pending', 'reviewed' => 'Mark Reviewed', 'interview' => 'Invite to Interview', 'rejected' => 'Reject', 'hired' => 'Hire 🎉'] as $val => $lbl)
                                                <option value="{{ $val }}" {{ $app->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    {{-- Download resume --}}
                                    <a href="{{ route('resumes.download', $app) }}"
                                       class="flex items-center gap-1.5 text-xs text-slate-500 hover:text-white border border-white/10 hover:border-white/25 rounded-lg px-3 py-1.5 transition-colors"
                                       title="Download Resume">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Resume
                                    </a>

                                    {{-- Expand cover letter --}}
                                    @if($app->cover_letter)
                                        <button @click="expanded = !expanded"
                                                class="flex items-center gap-1.5 text-xs text-slate-500 hover:text-white border border-white/10 hover:border-white/25 rounded-lg px-3 py-1.5 transition-colors">
                                            <svg class="w-3.5 h-3.5 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                                            <span x-text="expanded ? 'Hide' : 'Cover Letter'"></span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Cover Letter Expand --}}
                            @if($app->cover_letter)
                                <div x-show="expanded"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-4 bg-[var(--navy-900)] border border-white/8 rounded-xl p-4"
                                     style="display:none">
                                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Cover Letter</p>
                                    <p class="text-sm text-slate-400 leading-relaxed">{{ $app->cover_letter }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($applications->hasPages())
            <div class="mt-8">{{ $applications->links('vendor.pagination.custom') }}</div>
        @endif

    @else
        <div class="text-center py-24 bg-[var(--navy-800)] border border-white/8 rounded-2xl">
            <div class="w-16 h-16 bg-[var(--navy-700)] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <p class="text-white font-semibold mb-1">No applications yet</p>
            <p class="text-slate-500 text-sm">Applications will appear here once candidates apply.</p>
        </div>
    @endif
</div>
@endsection
