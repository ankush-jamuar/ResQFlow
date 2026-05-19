<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ResQFlow — Premium Emergency Intelligence</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        #tracking-map { min-height: 450px; height: 100%; width: 100%; z-index: 1; }
        .assigned-hospital-marker { animation: hospitalPulse 2s infinite; }
        @keyframes hospitalPulse {
            0% { filter: drop-shadow(0 0 2px rgba(16,185,129,0.5)); transform: scale(1); }
            50% { filter: drop-shadow(0 0 15px rgba(16,185,129,0.8)); transform: scale(1.1); }
            100% { filter: drop-shadow(0 0 2px rgba(16,185,129,0.5)); transform: scale(1); }
        }
        .user-marker { filter: drop-shadow(0 0 10px rgba(239,68,68,0.6)); }
        .ambulance-marker { animation: ambulancePulse 1s infinite; }
        @keyframes ambulancePulse {
            0% { filter: drop-shadow(0 0 2px rgba(245,158,11,0.5)); }
            50% { filter: drop-shadow(0 0 12px rgba(245,158,11,0.9)); }
            100% { filter: drop-shadow(0 0 2px rgba(245,158,11,0.5)); }
        }
        .leaflet-container { background: #f8fafc !important; border-radius: 2rem !important; }
        .leaflet-bar { border: none !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
        .leaflet-bar a { border-radius: 12px !important; margin-bottom: 4px; }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 bg-[#f8fafc] overflow-x-hidden">
    <!-- Ambient Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-red-100/30 blur-[120px] rounded-full animate-float"></div>
        <div class="absolute top-[30%] -right-[5%] w-[40%] h-[40%] bg-blue-100/20 blur-[100px] rounded-full animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute inset-0 grid-pattern opacity-20"></div>
    </div>

    <!-- Premium Navigation -->
    <nav class="sticky top-4 z-50 mx-auto w-[95%] max-w-7xl">
        <div class="glass-panel rounded-3xl px-6 py-3 flex justify-between items-center h-14">
            <a href="/" class="flex items-center gap-3 group">
                <div class="relative">
                    <div class="absolute inset-0 bg-red-500 blur-md opacity-20 group-hover:opacity-40 transition-opacity"></div>
                    <svg class="w-8 h-8 text-red-500 relative" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                </div>
                <span class="font-extrabold text-xl tracking-tight">ResQ<span class="text-red-500">Flow</span></span>
            </a>

            @if (Route::has('login'))
                <div class="flex items-center gap-6">
                    @auth
                        @php
                            $dashboardRoute = 'dashboard';
                            if(auth()->user()->role === 'admin') $dashboardRoute = 'admin.dashboard';
                            elseif(auth()->user()->role === 'hospital') $dashboardRoute = 'hospital.dashboard';
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="text-sm font-bold text-slate-600 hover:text-slate-950 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 hover:text-slate-950 transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-premium !py-2 !px-6 text-sm shadow-xl shadow-red-100">Join Network</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6" x-data="trackingState">
        <!-- Hero Section -->
        <section id="hero-section" class="py-24 flex flex-col items-center text-center">
            <div class="reveal inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 border border-red-100 mb-8">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
                <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest">Active Emergency Intelligence</span>
            </div>

            <h1 class="reveal text-6xl md:text-8xl font-[900] text-slate-950 tracking-tighter leading-[0.9] mb-8 max-w-4xl">
                When seconds matter, <br><span class="text-gradient-red">we flow faster.</span>
            </h1>

            <p class="reveal text-xl text-slate-500 max-w-2xl mb-14 leading-relaxed font-medium">
                The world's most advanced real-time emergency dispatch system. AI-powered hospital routing and instant ambulance tracking at your fingertips.
            </p>

            <!-- SOS Area -->
            <div id="sos-container" class="reveal relative mb-20 group flex flex-col items-center">
                <!-- Ambient Glow (Centered & Non-blocking) -->
                <div class="absolute inset-0 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-red-500 blur-[100px] opacity-10 group-hover:opacity-25 transition-opacity duration-700 pointer-events-none w-full max-w-lg aspect-square rounded-full"></div>
                
                <div class="relative z-10">
                    <button id="sosButton" class="w-48 h-48 rounded-full bg-red-500 shadow-[0_0_60px_rgba(239,68,68,0.4)] flex flex-col items-center justify-center group-hover:scale-110 transition-transform duration-700 active:scale-95 cursor-pointer">
                        <svg class="w-16 h-16 text-white mb-2 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21l-8.228-3.69a2 2 0 01-1.172-1.815V7.127a2 2 0 011.172-1.815L12 1.622l8.228 3.69a2 2 0 011.172 1.815v8.368a2 2 0 01-1.172 1.815L12 21z" />
                        </svg>
                        <span class="text-2xl font-black text-white tracking-tighter">SOS</span>
                    </button>
                    <!-- Pulse Rings (Non-blocking) -->
                    <div class="absolute inset-0 rounded-full border-4 border-red-500/20 animate-ping pointer-events-none" style="animation-duration: 3s;"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-red-500/10 animate-ping pointer-events-none" style="animation-duration: 3s; animation-delay: 1s;"></div>
                </div>
                <p class="mt-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] relative z-10">Tap for immediate dispatch</p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl mt-12">
                <div class="reveal glass-card rounded-[2.5rem] p-10 text-left">
                    <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 mb-6 border border-red-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-950 mb-3">Ultra-Fast Dispatch</h3>
                    <p class="text-slate-500 leading-relaxed font-medium">Proprietary algorithms assign the closest ambulance in under 12 seconds.</p>
                </div>
                <div class="reveal glass-card rounded-[2.5rem] p-10 text-left" style="transition-delay: 100ms;">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 mb-6 border border-blue-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-950 mb-3">Military-Grade GPS</h3>
                    <p class="text-slate-500 leading-relaxed font-medium">Real-time precision tracking of emergency units with sub-meter accuracy.</p>
                </div>
                <div class="reveal glass-card rounded-[2.5rem] p-10 text-left" style="transition-delay: 200ms;">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 mb-6 border border-emerald-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-950 mb-3">Neural Routing</h3>
                    <p class="text-slate-500 leading-relaxed font-medium">Dynamic selection based on traffic, hospital capacity, and specialty care.</p>
                </div>
            </div>
        </section>

        <!-- Tracking Section (Initially Hidden) -->
        <section id="tracking-section" class="hidden py-12 w-full">
            <div class="glass-panel rounded-[3rem] p-12 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-red-100/20 blur-3xl -z-10"></div>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                    <div>
                        <h2 class="text-4xl font-black text-slate-950 tracking-tight mb-2">Emergency Dashboard</h2>
                        <p class="text-slate-500 font-medium">Live status from central command center</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div id="live-status-badge" class="px-5 py-2 bg-amber-50 text-amber-600 text-xs font-black rounded-full uppercase tracking-widest border border-amber-100 animate-pulse-soft" x-text="status.toUpperCase()">Pending</div>
                        <button @click="cancelEmergency()" id="cancel-emergency-btn" class="px-5 py-2 bg-red-500 text-white text-xs font-black rounded-full hover:bg-red-600 transition-all shadow-lg shadow-red-100" x-show="status === 'pending'">ABORT MISSION</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="glass-card !bg-white/60 p-6 rounded-3xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Target Facility</p>
                        <p class="text-lg font-extrabold text-slate-950" x-text="hospital">Locating Nearest...</p>
                    </div>
                    <div class="glass-card !bg-white/60 p-6 rounded-3xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Assigned Unit</p>
                        <p class="text-lg font-extrabold text-slate-950" x-text="ambulance">Awaiting Dispatch</p>
                    </div>
                    <div class="glass-card !bg-slate-900 p-6 rounded-3xl text-white">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Estimated Arrival</p>
                        <p class="text-2xl font-black text-red-500" x-text="eta">CALCULATING...</p>
                    </div>
                </div>

                <!-- Responder Medical Access Pulse -->
                <div class="mb-8 p-4 bg-blue-50/50 border border-blue-100 rounded-2xl flex items-center justify-between gap-4" x-show="['dispatched', 'en_route', 'arrived'].includes(currentStatus)">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-500 blur-sm opacity-20 animate-pulse"></div>
                            <svg class="w-6 h-6 text-blue-600 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-950 uppercase tracking-tight">Responder Intelligence Active</p>
                            <p class="text-[9px] font-bold text-blue-600 uppercase">Rescue team has received your critical medical profile & allergies.</p>
                        </div>
                    </div>
                    @auth
                    <a href="{{ route('user.medical') }}" class="px-4 py-2 bg-white text-blue-600 text-[8px] font-black uppercase rounded-lg border border-blue-100 shadow-sm hover:bg-blue-50 transition-all">Update History</a>
                    @endauth
                </div>

                <div class="relative overflow-hidden rounded-3xl mb-10 shadow-inner border border-white/40">
                    <div id="tracking-map" class="w-full aspect-video z-0"></div>
                    
                    <!-- Demo Mode Badge -->
                    <div class="absolute top-6 left-6 px-4 py-2 bg-slate-950/80 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-[0.2em] rounded-xl z-10 border border-white/10 flex items-center gap-2">
                        <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                        TELEMETRY SIMULATION: ACTIVE
                    </div>

                    <div class="absolute bottom-6 right-6 px-6 py-3 bg-slate-950/80 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest rounded-2xl z-10 border border-white/10">
                        Operational Grid: Sector 01
                    </div>
                </div>

                <div class="max-w-4xl mx-auto">
                    <x-status-timeline :status="'pending'" id="tracking-timeline" />
                </div>

                <div class="mt-12 text-center">
                    <button @click="clearSession()" class="text-xs font-bold text-slate-400 hover:text-red-500 transition-colors uppercase tracking-widest">Clear Session & Reset</button>
                </div>
            </div>
        </section>

        <!-- Summary Section (Initially Hidden) -->
        <section id="summary-section" class="hidden py-12 w-full">
            <div class="glass-panel rounded-[3rem] p-16 shadow-2xl text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-emerald-50/30 -z-10"></div>
                <div class="w-24 h-24 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-emerald-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-5xl font-black text-slate-950 mb-4 tracking-tighter">Mission Successful</h2>
                <p class="text-xl text-slate-500 mb-12 font-medium">Assistance delivered. Your safety is our priority.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12 max-w-3xl mx-auto">
                    <div class="glass-card p-8 rounded-3xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Time</p>
                        <p id="summary-time" class="text-3xl font-black text-slate-950">-- min</p>
                    </div>
                    <div class="glass-card p-8 rounded-3xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Hospital</p>
                        <p id="summary-hospital" class="text-xl font-bold text-slate-950">--</p>
                    </div>
                    <div class="glass-card p-8 rounded-3xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Unit</p>
                        <p id="summary-ambulance" class="text-xl font-bold text-slate-950">--</p>
                    </div>
                </div>
                
                <button @click="resetToInitial()" class="btn-premium !px-12 !py-4 text-lg !rounded-2xl shadow-2xl shadow-red-100">Return to HQ</button>
            </div>
        </section>
        </section>

    <!-- Footer -->
    <footer class="py-20 border-t border-slate-100 mt-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" /></svg>
                <span class="font-black text-slate-950 uppercase tracking-widest">ResQFlow</span>
            </div>
            <div class="flex gap-8 text-sm font-bold text-slate-400 uppercase tracking-widest">
                <a href="#" class="hover:text-red-500 transition-colors">Privacy</a>
                <a href="#" class="hover:text-red-500 transition-colors">Terms</a>
                <a href="#" class="hover:text-red-500 transition-colors">Infrastructure</a>
            </div>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">© 2026 ResQFlow Tech.</p>
        </div>
    </footer>

    <!-- SOS Modal -->
    <div id="sos-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-md">
        <div class="glass-panel w-full max-w-xl rounded-[3rem] p-10 shadow-2xl animate-float" style="animation-duration: 2s;">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-500">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-950 tracking-tight">Confirm Emergency</h3>
                    <p class="text-slate-500 font-medium">Unit dispatch will be instantaneous.</p>
                </div>
            </div>
            
            <div class="space-y-6 mb-10">
                @auth
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">For Whom?</label>
                        <select id="family-member-id" class="glass-input w-full font-bold text-slate-950">
                            <option value="">Myself</option>
                            @foreach($familyMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->relationship }})</option>
                            @endforeach
                        </select>
                    </div>
                @endauth

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Emergency Type</label>
                        <select id="emergency-type" class="glass-input w-full font-bold text-slate-950">
                            <option value="Accident">Critical Accident</option>
                            <option value="Cardiac Arrest">Cardiac Arrest</option>
                            <option value="Stroke">Stroke Protocol</option>
                            <option value="Other">General Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Priority Level</label>
                        <select id="emergency-severity" class="glass-input w-full font-bold text-slate-950">
                            <option value="high">Tier 1: Life Threatening</option>
                            <option value="medium">Tier 2: Urgent</option>
                            <option value="low">Tier 3: Standard</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="button" @click="closeSosModal()" class="flex-1 py-4 px-6 text-sm font-bold text-slate-400 hover:text-slate-950 transition-colors uppercase tracking-widest">Cancel</button>
                <button type="button" @click="triggerEmergency()" :disabled="isSosLoading" class="flex-[2] btn-premium !py-4 shadow-2xl shadow-red-200">
                    <span x-show="!isSosLoading">CONFIRM DISPATCH</span>
                    <span x-show="isSosLoading">TRANSMITTING...</span>
                </button>
            </div>
        </div>
    </div>
    </main>

    <!-- Script Block (Alpine-driven Tracking) -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('trackingState', () => ({
                currentStatus: 'pending',
                hospital: 'Locating Nearest...',
                ambulance: 'Awaiting Dispatch',
                eta: 'CALCULATING...',
                emergencyId: localStorage.getItem('active_emergency_id'),
                sessionId: localStorage.getItem('active_session_id'),
                map: null,
                userMarker: null,
                ambulanceMarker: null,
                hospitalMarkersGroup: null,
                pollInterval: null,
                isSosLoading: false,
                currentLat: null,
                currentLng: null,

                init() {
                    console.log('[ResQFlow] Initializing State Forensics...');
                    if (this.emergencyId) {
                        // Strict validation: Check if we are stuck in a terminal state
                        this.pollStatus().then(() => {
                            if (['completed', 'cancelled', 'rejected'].includes(this.currentStatus)) {
                                console.log('[ResQFlow] Stale terminal session detected on boot. Clearing.');
                                this.clearSession();
                            } else {
                                this.startTracking();
                            }
                        });
                    }
                    
                    document.getElementById('sosButton')?.addEventListener('click', () => {
                        window.openSosModal();
                    });

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            pos => { this.currentLat = pos.coords.latitude; this.currentLng = pos.coords.longitude; },
                            err => console.warn('Geolocation error:', err)
                        );
                    }
                },

                triggerEmergency() {
                    if (this.isSosLoading) return;
                    this.isSosLoading = true;
                    console.log('[SOS] Triggered. Acquiring location...');
                    
                    if (!this.currentLat || !this.currentLng) {
                        const timeout = setTimeout(() => {
                            console.warn('[SOS] Geolocation timeout. Using Sector 01 default.');
                            this.currentLat = 30.2110;
                            this.currentLng = 74.9455;
                            this.performSubmit();
                        }, 5000);

                        navigator.geolocation.getCurrentPosition(
                            pos => { 
                                clearTimeout(timeout);
                                this.currentLat = pos.coords.latitude; 
                                this.currentLng = pos.coords.longitude; 
                                this.performSubmit();
                            },
                            err => {
                                clearTimeout(timeout);
                                console.warn('[SOS] Geolocation failed. Using Sector 01 default.', err);
                                this.currentLat = 30.2110;
                                this.currentLng = 74.9455;
                                this.performSubmit();
                            },
                            { enableHighAccuracy: true, timeout: 5000 }
                        );
                    } else {
                        this.performSubmit();
                    }
                },

                async performSubmit() {
                    const type = document.getElementById('emergency-type').value;
                    const severity = document.getElementById('emergency-severity').value;
                    const familyId = document.getElementById('family-member-id')?.value || null;

                    try {
                        const res = await fetch('{{ route("emergency.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ latitude: this.currentLat, longitude: this.currentLng, emergency_type: type, severity: severity, family_member_id: familyId })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.emergencyId = data.emergency_id;
                            this.sessionId = data.session_id || '';
                            localStorage.setItem('active_emergency_id', this.emergencyId);
                            localStorage.setItem('active_session_id', this.sessionId);
                            window.closeSosModal();
                            this.startTracking();
                        } else {
                            alert(data.message || 'Dispatch Error');
                        }
                    } catch (e) {
                        alert('Connection to Command Center failed.');
                    } finally {
                        this.isSosLoading = false;
                    }
                },

                startTracking() {
                    document.getElementById('hero-section').classList.add('hidden');
                    document.getElementById('tracking-section').classList.remove('hidden');
                    
                    this.$nextTick(() => {
                        this.initMap();
                        this.pollStatus();
                        this.pollInterval = setInterval(() => this.pollStatus(), 5000);
                    });
                },

                initMap() {
                    if (this.map) return;
                    this.map = L.map('tracking-map', {
                        maxBounds: [[6.5, 68.0], [35.5, 97.5]],
                        maxBoundsViscosity: 1.0,
                        minZoom: 4
                    }).setView([22.9734, 78.6569], 5);

                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; CartoDB'
                    }).addTo(this.map);

                    this.hospitalMarkersGroup = L.featureGroup().addTo(this.map);
                },

                async pollStatus() {
                    if (!this.emergencyId) return;
                    try {
                        const res = await fetch(`{{ route("emergency.track") }}?id=${this.emergencyId}&session_id=${this.sessionId || ''}`);
                        const data = await res.json();
                        if (!data.success) {
                            this.clearSession();
                            return;
                        }
                        this.updateUI(data);
                    } catch (err) {
                        console.warn('[ResQFlow] Polling error:', err);
                    }
                },

                updateUI(data) {
                    this.currentStatus = data.status;
                    this.hospital = data.hospital || 'Locating...';
                    this.ambulance = data.ambulance || 'Awaiting Dispatch';
                    this.eta = data.eta_minutes ? `~${data.eta_minutes} MIN` : 'CALCULATING...';

                    if (data.latitude && data.longitude) {
                        const pos = [data.latitude, data.longitude];
                        if (!this.userMarker) {
                            this.userMarker = L.circleMarker(pos, { color: '#ef4444', radius: 8, fillOpacity: 1 }).addTo(this.map);
                            this.map.setView(pos, 13);
                        } else {
                            this.userMarker.setLatLng(pos);
                        }
                    }

                    if (data.all_hospitals) {
                        this.hospitalMarkersGroup.clearLayers();
                        data.all_hospitals.forEach(h => {
                            const isAssigned = Number(data.assigned_hospital_id) === Number(h.id);
                            L.circleMarker([h.latitude, h.longitude], {
                                color: isAssigned ? '#10b981' : '#3b82f6',
                                radius: isAssigned ? 10 : 6,
                                fillOpacity: 0.8
                            }).addTo(this.hospitalMarkersGroup).bindPopup(`<b>${h.name}</b>`);
                        });
                    }

                    if (['dispatched', 'en_route', 'arrived'].includes(data.status) && data.ambulance_lat) {
                        const pos = [data.ambulance_lat, data.ambulance_lng];
                        if (!this.ambulanceMarker) {
                            this.ambulanceMarker = L.circleMarker(pos, { color: '#f59e0b', radius: 12, fillOpacity: 1 }).addTo(this.map);
                        } else {
                            this.ambulanceMarker.setLatLng(pos);
                        }
                    }

                    if (data.status === 'completed') {
                        console.log('[ResQFlow] Mission Completed. Initiating Teardown Protocol.');
                        clearInterval(this.pollInterval);
                        
                        // CAPTURE DATA BEFORE TEARDOWN
                        const finalHospital = this.hospital;
                        const finalAmbulance = this.ambulance;

                        setTimeout(() => {
                            document.getElementById('tracking-section').classList.add('hidden');
                            document.getElementById('summary-section').classList.remove('hidden');
                            document.getElementById('summary-time').innerText = 'Completed';
                            document.getElementById('summary-hospital').innerText = finalHospital;
                            document.getElementById('summary-ambulance').innerText = finalAmbulance;
                            
                            // CRITICAL: CLEAR PERSISTENCE NOW. 
                            // This ensures refresh goes to landing page, not summary.
                            this.fullTeardown();
                        }, 2000);
                    }

                    if (['rejected', 'cancelled'].includes(data.status)) {
                        this.clearSession();
                    }
                },

                async cancelEmergency() {
                    if (!confirm('Abort mission?')) return;
                    try {
                        const res = await fetch(`/emergency/${this.emergencyId}/cancel`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        if (data.success) this.clearSession();
                    } catch (e) {
                        alert('Cancellation failed.');
                    }
                },

                fullTeardown() {
                    console.log('[ResQFlow] Destroying session state and listeners.');
                    localStorage.removeItem('active_emergency_id');
                    localStorage.removeItem('active_session_id');
                    if (this.pollInterval) clearInterval(this.pollInterval);
                    // Reset internal references
                    this.emergencyId = null;
                    this.sessionId = null;
                },

                clearSession() {
                    this.fullTeardown();
                    location.reload();
                },

                resetToInitial() {
                    this.clearSession();
                }
            }));
        });

        window.closeSosModal = () => {
            document.getElementById('sos-modal')?.classList.add('hidden');
        };
        window.openSosModal = () => {
            if (localStorage.getItem('active_emergency_id')) {
                alert('You already have an active emergency request.');
                return;
            }
            document.getElementById('sos-modal')?.classList.remove('hidden');
        };
    </script>
</body>
</html>
