@extends('layouts.app')
@section('title', 'Employer Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-10">

    {{-- ===== HEADER ===== --}}
    <div class="flex items-start justify-between mb-10 flex-wrap gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-[var(--gold)] mb-1">Employer Dashboard</p>
            <h1 class="font-display text-3xl font-bold text-white">
                {{ auth()->user()->company_name ?? auth()->user()->name }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">Manage your job postings and review applications</p>
        </div>
        <a href="{{ route('my-jobs.create') }}"
           class="flex items-center gap-2 bg-[var(--gold)] text-[var(--navy-950)] font-semibold px-5 py-3 rounded-xl hover:bg-[var(--gold-lt)] transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
            Post New Job
        </a>
    </div>

    {{-- ===== STATS ROW ===== --}}
    @php
        $totalJobs      = auth()->user()->jobListings()->count();
        $activeJobs     = auth()->user()->jobListings()->where('status', 'active')->count();
        $totalApps      = \App\Models\JobApplication::whereHas('jobListing', fn($q) => $q->where('user_id', auth()->id()))->count();
        $pendingApps    = \App\Models\JobApplication::whereHas('jobListing', fn($q) => $q->where('user_id', auth()->id()))->where('status', 'pending')->count();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach([
            ['Total Jobs',        $totalJobs,   'text-white',            'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['Active Jobs',       $activeJobs,  'text-emerald-400',      'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Total Applicants',  $totalApps,   'text-blue-400',         'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
            ['Pending Review',    $pendingApps, 'text-[var(--gold)]',    'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as [$label, $value, $valueClass, $icon])
            <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-slate-500 uppercase tracking-widest">{{ $label }}</p>
                    <div class="w-8 h-8 rounded-lg bg-[var(--navy-900)] flex items-center justify-center">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="{{ $icon }}"/></svg>
                    </div>
                </div>
                <p class="font-display text-3xl font-bold {{ $valueClass }}">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===== JOB LISTINGS TABLE ===== --}}
    <div class="bg-[var(--navy-800)] border border-white/8 rounded-2xl overflow-hidden">

        {{-- Table header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
            <h2 class="font-semibold text-white">My Job Postings</h2>
            <span class="text-xs text-slate-500 font-mono">{{ $jobs->total() }} total</span>
        </div>

        @if($jobs->count())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Job Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500 hidden md:table-cell">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500 hidden lg:table-cell">Applications</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500 hidden lg:table-cell">Posted</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500 hidden xl:table-cell">Featured</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($jobs as $job)
                            <tr class="hover:bg-white/2 transition-colors group">
                                {{-- Title --}}
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-white group-hover:text-[var(--gold)] transition-colors">{{ $job->title }}</p>
                                        <p class="text-xs text-slate-600 mt-0.5">{{ $job->location }} · {{ ucfirst(str_replace('-', ' ', $job->job_type)) }}</p>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 hidden md:table-cell">
                                    @php
                                        $statusMap = [
                                            'active' => ['bg-emerald-500/10 text-emerald-400 border-emerald-500/20', 'Active'],
                                            'draft'  => ['bg-slate-500/10 text-slate-400 border-slate-500/20', 'Draft'],
                                            'closed' => ['bg-red-500/10 text-red-400 border-red-500/20', 'Closed'],
                                        ];
                                        [$cls, $lbl] = $statusMap[$job->status] ?? ['bg-slate-500/10 text-slate-400 border-slate-500/20', $job->status];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs border font-medium {{ $cls }}">{{ $lbl }}</span>
                                </td>

                                {{-- Applications count --}}
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    <a href="{{ route('applications.for-job', $job) }}"
                                       class="flex items-center gap-2 text-sm text-slate-400 hover:text-[var(--gold)] transition-colors w-fit">
                                        <span class="font-mono font-semibold text-white">{{ $job->applications_count }}</span>
                                        <span class="text-slate-600">candidates</span>
                                        @if($job->applications->where('status', 'pending')->count())
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--gold)] animate-pulse"></span>
                                        @endif
                                    </a>
                                </td>

                                {{-- Posted date --}}
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    <p class="text-xs text-slate-500">{{ $job->created_at->format('M d, Y') }}</p>
                                    @if($job->expires_at)
                                        <p class="text-xs text-slate-600 mt-0.5">Expires {{ $job->expires_at->format('M d') }}</p>
                                    @endif
                                </td>

                                {{-- Featured --}}
                                <td class="px-6 py-4 hidden xl:table-cell">
                                    @if($job->is_featured && $job->featured_until && $job->featured_until->isFuture())
                                        <div>
                                            <span class="font-mono text-[10px] font-semibold uppercase tracking-wider badge-featured">⭐ Featured</span>
                                            <p class="text-xs text-slate-600 mt-0.5">Until {{ $job->featured_until->format('M d') }}</p>
                                        </div>
                                    @else
                                        <form action="{{ route('payments.promote', $job) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-[var(--gold)] hover:text-[var(--gold-lt)] transition-colors underline underline-offset-2">
                                                Promote — $29.99
                                            </button>
                                        </form>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('jobs.show', $job) }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-[var(--navy-900)] text-slate-400 hover:text-white transition-colors" title="View">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('my-jobs.edit', $job) }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-[var(--navy-900)] text-slate-400 hover:text-white transition-colors" title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('my-jobs.destroy', $job) }}" method="POST"
                                              onsubmit="return confirm('Delete this job listing?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-[var(--navy-900)] text-slate-400 hover:text-red-400 transition-colors" title="Delete">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($jobs->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $jobs->links('vendor.pagination.custom') }}
                </div>
            @endif

        @else
            {{-- Empty state --}}
            <div class="text-center py-20">
                <div class="w-16 h-16 bg-[var(--navy-700)] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-white font-semibold mb-1">No job postings yet</p>
                <p class="text-slate-500 text-sm mb-6">Start attracting top talent by posting your first job.</p>
                <a href="{{ route('my-jobs.create') }}"
                   class="inline-flex items-center gap-2 bg-[var(--gold)] text-[var(--navy-950)] font-semibold px-6 py-3 rounded-xl hover:bg-[var(--gold-lt)] transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                    Post Your First Job
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
