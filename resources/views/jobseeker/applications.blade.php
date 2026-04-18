@extends('layouts.app')
@section('title', 'My Applications')

@section('content')
<div class="max-w-5xl mx-auto px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-10">
        <p class="text-xs font-semibold uppercase tracking-widest text-[var(--gold)] mb-1">Job Seeker Dashboard</p>
        <h1 class="font-display text-3xl font-bold text-white">My Applications</h1>
        <p class="text-slate-500 text-sm mt-1">Track all your job applications in one place</p>
    </div>

    {{-- Stats --}}
    @php
        $all        = auth()->user()->applications()->with('jobListing')->get();
        $pending    = $all->where('status', 'pending')->count();
        $interview  = $all->where('status', 'interview')->count();
        $hired      = $all->where('status', 'hired')->count();
        $rejected   = $all->where('status', 'rejected')->count();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach([
            ['Total',      $all->count(),  'text-white',           'text-slate-600'],
            ['Pending',    $pending,       'text-yellow-400',      'text-yellow-900/30'],
            ['Interview',  $interview,     'text-purple-400',      'text-purple-900/30'],
            ['Hired',      $hired,         'text-emerald-400',     'text-emerald-900/30'],
        ] as [$label, $count, $valueColor, $bg])
            <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-5">
                <p class="text-xs text-slate-500 uppercase tracking-widest mb-2">{{ $label }}</p>
                <p class="font-display text-3xl font-bold {{ $valueColor }}">{{ $count }}</p>
            </div>
        @endforeach
    </div>

    {{-- Applications --}}
    @if($applications->count())
        <div class="space-y-4">
            @foreach($applications as $app)
                @php
                    $statusConfig = [
                        'pending'   => ['bg-yellow-500/10 border-yellow-500/20 text-yellow-400',    'Pending Review',        '⏳'],
                        'reviewed'  => ['bg-blue-500/10 border-blue-500/20 text-blue-400',          'Under Review',          '👀'],
                        'interview' => ['bg-purple-500/10 border-purple-500/20 text-purple-400',    'Interview Scheduled',   '🎯'],
                        'rejected'  => ['bg-red-500/10 border-red-500/20 text-red-400',             'Not Selected',          '✕'],
                        'hired'     => ['bg-emerald-500/10 border-emerald-500/20 text-emerald-400', 'Offer Extended!',       '🎉'],
                    ];
                    [$statusCls, $statusLabel, $statusEmoji] = $statusConfig[$app->status] ?? ['bg-slate-500/10 border-slate-500/20 text-slate-400', ucfirst($app->status), '·'];
                @endphp

                <div class="bg-[var(--navy-800)] border rounded-2xl overflow-hidden transition-colors
                            {{ $app->status === 'hired' ? 'border-emerald-500/30' : ($app->status === 'interview' ? 'border-purple-500/30' : 'border-white/8') }}">

                    {{-- Status banner for important statuses --}}
                    @if(in_array($app->status, ['hired', 'interview']))
                        <div class="px-6 py-2 text-xs font-semibold
                                    {{ $app->status === 'hired' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-purple-500/10 text-purple-400' }}">
                            {{ $statusEmoji }} {{ $statusLabel }}
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-start gap-4">

                            {{-- Company Logo --}}
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-[var(--navy-900)] border border-white/10 flex items-center justify-center overflow-hidden">
                                @if($app->jobListing->user->company_logo)
                                    <img src="{{ Storage::url($app->jobListing->user->company_logo) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-base font-display font-bold text-[var(--gold)]">
                                        {{ strtoupper(substr($app->jobListing->user->company_name ?? 'C', 0, 1)) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 flex-wrap">
                                    <div>
                                        <a href="{{ route('jobs.show', $app->jobListing) }}"
                                           class="font-semibold text-white hover:text-[var(--gold)] transition-colors">
                                            {{ $app->jobListing->title }}
                                        </a>
                                        <p class="text-sm text-slate-500 mt-0.5">{{ $app->jobListing->user->company_name ?? 'Company' }}</p>
                                    </div>

                                    {{-- Status badge --}}
                                    @if(!in_array($app->status, ['hired', 'interview']))
                                        <span class="px-2.5 py-1 rounded-full text-xs border font-medium {{ $statusCls }}">
                                            {{ $statusLabel }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta --}}
                                <div class="flex flex-wrap items-center gap-4 mt-3">
                                    <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $app->jobListing->location }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Applied {{ $app->created_at->format('M d, Y') }}
                                    </span>
                                    @php
                                        $typeColors = ['full-time' => 'bg-blue-500/10 text-blue-400 border-blue-500/20', 'part-time' => 'bg-purple-500/10 text-purple-400 border-purple-500/20', 'remote' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20', 'contract' => 'bg-orange-500/10 text-orange-400 border-orange-500/20'];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs border font-medium {{ $typeColors[$app->jobListing->job_type] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                        {{ ucfirst(str_replace('-', ' ', $app->jobListing->job_type)) }}
                                    </span>
                                </div>

                                {{-- Progress tracker --}}
                                @php
                                    $stages   = ['pending', 'reviewed', 'interview', 'hired'];
                                    $curIdx   = array_search($app->status, $stages);
                                    $isRej    = $app->status === 'rejected';
                                @endphp
                                @if(!$isRej)
                                    <div class="mt-4">
                                        <div class="flex items-center gap-0">
                                            @foreach($stages as $i => $stage)
                                                @php $done = $curIdx !== false && $i <= $curIdx; @endphp
                                                <div class="flex items-center {{ $i < count($stages)-1 ? 'flex-1' : '' }}">
                                                    <div class="flex flex-col items-center gap-1">
                                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs
                                                                    {{ $done ? 'bg-[var(--gold)] text-[var(--navy-950)]' : 'bg-[var(--navy-900)] border border-white/10 text-slate-600' }}">
                                                            @if($done)
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            @else
                                                                {{ $i + 1 }}
                                                            @endif
                                                        </div>
                                                        <span class="text-[10px] text-slate-600 whitespace-nowrap capitalize">{{ $stage }}</span>
                                                    </div>
                                                    @if($i < count($stages)-1)
                                                        <div class="flex-1 h-px mx-1 {{ $i < $curIdx ? 'bg-[var(--gold)]' : 'bg-white/8' }} mb-3.5"></div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3 flex items-center gap-2 text-xs text-red-400">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                        Unfortunately, you were not selected for this position. Keep applying!
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($applications->hasPages())
            <div class="mt-8">{{ $applications->links('vendor.pagination.custom') }}</div>
        @endif

    @else
        {{-- Empty state --}}
        <div class="text-center py-24 bg-[var(--navy-800)] border border-white/8 rounded-2xl">
            <div class="w-16 h-16 bg-[var(--navy-700)] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-white font-semibold mb-1">No applications yet</p>
            <p class="text-slate-500 text-sm mb-6">Start exploring open positions and apply to the ones that interest you.</p>
            <a href="{{ route('jobs.index') }}"
               class="inline-flex items-center gap-2 bg-[var(--gold)] text-[var(--navy-950)] font-semibold px-6 py-3 rounded-xl hover:bg-[var(--gold-lt)] transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Browse Open Jobs
            </a>
        </div>
    @endif

    {{-- Tips Card --}}
    <div class="mt-8 bg-gradient-to-r from-[var(--navy-800)] to-[var(--navy-700)] border border-[var(--gold)]/20 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-[var(--gold)]/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-[var(--gold)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-white mb-1">Pro tip for faster responses</p>
                <p class="text-sm text-slate-500">Tailoring your cover letter to each job description increases your response rate by up to 3x. Mention specific skills and how you'll contribute to the company's goals.</p>
            </div>
        </div>
    </div>
</div>
@endsection
