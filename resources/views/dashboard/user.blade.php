<x-app-layout>
    <x-slot name="header">
        Mission Pulse
    </x-slot>

    <div class="space-y-10" x-data="userMission('{{ $activeEmergency->status ?? '' }}')">
        @if($activeEmergency)
            <section class="reveal">
                <div class="glass-panel rounded-[3rem] p-12 shadow-2xl relative overflow-hidden">
                    {{-- Ambient Background --}}
                    <div class="absolute top-0 right-0 w-64 h-64 bg-red-100/20 blur-3xl -z-10 animate-pulse-soft"></div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                        <div>
                            <h2 class="text-4xl font-black text-slate-950 tracking-tight mb-2 uppercase">Emergency Protocol Active</h2>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-red-500 animate-ping"></div>
                                <p class="text-slate-500 font-black uppercase text-[10px] tracking-[0.3em]">Live Tracking Mission #{{ str_pad($activeEmergency->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <x-status-badge ::status="currentStatus" />
                            @if(!in_array($activeEmergency->status, ['completed', 'cancelled', 'rejected']))
                                <form method="POST" action="{{ route('emergency.cancel', $activeEmergency->id) }}">
                                    @csrf
                                    <button type="submit" class="px-6 py-2.5 bg-red-500 text-white text-[10px] font-black rounded-full hover:bg-red-600 transition-all shadow-xl shadow-red-100 uppercase tracking-widest">Abort Mission</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                        <div class="glass-card !bg-white/60 p-8 rounded-[2.5rem] border-white/60">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Target Facility</p>
                            <p class="text-xl font-black text-slate-950 uppercase tracking-tight">{{ $activeEmergency->hospital->name ?? 'Locating...' }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ trim(explode(',', $activeEmergency->hospital->address ?? '')[0]) }}</p>
                        </div>
                        <div class="glass-card !bg-white/60 p-8 rounded-[2.5rem] border-white/60">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Assigned Unit</p>
                            <p class="text-xl font-black text-slate-950 uppercase tracking-tight" x-text="ambulancePlate || 'Awaiting Dispatch'"></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1" x-text="driverName || 'Unit Pending'"></p>
                        </div>
                        <div class="glass-card !bg-slate-950 p-8 rounded-[2.5rem] text-white relative overflow-hidden group">
                            <div class="absolute inset-0 bg-red-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Response Velocity (ETA)</p>
                            <p class="text-4xl font-black text-red-500 tracking-tighter">
                                <span x-text="eta"></span><span class="text-lg ml-1 text-slate-500">min</span>
                            </p>
                            <p class="text-[9px] font-bold text-emerald-500 uppercase mt-1 tracking-widest">Live Dynamic Calculation</p>
                        </div>
                    </div>

                    {{-- Map Integration --}}
                    <div id="tracking-map" class="h-[500px] w-full rounded-[2.5rem] border border-white/40 shadow-inner mb-12"></div>

                    {{-- Status Timeline --}}
                    <div class="max-w-4xl mx-auto">
                        <x-status-timeline status="" id="tracking-timeline" />
                    </div>
                </div>
            </section>
        @else
            {{-- ... keep existing empty state ... --}}
            <section class="reveal py-20 flex flex-col items-center text-center">
                <div class="w-32 h-32 bg-slate-100 rounded-[2.5rem] flex items-center justify-center text-slate-300 mb-8">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h2 class="text-4xl font-black text-slate-950 tracking-tighter mb-4">Shield Status: Active</h2>
                <p class="text-lg text-slate-500 font-medium max-w-lg mb-12">No active emergencies detected. Our tactical network is on standby 24/7 to protect you.</p>
                
                <div class="flex gap-4">
                    <a href="/" class="btn-premium !px-10 !py-4 text-sm !rounded-2xl shadow-xl shadow-red-100">Initiate Protocol</a>
                    <a href="{{ route('user.history') }}" class="px-10 py-4 bg-white border border-slate-100 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-50 transition-all">Mission Archives</a>
                </div>
            </section>
        @endif
    </div>

    {{-- Leaflet Integration --}}
    @if($activeEmergency)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .leaflet-marker-icon {
                transition: transform 3s linear;
            }
        </style>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('userMission', (initialStatus) => ({
                    currentStatus: initialStatus,
                    eta: {{ $activeEmergency->eta_minutes ?? 0 }},
                    ambulancePlate: '{{ $activeEmergency->ambulance->plate_number ?? "" }}',
                    driverName: '{{ $activeEmergency->ambulance->driver_name ?? "" }}',
                    emergencyId: {{ $activeEmergency->id }},
                    map: null,
                    ambulanceMarker: null,

                    init() {
                        this.initMap();
                        this.startPolling();
                        this.initWebSockets();
                    },

                    initMap() {
                        this.map = L.map('tracking-map', { zoomControl: false }).setView([{{ $activeEmergency->latitude }}, {{ $activeEmergency->longitude }}], 14);
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(this.map);
                        
                        // User Location Marker
                        L.circleMarker([{{ $activeEmergency->latitude }}, {{ $activeEmergency->longitude }}], {
                            radius: 10,
                            fillColor: '#ef4444',
                            color: '#fff',
                            weight: 3,
                            fillOpacity: 1
                        }).addTo(this.map).bindPopup('<b class="font-black">YOUR LOCATION</b>');

                        // Hospital Marker
                        @if($activeEmergency->hospital)
                            L.circleMarker([{{ $activeEmergency->hospital->latitude }}, {{ $activeEmergency->hospital->longitude }}], {
                                radius: 8,
                                fillColor: '#10b981',
                                color: '#fff',
                                weight: 2,
                                fillOpacity: 1
                            }).addTo(this.map).bindPopup('<b class="font-black">{{ addslashes($activeEmergency->hospital->name) }}</b>');
                        @endif
                    },

                    async updateTracking() {
                        try {
                            const response = await fetch(`/api/emergency/track?id=${this.emergencyId}`);
                            const data = await response.json();

                            if (data.success) {
                                this.currentStatus = data.status;
                                this.eta = data.eta_minutes || 0;
                                this.ambulancePlate = data.ambulance_plate || this.ambulancePlate;
                                this.driverName = data.driver_name || this.driverName;

                                if (['completed', 'cancelled', 'rejected'].includes(data.status)) {
                                    window.location.href = '{{ route("user.history") }}';
                                    return;
                                }

                                if (data.ambulance_lat && data.ambulance_lng) {
                                    if (!this.ambulanceMarker) {
                                        this.ambulanceMarker = L.circleMarker([data.ambulance_lat, data.ambulance_lng], {
                                            radius: 8,
                                            fillColor: '#3b82f6',
                                            color: '#fff',
                                            weight: 3,
                                            fillOpacity: 1
                                        }).addTo(this.map).bindPopup('<b class="font-black">RESCUE UNIT</b>');
                                    } else {
                                        this.ambulanceMarker.setLatLng([data.ambulance_lat, data.ambulance_lng]);
                                    }
                                }
                            }
                        } catch (error) {
                            console.error('[Sync] Tracking poll failed', error);
                        }
                    },

                    startPolling() {
                        this.pollingInterval = setInterval(() => this.updateTracking(), 3000);
                        this.updateTracking();
                    },

                    initWebSockets() {
                        if (window.Echo) {
                            const channelName = `emergency.tracking.${this.emergencyId}`;
                            console.log(`[Sync] Listening on: ${channelName}`);

                            window.Echo.channel(channelName)
                                .listen('.EmergencyStatusUpdated', (e) => {
                                    console.log('[Sync] WebSocket Event:', e);
                                    this.currentStatus = e.status;
                                    this.eta = e.eta_minutes || this.eta;

                                    if (['completed', 'cancelled', 'rejected'].includes(e.status)) {
                                        window.location.href = '{{ route("user.history") }}';
                                    }
                                });
                        }
                    }
                }));
            });
        </script>
    @endif
</x-app-layout>
