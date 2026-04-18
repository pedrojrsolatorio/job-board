@extends('layouts.app')
@section('title', 'Browse Jobs')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="relative overflow-hidden grid-texture py-24 border-b border-white/5">

    {{-- Radial glow --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[400px] bg-[var(--navy-700)]/20 rounded-full blur-3xl"></div>
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-[400px] h-[200px] bg-[var(--gold)]/5 rounded-full blur-2xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 text-center">

        {{-- Eyebrow --}}
        <div class="inline-flex items-center gap-2 border border-[var(--gold)]/30 bg-[var(--gold)]/5 rounded-full px-4 py-1.5 mb-6 fade-up">
            <span class="w-1.5 h-1.5 rounded-full bg-[var(--gold)] animate-pulse"></span>
            <span class="text-xs font-medium text-[var(--gold)] tracking-widest uppercase">{{ $jobs->total() }} Positions Available</span>
        </div>

        {{-- Headline --}}
        <h1 class="font-display text-5xl md:text-7xl font-bold text-white leading-[1.1] mb-6 fade-up fade-up-d1">
            Find Your Next<br>
            <span class="text-[var(--gold)]">Executive Role</span>
        </h1>

        <p class="text-slate-400 text-lg max-w-xl mx-auto mb-10 fade-up fade-up-d2">
            Curated opportunities at world-class companies. Apply in minutes, not hours.
        </p>

        {{-- ===== SEARCH BAR ===== --}}
        <form method="GET" action="{{ route('jobs.index') }}"
              class="fade-up fade-up-d3 max-w-3xl mx-auto">
            <div class="flex flex-col md:flex-row gap-3 bg-[var(--navy-800)] border border-white/10 rounded-2xl p-2 shadow-2xl">

                {{-- Keyword --}}
                <div class="flex-1 flex items-center gap-3 bg-[var(--navy-900)] rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Title, skill, or keyword..."
                           class="bg-transparent flex-1 text-sm text-white placeholder-slate-600 focus:outline-none input-gold">
                </div>

                {{-- Location --}}
                <div class="flex items-center gap-3 bg-[var(--navy-900)] rounded-xl px-4 py-3 md:w-48">
                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <input type="text" name="location" value="{{ request('location') }}"
                           placeholder="Location..."
                           class="bg-transparent flex-1 text-sm text-white placeholder-slate-600 focus:outline-none input-gold min-w-0">
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="bg-[var(--gold)] text-[var(--navy-950)] font-semibold text-sm px-6 py-3 rounded-xl hover:bg-[var(--gold-lt)] transition-colors whitespace-nowrap">
                    Search Jobs
                </button>
            </div>

            {{-- Quick filters --}}
            <div class="flex flex-wrap justify-center gap-2 mt-4">
                @foreach(['Remote', 'Full Time', 'Contract', 'Part Time'] as $type)
                    @php $val = strtolower(str_replace(' ', '-', $type)); @endphp
                    <a href="{{ route('jobs.index', array_merge(request()->query(), ['type' => $val])) }}"
                       class="px-3 py-1 rounded-full text-xs border transition-colors
                              {{ request('type') === $val
                                 ? 'bg-[var(--gold)]/15 border-[var(--gold)]/50 text-[var(--gold)]'
                                 : 'border-white/10 text-slate-500 hover:border-white/25 hover:text-slate-300' }}">
                        {{ $type }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>
</section>

{{-- ===== MAIN CONTENT ===== --}}
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="lg:col-span-1">
            <form method="GET" action="{{ route('jobs.index') }}">
                @if(request('q'))   <input type="hidden" name="q"   value="{{ request('q') }}">   @endif
                @if(request('location')) <input type="hidden" name="location" value="{{ request('location') }}"> @endif

                {{-- Filter Card --}}
                <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-6 sticky top-20">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-semibold text-white">Filters</h3>
                        @if(request()->hasAny(['category', 'type']))
                            <a href="{{ route('jobs.index', ['q' => request('q'), 'location' => request('location')]) }}"
                               class="text-xs text-[var(--gold)] hover:text-[var(--gold-lt)] transition-colors">Clear all</a>
                        @endif
                    </div>

                    {{-- Category --}}
                    <div class="mb-6">
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-3">Category</h4>
                        <div class="space-y-1">
                            @foreach($categories as $cat)
                                <label class="flex items-center justify-between cursor-pointer group py-1.5 px-2 rounded-lg hover:bg-white/5 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="category" value="{{ $cat->id }}"
                                               {{ request('category') == $cat->id ? 'checked' : '' }}
                                               class="w-3.5 h-3.5 accent-[var(--gold)]">
                                        <span class="text-sm text-slate-400 group-hover:text-white transition-colors">{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-xs text-slate-600 font-mono">{{ $cat->job_listings_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Job Type --}}
                    <div class="mb-6">
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-3">Job Type</h4>
                        <div class="space-y-1">
                            @foreach(['full-time' => 'Full Time', 'part-time' => 'Part Time', 'remote' => 'Remote', 'contract' => 'Contract', 'internship' => 'Internship'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer group py-1.5 px-2 rounded-lg hover:bg-white/5 transition-colors">
                                    <input type="radio" name="type" value="{{ $val }}"
                                           {{ request('type') === $val ? 'checked' : '' }}
                                           class="w-3.5 h-3.5 accent-[var(--gold)]">
                                    <span class="text-sm text-slate-400 group-hover:text-white transition-colors">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-[var(--navy-700)] hover:bg-[var(--navy-600)] border border-white/10 text-white text-sm font-medium py-2.5 rounded-xl transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </aside>

        {{-- ===== JOB LISTINGS ===== --}}
        <div class="lg:col-span-3">

            {{-- Results header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-white font-semibold">
                        {{ $jobs->total() }} <span class="text-slate-500 font-normal">positions found</span>
                    </h2>
                    @if(request('q'))
                        <p class="text-xs text-slate-600 mt-0.5">Results for "<span class="text-[var(--gold)]">{{ request('q') }}</span>"</p>
                    @endif
                </div>
                <select class="bg-[var(--navy-800)] border border-white/10 text-slate-400 text-xs rounded-lg px-3 py-2 focus:outline-none input-gold">
                    <option>Most Recent</option>
                    <option>Most Relevant</option>
                    <option>Highest Salary</option>
                </select>
            </div>

            {{-- Featured Jobs --}}
            @php $featured = $jobs->filter(fn($j) => $j->is_featured); @endphp
            @if($featured->count())
                <div class="mb-2">
                    <span class="text-xs font-semibold uppercase tracking-widest text-[var(--gold)]">⭐ Featured</span>
                </div>
            @endif

            {{-- Job Cards --}}
            <div class="space-y-3">
                @forelse($jobs as $job)
                    <a href="{{ route('jobs.show', $job) }}"
                       class="card-lift block bg-[var(--navy-800)] border rounded-2xl p-6 transition-colors
                              {{ $job->is_featured
                                 ? 'border-[var(--gold)]/30 bg-gradient-to-r from-[var(--navy-800)] to-[var(--navy-700)]'
                                 : 'border-white/8 hover:border-white/15' }}">

                        <div class="flex items-start gap-4">

                            {{-- Company Logo --}}
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-[var(--navy-900)] border border-white/10 flex items-center justify-center overflow-hidden">
                                @if($job->user->company_logo)
                                    <img src="{{ Storage::url($job->user->company_logo) }}" alt="{{ $job->user->company_name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-lg font-display font-bold text-[var(--gold)]">
                                        {{ strtoupper(substr($job->user->company_name ?? 'C', 0, 1)) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Job Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 flex-wrap">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <h3 class="font-semibold text-white text-base leading-snug">{{ $job->title }}</h3>
                                            @if($job->is_featured)
                                                <span class="font-mono text-[10px] font-semibold uppercase tracking-wider badge-featured">Featured</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-500">{{ $job->user->company_name ?? 'Company' }}</p>
                                    </div>

                                    {{-- Salary --}}
                                    @if($job->salary_min || $job->salary_max)
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-white font-mono">
                                                @if($job->salary_min && $job->salary_max)
                                                    ${{ number_format($job->salary_min/1000) }}k – ${{ number_format($job->salary_max/1000) }}k
                                                @elseif($job->salary_min)
                                                    From ${{ number_format($job->salary_min/1000) }}k
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-600">per year</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Meta row --}}
                                <div class="flex items-center gap-4 mt-3 flex-wrap">
                                    <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $job->location }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $job->created_at->diffForHumans() }}
                                    </span>

                                    {{-- Job type badge --}}
                                    @php
                                        $typeColors = [
                                            'full-time'  => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            'part-time'  => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                            'remote'     => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            'contract'   => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                            'internship' => 'bg-pink-500/10 text-pink-400 border-pink-500/20',
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs border font-medium {{ $typeColors[$job->job_type] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                        {{ ucfirst(str_replace('-', ' ', $job->job_type)) }}
                                    </span>

                                    {{-- Category --}}
                                    <span class="text-xs text-slate-600">{{ $job->category->name }}</span>
                                </div>

                                {{-- Tags --}}
                                @if($job->tags->count())
                                    <div class="flex flex-wrap gap-1.5 mt-3">
                                        @foreach($job->tags->take(4) as $tag)
                                            <span class="px-2 py-0.5 rounded-md bg-[var(--navy-900)] border border-white/8 text-xs text-slate-500">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-24 bg-[var(--navy-800)] border border-white/8 rounded-2xl">
                        <div class="w-16 h-16 bg-[var(--navy-700)] rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <p class="text-white font-semibold mb-1">No jobs found</p>
                        <p class="text-slate-500 text-sm">Try adjusting your search or filters</p>
                        <a href="{{ route('jobs.index') }}" class="inline-block mt-4 text-sm text-[var(--gold)] hover:text-[var(--gold-lt)] transition-colors">Clear all filters →</a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($jobs->hasPages())
                <div class="mt-8">
                    {{ $jobs->withQueryString()->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== STATS SECTION ===== --}}
<section class="border-t border-white/5 mt-12 py-16 bg-[var(--navy-900)]">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach([
                ['12,000+', 'Active Jobs'],
                ['3,500+', 'Companies'],
                ['94%',    'Placement Rate'],
                ['48hrs',  'Avg. Response Time'],
            ] as [$num, $label])
                <div>
                    <p class="font-display text-3xl font-bold text-white mb-1">{{ $num }}</p>
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
