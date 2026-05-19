<x-app-layout>
    <div class="p-6 lg:p-8 space-y-8 bg-gradient-to-br from-gray-900 to-black min-h-screen text-slate-200 font-sans">
        
        {{-- 1. HEADER --}}
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter">COMMAND <span class="text-indigo-500">CENTER</span></h1>
                <p class="text-gray-400 font-medium tracking-wide">Real-time emergency system oversight and analytics</p>
            </div>
            <div class="flex items-center gap-6 bg-white/5 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.8)]"></span>
                    </div>
                    <span class="text-xs font-black uppercase tracking-[0.2em] text-emerald-400">Live System</span>
                </div>
                <div class="h-8 w-px bg-white/10"></div>
                <div class="text-right">
                    <p id="live-time" class="text-lg font-bold text-white tabular-nums">{{ now()->format('H:i:s') }}</p>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ now()->format('D, M d, Y') }}</p>
                </div>
            </div>
        </header>

        {{-- 2. METRICS GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            @php
                $metrics = [
                    ['label' => 'Total Emergencies', 'value' => $emergenciesCount, 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'color' => 'indigo'],
                    ['label' => 'Today\'s Volume', 'value' => $totalToday, 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5', 'color' => 'purple'],
                    ['label' => 'Active Signals', 'value' => $activeCount, 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z', 'color' => 'rose'],
                    ['label' => 'Successful ResQ', 'value' => $completedCount, 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'color' => 'emerald'],
                    ['label' => 'Avg Response', 'value' => $avgResponseMinutes . 'm', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'color' => 'amber'],
                ];
            @endphp
            @foreach($metrics as $metric)
                <div class="group bg-white/10 backdrop-blur-lg border border-white/10 p-6 rounded-2xl shadow-xl hover:scale-105 transition-all duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-{{ $metric['color'] }}-500/20 rounded-xl border border-{{ $metric['color'] }}-500/20 text-{{ $metric['color'] }}-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $metric['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-white tracking-tight">{{ $metric['value'] }}</p>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">{{ $metric['label'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- 3. CHARTS GRID --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Trend --}}
                <div class="bg-white/10 backdrop-blur-lg border border-white/10 p-8 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold text-white tracking-tight">Signal Analysis <span class="text-gray-500 font-medium text-sm ml-2">7 Day Window</span></h3>
                        <div class="flex gap-2">
                            <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                        </div>
                    </div>
                    <div class="h-80 w-full">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Status --}}
                    <div class="bg-white/10 backdrop-blur-lg border border-white/10 p-8 rounded-2xl shadow-xl">
                        <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-6">Status Distribution</h3>
                        <div class="h-64">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                    {{-- Cities --}}
                    <div class="bg-white/10 backdrop-blur-lg border border-white/10 p-8 rounded-2xl shadow-xl">
                        <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-6">City Load</h3>
                        <div class="h-64">
                            <canvas id="cityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. SIDE PANEL --}}
            <div class="space-y-8">
                {{-- Live Feed --}}
                <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl shadow-xl flex flex-col h-[500px]">
                    <div class="p-6 border-b border-white/5">
                        <h3 class="text-sm font-black text-white uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                            Emergency Stream
                        </h3>
                    </div>
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                        @foreach($recentEmergencies as $em)
                            <div class="p-4 rounded-xl border {{ in_array($em->status, ['pending', 'en_route']) ? 'bg-rose-500/10 border-rose-500/20 shadow-[0_0_15px_rgba(244,63,94,0.1)]' : 'bg-white/5 border-white/5' }} transition-all">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ in_array($em->status, ['pending', 'en_route']) ? 'text-rose-400' : 'text-gray-400' }}">
                                        {{ $em->emergency_type ?? 'Medical' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-gray-500 tabular-nums">{{ $em->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-200 font-medium leading-relaxed">{{ $em->user->name ?? 'Guest' }} - {{ $em->hospital->name ?? 'Unassigned' }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-[8px] font-bold px-2 py-0.5 rounded-full {{ in_array($em->status, ['pending', 'en_route']) ? 'bg-rose-500 text-white' : 'bg-gray-700 text-gray-300' }} uppercase">
                                        {{ $em->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- System Health --}}
                <div class="bg-white/10 backdrop-blur-lg border border-white/10 p-8 rounded-2xl shadow-xl">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-6">Unit Deployment</h3>
                    @php
                        $activeAmbs = \App\Models\Ambulance::where('status', 'in_transit')->count();
                        $availAmbs = \App\Models\Ambulance::where('status', 'available')->count();
                    @endphp
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-300">Active Units</span>
                            </div>
                            <span class="text-xl font-black text-white tabular-nums">{{ $activeAmbs }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-300">Ready Standby</span>
                            </div>
                            <span class="text-xl font-black text-white tabular-nums">{{ $availAmbs }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. HOSPITAL TABLE --}}
        <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 border-b border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <h3 class="text-xl font-bold text-white tracking-tight">Node Management</h3>
                <div class="flex gap-4 w-full md:w-auto">
                    <input type="text" placeholder="Search facilities..." class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all flex-1">
                    <select class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm text-gray-400 cursor-pointer">
                        <option>All Regions</option>
                        @foreach($citiesList as $city) <option>{{ $city }}</option> @endforeach
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 text-[10px] text-gray-500 font-black uppercase tracking-widest">
                            <th class="px-8 py-4">Facility</th>
                            <th class="px-8 py-4">Region</th>
                            <th class="px-8 py-4">Security Status</th>
                            <th class="px-8 py-4 text-right">Protocol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($hospitals as $hosp)
                            <tr class="hover:bg-white/5 transition-all group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-white group-hover:text-indigo-400 transition-colors">{{ $hosp->name }}</div>
                                    <div class="text-[10px] text-gray-500 truncate w-48">{{ $hosp->address }}</div>
                                </td>
                                <td class="px-8 py-5 text-sm font-bold text-gray-400">{{ trim(explode(',', $hosp->address)[0]) }}</td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase border border-indigo-500/20">
                                        <span class="w-1 h-1 rounded-full bg-indigo-400 shadow-[0_0_8px_rgba(129,140,248,1)]"></span>
                                        Encrypted
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button class="bg-white/5 hover:bg-white text-gray-400 hover:text-black px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        📍 Locate
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 6. MAP --}}
        <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl shadow-xl overflow-hidden h-[450px]">
            <div id="admin-map" class="w-full h-full grayscale-[0.8] brightness-[0.7] invert-[0.1]"></div>
        </div>

    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        .leaflet-container { background: #000 !important; }
        .leaflet-popup-content-wrapper { background: #111 !important; color: #fff !important; border-radius: 12px !important; border: 1px solid #333 !important; }
        .leaflet-popup-tip { background: #111 !important; }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Clock
            setInterval(() => {
                document.getElementById('live-time').innerText = new Date().toLocaleTimeString('en-US', { hour12: false });
            }, 1000);

            // Chart Defaults
            Chart.defaults.color = '#666';
            Chart.defaults.font.family = 'Inter, sans-serif';

            // 1. TREND
            const trendData = @json($dailyStats);
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.date),
                    datasets: [{
                        data: trendData.map(d => d.count),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                }
            });

            // 2. STATUS
            const statusStats = @json($statusStats);
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusStats),
                    datasets: [{
                        data: Object.values(statusStats),
                        backgroundColor: ['#6366f1', '#a855f7', '#10b981', '#f43f5e', '#f59e0b'],
                        borderWidth: 0,
                        cutout: '80%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { padding: 20, boxWidth: 10, font: { size: 10 } } } }
                }
            });

            // 3. CITIES
            const cityData = @json($cityStats);
            new Chart(document.getElementById('cityChart'), {
                type: 'bar',
                data: {
                    labels: cityData.map(c => c.city),
                    datasets: [{
                        data: cityData.map(c => c.total),
                        backgroundColor: '#6366f1',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                }
            });

            // MAP
            const map = L.map('admin-map', { zoomControl: false }).setView([20.5937, 78.9629], 5);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '©OpenStreetMap, ©CartoDB'
            }).addTo(map);

            const dotIcon = L.divIcon({
                className: 'custom-dot',
                html: '<div style="background:#6366f1; width:8px; height:8px; border-radius:50%; border:2px solid #fff; box-shadow:0 0 10px #6366f1;"></div>',
                iconSize: [12, 12]
            });

            @foreach($hospitals as $hosp)
                L.marker([{{ $hosp->latitude }}, {{ $hosp->longitude }}], { icon: dotIcon }).addTo(map);
            @endforeach
        });
    </script>
    @endpush
</x-app-layout>
