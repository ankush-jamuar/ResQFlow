<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" x-data="{ sidebarOpen: true }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ResQFlow') }} — Emergency Intelligence</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-900 selection:bg-red-100 selection:text-red-600 bg-[#f8fafc] overflow-x-hidden">
    <!-- Ambient Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-red-100/30 blur-[120px] rounded-full animate-float"></div>
        <div class="absolute top-[20%] -right-[5%] w-[30%] h-[30%] bg-blue-100/20 blur-[100px] rounded-full animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute inset-0 grid-pattern opacity-30"></div>
    </div>

    {{-- Admin Impersonation Banner --}}
    @if(session()->has('admin_impersonator_id'))
        <div class="fixed top-0 left-0 right-0 z-[200] bg-slate-950 text-white px-6 py-3 flex items-center justify-between border-b border-red-500/30 shadow-2xl backdrop-blur-md bg-opacity-90">
            <div class="flex items-center gap-4">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/20">
                    <div class="h-2 w-2 animate-ping rounded-full bg-red-500"></div>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tactical Oversight Active</p>
                    <p class="text-xs font-bold">Impersonating: <span class="text-red-500">{{ auth()->user()->name }}</span></p>
                </div>
            </div>
            <form action="{{ route('admin.return') }}" method="POST">
                @csrf
                <button type="submit" class="group flex items-center gap-2 rounded-full bg-red-500 px-6 py-2 text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:bg-red-600 hover:shadow-[0_0_20px_rgba(239,68,68,0.4)]">
                    Return to High Command
                    <svg class="h-3 w-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </form>
        </div>
    @endif

    @auth
        <x-sidebar />
        <x-topbar />
    @endauth

    <!-- Main Content Area -->
    <div 
        :class="sidebarOpen ? 'lg:pl-72' : 'lg:pl-24'" 
        class="min-h-screen transition-all duration-500 pt-32 pb-20"
    >
        <main class="max-w-7xl mx-auto px-6 lg:px-12">
            @isset($header)
                <div class="mb-12 reveal">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-1 bg-red-500 rounded-full"></div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Strategic Stream</p>
                    </div>
                    <h1 class="text-5xl font-[900] text-slate-950 tracking-tighter leading-none">
                        {{ $header }}
                    </h1>
                </div>
            @endisset

            @if(session('success') || session('error'))
                <div class="mb-8 reveal">
                    <div class="glass-panel !bg-slate-950/90 border-{{ session('success') ? 'emerald' : 'red' }}-500/30 p-6 rounded-2xl shadow-2xl flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-{{ session('success') ? 'emerald' : 'red' }}-500/20 rounded-xl flex items-center justify-center text-{{ session('success') ? 'emerald' : 'red' }}-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-{{ session('success') ? 'emerald' : 'red' }}-500 uppercase tracking-widest mb-1">{{ session('success') ? 'Success' : 'System Alert' }}</p>
                                <p class="text-xs font-bold text-white leading-relaxed">{{ session('success') ?? session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="reveal">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Global Toasts Container -->
    <div id="toast-container" class="fixed bottom-8 right-8 z-[100] flex flex-col gap-4"></div>

    @stack('scripts')
</body>
</html>
