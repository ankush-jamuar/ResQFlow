<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ResQFlow') }} — Access Command Center</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-900 bg-[#f8fafc] overflow-hidden">
    <div class="min-h-screen flex relative">
        <!-- Ambient Background -->
        <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-red-100/30 blur-[120px] rounded-full animate-float"></div>
            <div class="absolute top-[30%] -right-[5%] w-[40%] h-[40%] bg-blue-100/20 blur-[100px] rounded-full animate-float" style="animation-delay: -2s;"></div>
            <div class="absolute inset-0 grid-pattern opacity-10"></div>
        </div>

        <!-- Left Panel: Cinematic Visual -->
        <div class="hidden lg:flex lg:w-[45%] relative overflow-hidden bg-slate-950 p-16 flex-col justify-between">
            <!-- Background Visuals -->
            <div class="absolute inset-0 opacity-40">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[150%] bg-[radial-gradient(circle_at_center,rgba(239,68,68,0.15)_0%,transparent_70%)] animate-pulse-soft"></div>
                <div class="absolute inset-0 grid-pattern opacity-20"></div>
            </div>

            <div class="relative z-10">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-red-500 rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/40">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                    </div>
                    <span class="font-black text-2xl text-white tracking-tighter uppercase">ResQ<span class="text-red-500">Flow</span></span>
                </a>
            </div>

            <div class="relative z-10 max-w-lg">
                <h2 class="text-6xl font-black text-white leading-[1.1] tracking-tighter mb-8">
                    Empowering <br> <span class="text-gradient-red">Life-Savers.</span>
                </h2>
                <p class="text-slate-400 text-lg font-medium leading-relaxed mb-12">
                    Enter the command center. Manage real-time emergencies with sub-second latency and military-grade precision.
                </p>
                
                <div class="flex items-center gap-4 bg-white/5 border border-white/10 p-6 rounded-[2rem] backdrop-blur-md">
                    <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Response Optimized</p>
                        <p class="text-slate-500 text-xs font-medium">System latency: <span class="text-emerald-400">14ms</span></p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.3em]">Operational Readiness Level: HIGH</span>
            </div>
        </div>

        <!-- Right Panel: Auth Forms -->
        <div class="flex-1 flex flex-col justify-center items-center p-8 md:p-16 relative">
            <div class="w-full max-w-md reveal">
                <div class="lg:hidden flex items-center gap-3 mb-12">
                    <div class="w-8 h-8 bg-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" /></svg>
                    </div>
                    <span class="font-black text-xl text-slate-950 tracking-tighter uppercase">ResQFlow</span>
                </div>

                <div class="glass-panel p-10 rounded-[3rem] shadow-2xl relative border-white/40 overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-100/20 blur-3xl -z-10"></div>
                    {{ $slot }}
                </div>

                <p class="mt-12 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Secure Command Center Access — End-to-End Encrypted
                </p>
            </div>
        </div>
    </div>
</body>
</html>
