<x-app-layout>
    <x-slot name="header">
        Strategic Command Overview
    </x-slot>

    <div class="space-y-10" x-data="adminDashboard()">
        {{-- Metrics Pulse --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl group hover:bg-white transition-all duration-500">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Total Fleet Missions</p>
                <p class="text-4xl font-black text-slate-950 tracking-tighter">{{ $emergenciesCount }}</p>
                <div class="mt-4 w-12 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-slate-950 w-2/3"></div>
                </div>
            </div>

            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl group hover:bg-white transition-all duration-500">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Active Cycles (24h)</p>
                <p class="text-4xl font-black text-red-500 tracking-tighter">{{ $totalToday }}</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                    <span class="text-[9px] font-black text-red-400 uppercase">Live Load</span>
                </div>
            </div>

            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl group hover:bg-white transition-all duration-500">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Critical En-Route</p>
                <p class="text-4xl font-black text-blue-600 tracking-tighter">{{ $activeCount }}</p>
                <p class="mt-4 text-[10px] font-bold text-slate-500">OPTIMIZED STATE</p>
            </div>

            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl group hover:bg-white transition-all duration-500 bg-slate-950 !border-slate-800 shadow-slate-200">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Avg Velocity</p>
                <p class="text-4xl font-black text-white tracking-tighter">{{ $avgResponseMinutes }}<span class="text-lg text-slate-500 ml-1">m</span></p>
                <p class="mt-4 text-[10px] font-bold text-emerald-400">NOMINAL</p>
            </div>
        </section>

        {{-- Global Live Map --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-950 tracking-tight">Global Operational Map</h3>
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-950 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                    <span class="text-[9px] font-black text-white uppercase tracking-widest">Live Feed</span>
                </div>
            </div>
            <div id="admin-global-map" class="h-[400px] w-full rounded-[2.5rem] border border-white/40 shadow-inner z-0"></div>
        </section>

        {{-- Visual Intelligence --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xl font-black text-slate-950 tracking-tight">Mission Trajectory</h3>
                    <a href="{{ route('admin.analytics') }}" class="text-[9px] font-black text-red-500 uppercase tracking-widest hover:underline">Full Analytics</a>
                </div>
                <div class="h-[300px]">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            <div class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xl font-black text-slate-950 tracking-tight">Status distribution</h3>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Global State</span>
                </div>
                <div class="h-[300px]">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </section>

        {{-- Recent Feed --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-950 tracking-tight">Recent Signals</h3>
                <a href="{{ route('admin.emergencies') }}" class="btn-premium !py-2.5 !px-6 text-[9px] !rounded-xl">View All Missions</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-6 py-4">Operative</th>
                            <th class="px-6 py-4">Facility</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentEmergencies as $em)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-6 py-5">
                                    <p class="text-sm font-black text-slate-950">{{ $em->user->name ?? 'GUEST' }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">#{{ $em->id }}</p>
                                </td>
                                <td class="px-6 py-5 text-xs font-bold text-slate-600">{{ $em->hospital->name ?? '--' }}</td>
                                <td class="px-6 py-5">
                                    <x-status-badge :status="$em->status" />
                                </td>
                                <td class="px-6 py-5 text-right text-xs font-black text-slate-400">{{ $em->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Alpine Data for Auto-Refresh
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminDashboard', () => ({
                init() {
                    if (window.Echo) {
                        console.log('[Tactical] Subscribing to admin.emergencies');
                        window.Echo.private('admin.emergencies')
                            .listen('.EmergencyCreated', (e) => {
                                console.log('[Tactical] NEW EMERGENCY DETECTED:', e);
                                window.location.reload();
                            })
                            .listen('.EmergencyStatusUpdated', (e) => {
                                console.log('[Tactical] Status Update Sync:', e);
                                window.location.reload();
                            });
                    }
                }
            }));
        });

        // Map Setup - Default Center: India
        const map = L.map('admin-global-map', { zoomControl: false }).setView([20.5937, 78.9629], 5);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

        // Global Map Layers
        const emergencies = @json($activeEmergenciesList);
        const heatmapPoints = @json($heatmapData);
        
        const markersLayer = L.featureGroup();
        const heatmapLayer = L.heatLayer(heatmapPoints, {
            radius: 25,
            blur: 15,
            maxZoom: 10,
            gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'}
        });

        emergencies.forEach(em => {
            const emMarker = L.circleMarker([em.latitude, em.longitude], {
                radius: 8, fillColor: '#ef4444', color: '#fff', weight: 2, fillOpacity: 1
            }).bindPopup(`<b class="font-black text-xs uppercase">Mission #${em.id}</b><br><span class="text-[10px] uppercase text-slate-500">Target - ${em.status}</span>`);
            markersLayer.addLayer(emMarker);

            if (em.ambulance && em.ambulance.current_latitude && em.ambulance.current_longitude) {
                const ambMarker = L.circleMarker([em.ambulance.current_latitude, em.ambulance.current_longitude], {
                    radius: 6, fillColor: '#3b82f6', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(`<b class="font-black text-xs uppercase">Unit ${em.ambulance.plate_number}</b><br><span class="text-[10px] uppercase text-slate-500">${em.status}</span>`);
                markersLayer.addLayer(ambMarker);
            }
        });

        markersLayer.addTo(map);
        
        L.control.layers({
            "Active Missions": markersLayer,
            "Operational Heatmap": heatmapLayer
        }, null, { collapsed: false }).addTo(map);

        if (emergencies.length > 0) {
            map.fitBounds(markersLayer.getBounds(), { padding: [50, 50] });
        }

        // Charts
        Chart.defaults.font.family = 'Plus Jakarta Sans, system-ui, sans-serif';
        Chart.defaults.color = '#94a3b8';

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#f1f5f9', drawBorder: false }, ticks: { font: { weight: '700', size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { weight: '700', size: 10 } } }
            }
        };

        const dailyRaw = @json($dailyStats);
        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: dailyRaw.map(d => d.date),
                datasets: [{
                    data: dailyRaw.map(d => d.count),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 0
                }]
            },
            options: chartOptions
        });

        const statusRaw = @json($statusStats);
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusRaw),
                datasets: [{
                    data: Object.values(statusRaw),
                    backgroundColor: ['#f59e0b', '#3b82f6', '#0f172a', '#10b981', '#ef4444', '#94a3b8'],
                    borderWidth: 0
                }]
            },
            options: { ...chartOptions, cutout: '75%' }
        });
    });
    </script>
</x-app-layout>
