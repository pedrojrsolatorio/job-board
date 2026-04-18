<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'JobBoard') }} — @yield('title', 'Find Your Next Career')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --navy-950: #060d1f;
            --navy-900: #0a1628;
            --navy-800: #0f2040;
            --navy-700: #162d58;
            --navy-600: #1e3d73;
            --gold:     #c9a84c;
            --gold-lt:  #e4c97a;
            --cream:    #f5f0e8;
            --slate:    #94a3b8;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--navy-950);
            color: #e2e8f0;
            min-height: 100vh;
        }

        .font-display { font-family: 'Playfair Display', serif; }
        .font-mono    { font-family: 'DM Mono', monospace; }

        /* Gold accent line on focused inputs */
        .input-gold:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(201,168,76,0.15);
        }

        /* Subtle grid texture on hero */
        .grid-texture {
            background-image:
                linear-gradient(rgba(201,168,76,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,168,76,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Gold shimmer on featured badge */
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position:  200% center; }
        }
        .badge-featured {
            background: linear-gradient(90deg, var(--gold) 0%, var(--gold-lt) 50%, var(--gold) 100%);
            background-size: 200% auto;
            animation: shimmer 3s linear infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Card hover lift */
        .card-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.4);
        }

        /* Gold underline on nav links */
        .nav-link {
            position: relative;
            padding-bottom: 2px;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 0; height: 1px;
            background: var(--gold);
            transition: width 0.25s ease;
        }
        .nav-link:hover::after { width: 100%; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--navy-900); }
        ::-webkit-scrollbar-thumb { background: var(--navy-700); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold); }

        /* Page fade in */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.5s ease both; }
        .fade-up-d1 { animation-delay: 0.1s; }
        .fade-up-d2 { animation-delay: 0.2s; }
        .fade-up-d3 { animation-delay: 0.3s; }
    </style>
</head>
<body>

    <!-- ===== NAVIGATION ===== -->
    <nav class="border-b border-white/5 bg-[#060d1f]/95 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-[var(--gold)] flex items-center justify-center">
                        <svg class="w-4 h-4 text-[var(--navy-950)]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"/>
                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                        </svg>
                    </div>
                    <span class="font-display font-semibold text-white text-lg tracking-wide">JobBoard<span class="text-[var(--gold)]">.</span></span>
                </a>

                <!-- Center nav links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('jobs.index') }}" class="nav-link text-sm text-slate-400 hover:text-white transition-colors">Browse Jobs</a>
                    <a href="#" class="nav-link text-sm text-slate-400 hover:text-white transition-colors">Companies</a>
                    <a href="#" class="nav-link text-sm text-slate-400 hover:text-white transition-colors">Salary Guide</a>
                </div>

                <!-- Auth buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Role-based dashboard link -->
                        @if(auth()->user()->isEmployer())
                            <a href="{{ route('my-jobs.index') }}" class="nav-link text-sm text-slate-400 hover:text-white transition-colors">My Jobs</a>
                        @elseif(auth()->user()->isJobSeeker())
                            <a href="{{ route('my-applications') }}" class="nav-link text-sm text-slate-400 hover:text-white transition-colors">My Applications</a>
                        @endif

                        <!-- User dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 bg-[var(--navy-800)] border border-white/10 rounded-lg px-3 py-2 text-sm text-white hover:border-[var(--gold)]/40 transition-colors">
                                <div class="w-6 h-6 rounded-full bg-[var(--navy-600)] flex items-center justify-center text-[var(--gold)] text-xs font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                                <svg class="w-3 h-3 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-48 bg-[var(--navy-800)] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-50"
                                 style="display:none">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profile
                                </a>
                                <div class="border-t border-white/5"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-slate-400 hover:bg-white/5 hover:text-red-400 transition-colors text-left">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition-colors nav-link">Sign In</a>
                        <a href="{{ route('register') }}"
                           class="text-sm font-medium px-4 py-2 rounded-lg bg-[var(--gold)] text-[var(--navy-950)] hover:bg-[var(--gold-lt)] transition-colors">
                            Get Started
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- ===== FLASH MESSAGES ===== -->
    @if(session('success') || session('error'))
        <div class="max-w-7xl mx-auto px-6 pt-4"
             x-data="{ show: true }" x-show="show" x-transition
             x-init="setTimeout(() => show = false, 5000)">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>
    @endif

    <!-- ===== MAIN CONTENT ===== -->
    <main>
        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="border-t border-white/5 mt-24 bg-[var(--navy-900)]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded bg-[var(--gold)] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[var(--navy-950)]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"/>
                                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                            </svg>
                        </div>
                        <span class="font-display font-semibold text-white text-lg">JobBoard<span class="text-[var(--gold)]">.</span></span>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs">
                        Connecting exceptional talent with world-class companies. Your next career move starts here.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-[var(--gold)] mb-4">For Job Seekers</h4>
                    <ul class="space-y-2">
                        @foreach(['Browse Jobs', 'Companies', 'Salary Guide', 'Career Advice'] as $link)
                            <li><a href="#" class="text-sm text-slate-500 hover:text-white transition-colors">{{ $link }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-[var(--gold)] mb-4">For Employers</h4>
                    <ul class="space-y-2">
                        @foreach(['Post a Job', 'Pricing', 'Employer Dashboard', 'Success Stories'] as $link)
                            <li><a href="#" class="text-sm text-slate-500 hover:text-white transition-colors">{{ $link }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-xs text-slate-600">© {{ date('Y') }} JobBoard. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    @foreach(['Privacy Policy', 'Terms of Service', 'Contact'] as $link)
                        <a href="#" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">{{ $link }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
