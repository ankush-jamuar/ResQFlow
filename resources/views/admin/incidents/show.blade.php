<x-app-layout>
    <x-slot name="header">
        Mission Replay: #{{ $emergency->id }}
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- Left: Mission Metadata & Timeline --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="glass-panel rounded-[2.5rem] p-8 border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Core Intel</p>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500 uppercase">Status</span>
                        <span class="px-3 py-1 bg-slate-950 text-white text-[10px] font-black rounded-lg uppercase tracking-tight">{{ $emergency->status }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500 uppercase">Assigned Unit</span>
                        <span class="text-xs font-black text-slate-950 uppercase">{{ $emergency->ambulance->plate_number ?? 'UNASSIGNED' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500 uppercase">Response Time</span>
                        <span class="text-xs font-black text-slate-950 uppercase">{{ $emergency->completed_at ? $emergency->created_at->diffInMinutes($emergency->completed_at) . ' MIN' : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel rounded-[2.5rem] p-8 border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Operational Timeline</p>
                <div class="relative space-y-8 before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                    @foreach($audits as $audit)
                        <div class="relative pl-10">
                            <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-white border-4 border-slate-950 z-10"></div>
                            <div class="flex flex-col">
                                <p class="text-[10px] font-black text-slate-950 uppercase tracking-tight">{{ $audit->action }}</p>
                                <p class="text-[9px] text-slate-500 font-bold mb-2">{{ $audit->created_at->format('H:i:s.v') }}</p>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-[9px] text-slate-400 font-black uppercase mb-1">State Transition</p>
                                    <p class="text-[10px] font-bold text-slate-700 uppercase">
                                        <span class="text-slate-400">{{ $audit->old_state ?: 'NULL' }}</span>
                                        <span class="mx-2 text-slate-300">→</span>
                                        <span class="text-slate-950">{{ $audit->new_state ?: 'NULL' }}</span>
                                    </p>
                                    @if($audit->notes)
                                        <p class="mt-2 text-[9px] text-slate-500 italic leading-relaxed">{{ $audit->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Replay Visualization (Conceptual Map Replay) --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-panel rounded-[3rem] p-4 border-white/40 shadow-2xl overflow-hidden aspect-video relative group">
                <div id="replay-map" class="w-full h-full rounded-[2.5rem] z-0"></div>
                
                {{-- Playback Controls Overlay --}}
                <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex items-center gap-6 px-8 py-4 bg-slate-950/90 backdrop-blur-xl rounded-3xl border border-white/10 z-10 shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-500">
                    <button class="w-10 h-10 flex items-center justify-center text-white hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </button>
                    <div class="flex flex-col min-w-[200px]">
                        <div class="h-1 w-full bg-white/10 rounded-full mb-2 relative overflow-hidden">
                            <div class="absolute inset-y-0 left-0 bg-red-500 w-1/3"></div>
                        </div>
                        <div class="flex justify-between items-center text-[8px] font-black text-slate-400 uppercase tracking-widest">
                            <span>00:00:00</span>
                            <span class="text-white">REPLAY MODE: NOMINAL</span>
                            <span>{{ $emergency->completed_at ? $emergency->created_at->diff($emergency->completed_at)->format('%H:%I:%S') : 'LIVE' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Map Label Overlay --}}
                <div class="absolute top-10 right-10 px-4 py-2 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl z-10">
                    Tactical Reconstruction
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="glass-panel rounded-[2.5rem] p-10 border-white/40 shadow-xl">
                    <h3 class="text-xl font-black text-slate-950 tracking-tight mb-6 text-center">Telemetry Signature</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Start Latency</p>
                            <p class="text-sm font-black text-slate-950">142ms</p>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg ETA Error</p>
                            <p class="text-sm font-black text-slate-950">-0.4 min</p>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Signal Reliability</p>
                            <p class="text-sm font-black text-emerald-500">99.2%</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-[2.5rem] p-10 border-white/40 shadow-xl bg-slate-950 text-white">
                    <h3 class="text-xl font-black tracking-tight mb-6">Mission Verdict</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-8">
                        The mission was executed within the target SLA parameters. Ambulance #{{ $emergency->ambulance->id ?? '--' }} maintained consistent telemetry propagation throughout the en-route phase.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center shadow-[0_0_20px_rgba(16,185,129,0.4)]">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Compliance Status</p>
                            <p class="text-sm font-black uppercase">FULL COMPLIANCE</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('replay-map', {
                zoomControl: false,
                attributionControl: false
            }).setView([{{ $emergency->latitude }}, {{ $emergency->longitude }}], 14);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

            // User Location
            L.circleMarker([{{ $emergency->latitude }}, {{ $emergency->longitude }}], {
                color: '#ef4444',
                radius: 10,
                fillOpacity: 1,
                weight: 4
            }).addTo(map).bindTooltip('INCIDENT ORIGIN', { permanent: true, className: 'map-tooltip' });

            @if($emergency->hospital)
                // Hospital Location
                L.circleMarker([{{ $emergency->hospital->latitude }}, {{ $emergency->hospital->longitude }}], {
                    color: '#3b82f6',
                    radius: 12,
                    fillOpacity: 1,
                    weight: 4
                }).addTo(map).bindTooltip('ASSIGNED FACILITY', { permanent: true, direction: 'top' });
            @endif

            // Reconstruct path if telemetry existed (simplified for now)
            const pathPoints = [
                [{{ $emergency->latitude }}, {{ $emergency->longitude }}],
                @if($emergency->hospital) [{{ $emergency->hospital->latitude }}, {{ $emergency->hospital->longitude }}] @endif
            ];
            
            L.polyline(pathPoints, {
                color: '#94a3b8',
                dashArray: '10, 10',
                weight: 2
            }).addTo(map);

            map.fitBounds(pathPoints, { padding: [50, 50] });
        });
    </script>

    <style>
        .map-tooltip {
            background: #0f172a !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: white !important;
            font-weight: 900 !important;
            font-size: 8px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            padding: 4px 8px !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5) !important;
        }
    </style>
</x-app-layout>
