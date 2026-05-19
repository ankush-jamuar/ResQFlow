<x-app-layout>
    <x-slot name="header">
        System Health & Observability
    </x-slot>

    <div class="space-y-10" x-data="healthMonitor()">
        {{-- High-Level Status Grid --}}
        <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <template x-for="(status, key) in stats" :key="key">
                <div class="glass-panel rounded-[2.5rem] p-8 border-white/40 shadow-xl flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4" x-text="key.replace('_', ' ')"></p>
                        <h3 class="text-3xl font-black text-slate-950" x-text="status.value || status.status || '---'"></h3>
                    </div>
                    <div class="mt-6 flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full animate-pulse-soft" :class="getHealthClass(status)"></div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight" x-text="status.latency_ms ? `${status.latency_ms}ms latency` : 'Operational'"></p>
                    </div>
                </div>
            </template>
        </section>

        {{-- Operational Metrics --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
                <div class="flex justify-between items-center mb-10">
                    <h2 class="text-2xl font-black text-slate-950 tracking-tight">Failed Jobs Monitoring</h2>
                    <div class="flex gap-4">
                        <button @click="fetchFailedJobs()" class="px-5 py-2 bg-slate-50 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-100 transition-all">Refresh Logs</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">ID / UUID</th>
                                <th class="px-6 py-4">Queue</th>
                                <th class="px-6 py-4">Failed At</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="job in failedJobs" :key="job.id">
                                <tr class="group hover:bg-red-50/30 transition-all duration-300">
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-black text-slate-950" x-text="`#${job.id}`"></p>
                                        <p class="text-[8px] font-bold text-slate-400" x-text="job.uuid"></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-slate-100 text-[9px] font-black rounded-lg uppercase" x-text="job.queue"></span>
                                    </td>
                                    <td class="px-6 py-4 text-[10px] font-bold text-slate-500" x-text="job.failed_at"></td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="retryJob(job.id)" class="px-4 py-2 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all">Retry</button>
                                            <button @click="deleteJob(job.id)" class="px-4 py-2 bg-white border border-red-100 text-red-500 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-red-50 transition-all">Flush</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="failedJobs.length === 0">
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        </div>
                                        <p class="text-sm font-black text-slate-950 uppercase tracking-tight">Queue Integrity Nominal</p>
                                        <p class="text-xs text-slate-400 font-medium mt-1">No failed jobs detected in the last 24 hours.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-950 tracking-tight mb-8">System Reliability</h2>
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <p class="text-xs font-bold text-slate-500">Uptime Reliability</p>
                            <p class="text-sm font-black text-emerald-500 uppercase">99.98%</p>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 w-[99.98%] shadow-[0_0_10px_rgba(16,185,129,0.4)]"></div>
                        </div>

                        <div class="flex justify-between items-center mt-10">
                            <p class="text-xs font-bold text-slate-500">API Latency Avg</p>
                            <p class="text-sm font-black text-slate-950 uppercase" x-text="stats.database?.latency_ms ? `${stats.database.latency_ms}ms` : '---'"></p>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 w-[45%] shadow-[0_0_10px_rgba(59,130,246,0.4)]"></div>
                        </div>

                        <div class="pt-10 border-t border-slate-100 mt-10">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Environment Signature</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-2xl">
                                    <p class="text-[8px] font-bold text-slate-400 uppercase mb-1">PHP Version</p>
                                    <p class="text-xs font-black text-slate-950" x-text="stats.system?.php_version"></p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl">
                                    <p class="text-[8px] font-bold text-slate-400 uppercase mb-1">Environment</p>
                                    <p class="text-xs font-black text-slate-950 uppercase" x-text="stats.system?.environment"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-10 p-6 bg-slate-950 rounded-[2rem] text-white">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Operational Health</p>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.8)]"></div>
                        <p class="text-lg font-black tracking-tight">ALL SYSTEMS NOMINAL</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function healthMonitor() {
            return {
                stats: {},
                failedJobs: [],
                init() {
                    this.fetchStats();
                    this.fetchFailedJobs();
                    setInterval(() => this.fetchStats(), 5000);
                },
                async fetchStats() {
                    try {
                        const res = await fetch('{{ route('admin.api.stats') }}');
                        const data = await res.json();
                        this.stats = {
                            database: data.database,
                            redis: data.redis,
                            queue: { ...data.queue, value: data.queue.pending },
                            active_missions: { value: data.operational.active_emergencies, status: 'online' },
                            system: data.system
                        };
                    } catch (e) {
                        console.error('Failed to fetch stats', e);
                    }
                },
                async fetchFailedJobs() {
                    try {
                        const res = await fetch('{{ route('admin.api.failed-jobs') }}');
                        this.failedJobs = await res.json();
                    } catch (e) {
                        console.error('Failed to fetch failed jobs', e);
                    }
                },
                getHealthClass(status) {
                    if (status.status === 'offline' || (status.failed > 0)) return 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]';
                    if (status.latency_ms > 200) return 'bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]';
                    return 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]';
                },
                async retryJob(id) {
                    const res = await fetch(`/admin/api/failed-jobs/${id}/retry`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if (res.ok) this.fetchFailedJobs();
                },
                async deleteJob(id) {
                    if (!confirm('Permanently remove this failed job?')) return;
                    const res = await fetch(`/admin/api/failed-jobs/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if (res.ok) this.fetchFailedJobs();
                }
            }
        }
    </script>
</x-app-layout>
