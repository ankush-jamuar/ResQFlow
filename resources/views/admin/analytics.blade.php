<x-app-layout>
    <x-slot name="header">
        Strategic Intelligence
    </x-slot>

    <div class="space-y-10">
        {{-- High-Level Tactical Metrics --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-500/10 blur-3xl rounded-full group-hover:bg-red-500/20 transition-colors"></div>
                <h3 class="text-xl font-black text-slate-950 tracking-tight mb-8">Severity Distribution</h3>
                <div class="h-[250px]">
                    <canvas id="severityChart"></canvas>
                </div>
            </div>

            <div class="md:col-span-2 glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 blur-3xl rounded-full group-hover:bg-blue-500/20 transition-colors"></div>
                <h3 class="text-xl font-black text-slate-950 tracking-tight mb-8">Regional Response Intelligence</h3>
                <div class="h-[250px]">
                    <canvas id="cityChart"></canvas>
                </div>
            </div>
        </section>

        {{-- Deep Intelligence Grid --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-950 tracking-tight">Mission Heatmap Data</h3>
                    <div class="flex gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Global Sector Sync</p>
                    </div>
                </div>
                <div class="space-y-6">
                    @foreach($cityStats as $stat)
                        <div class="flex items-center gap-6">
                            <p class="text-[10px] font-black text-slate-950 uppercase tracking-widest w-24">{{ $stat->city }}</p>
                            <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden flex items-center">
                                <div class="h-full bg-red-500 rounded-full" style="width: {{ ($stat->total / $cityStats->max('total')) * 100 }}%"></div>
                            </div>
                            <p class="text-xs font-black text-slate-400 w-12 text-right">{{ $stat->total }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl bg-slate-950 text-white relative overflow-hidden">
                <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-red-500/10 to-transparent"></div>
                <h3 class="text-xl font-black mb-8 tracking-tight">Response Velocity Insights</h3>
                <div class="space-y-10 relative z-10">
                    <div>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Network Average Response</p>
                        <p class="text-4xl font-black {{ $avgResponseTime > 15 ? 'text-red-500' : 'text-emerald-500' }}">
                            {{ $avgResponseTime }}<span class="text-lg text-slate-500 ml-1">m</span>
                        </p>
                        <p class="text-[9px] font-bold text-slate-600 uppercase mt-2 italic">Based on all completed missions</p>
                    </div>
                    <div class="w-full h-px bg-white/5"></div>
                    <div>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Negligence Watch</p>
                        @if($negligenceLeague->isEmpty())
                            <p class="text-sm font-black text-emerald-500 uppercase">No active warnings</p>
                        @else
                            <div class="space-y-3">
                                @foreach($negligenceLeague->take(3) as $badHosp)
                                    <div class="flex justify-between items-center bg-white/5 rounded-xl p-3">
                                        <span class="text-[10px] font-bold text-slate-300 uppercase truncate pr-4">{{ $badHosp->name }}</span>
                                        <span class="text-[10px] font-black text-red-500 bg-red-500/10 px-2 py-1 rounded-md">{{ $badHosp->warning_points }} ⚠️</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = 'Plus Jakarta Sans, system-ui, sans-serif';
        Chart.defaults.color = '#94a3b8';

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { display: false }, ticks: { font: { weight: '700', size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { weight: '700', size: 10 } } }
            }
        };

        const severityRaw = @json($severityStats);
        new Chart(document.getElementById('severityChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(severityRaw).map(s => s.toUpperCase()),
                datasets: [{
                    data: Object.values(severityRaw),
                    backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6'],
                    borderRadius: 12
                }]
            },
            options: chartOptions
        });

        const cityRaw = @json($cityStats);
        new Chart(document.getElementById('cityChart'), {
            type: 'line',
            data: {
                labels: cityRaw.map(c => c.city),
                datasets: [{
                    data: cityRaw.map(c => c.total),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 3
                }]
            },
            options: chartOptions
        });
    });
    </script>
</x-app-layout>
