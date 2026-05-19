@props(['active' => 'dashboard'])

@php
    $role = auth()->user()->role ?? 'user';
    $links = [
        'admin' => [
            ['name' => 'Overview', 'route' => 'admin.dashboard', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['name' => 'Hospitals', 'route' => 'admin.hospitals', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['name' => 'Ambulances', 'route' => 'admin.ambulances', 'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
            ['name' => 'Emergencies', 'route' => 'admin.emergencies', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            ['name' => 'Analytics', 'route' => 'admin.analytics', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
            ['name' => 'Settings', 'route' => 'admin.settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ],
        'hospital' => [
            ['name' => 'Overview', 'route' => 'hospital.dashboard', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['name' => 'Ambulance Fleet', 'route' => 'hospital.fleet', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['name' => 'Rescue Queue', 'route' => 'hospital.queue', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['name' => 'Resources', 'route' => 'hospital.resources', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
        ],
        'user' => [
            ['name' => 'Life Support', 'route' => 'dashboard', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['name' => 'Mission History', 'route' => 'user.history', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['name' => 'Medical Profile', 'route' => 'user.profile', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ]
    ];

    $roleLinks = $links[$role] ?? $links['user'];
@endphp

<aside 
    id="sidebar"
    :class="sidebarOpen ? 'w-72' : 'w-24'"
    class="fixed left-0 top-0 h-screen bg-slate-950 transition-all duration-500 z-[60] overflow-hidden flex flex-col border-r border-white/5"
>
    <!-- Background Accents -->
    <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/10 blur-3xl -z-10 animate-pulse-soft"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-500/5 blur-3xl -z-10"></div>

    <!-- Header / Brand -->
    <div class="h-24 flex items-center px-7 mb-4">
        <a href="/" class="flex items-center gap-4 group">
            <div class="w-12 h-12 bg-red-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-red-500/40 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
            </div>
            <div x-show="sidebarOpen" x-transition.opacity class="flex flex-col">
                <span class="text-xl font-black text-white tracking-tighter uppercase leading-none">ResQ<span class="text-red-500">Flow</span></span>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mt-1">Life-Response OS</span>
            </div>
        </a>
    </div>

    <!-- Navigation Streams -->
    <div class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar pt-4">
        <div x-show="sidebarOpen" class="text-slate-600 text-[9px] font-black uppercase tracking-[0.4em] px-5 mb-4">Command Streams</div>
        
        @foreach($roleLinks as $link)
            <a 
                href="{{ route($link['route']) }}" 
                class="flex items-center gap-5 px-5 py-3.5 rounded-[1.5rem] transition-all duration-300 group relative {{ request()->routeIs($link['route']) ? 'bg-white text-slate-950 shadow-xl' : 'text-slate-500 hover:text-white hover:bg-white/5' }}"
            >
                @if(request()->routeIs($link['route']))
                    <div class="absolute left-0 w-1 h-5 bg-red-500 rounded-r-full"></div>
                @endif
                
                <div class="{{ request()->routeIs($link['route']) ? 'text-red-500' : 'text-slate-600 group-hover:text-red-400' }} transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                    </svg>
                </div>
                <span x-show="sidebarOpen" x-transition.opacity class="font-black text-[10px] uppercase tracking-[0.15em] whitespace-nowrap">{{ $link['name'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Operative Status -->
    <div class="p-4">
        <div class="bg-white/5 rounded-[2rem] p-5 border border-white/5 relative overflow-hidden group">
            <div class="flex items-center gap-4">
                <div class="w-9 h-9 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-center text-red-500 font-black text-xs">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div x-show="sidebarOpen" x-transition.opacity class="flex-1 min-w-0">
                    <p class="text-[10px] font-black text-white truncate uppercase tracking-tight">{{ auth()->user()->name }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <p class="text-[8px] text-slate-600 uppercase font-black tracking-widest">Active</p>
                    </div>
                </div>
            </div>
            
            <form x-show="sidebarOpen" method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button class="w-full flex items-center justify-center gap-2.5 px-4 py-2.5 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-all duration-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="text-[9px] font-black uppercase tracking-widest">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
