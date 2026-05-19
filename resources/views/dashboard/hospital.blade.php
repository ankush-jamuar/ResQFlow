<x-app-layout>
    <x-slot name="header">
        Dispatch Command Center
    </x-slot>

    <div class="space-y-10" x-data="hospitalDashboard()">
        {{-- Operational Status --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Fleet Readiness</p>
                    <p class="text-3xl font-black text-slate-950 tracking-tighter">{{ $ambulances->where('status', 'available')->count() }} Units</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>

            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pending Signals</p>
                    <p class="text-3xl font-black text-red-500 tracking-tighter">{{ $emergencies->where('status', 'pending')->count() }} SOS</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 border border-red-100 animate-pulse">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Active Missions</p>
                    <p class="text-3xl font-black text-blue-600 tracking-tighter">{{ $emergencies->where('status', 'en_route')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 border border-blue-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                </div>
            </div>
        </section>

        {{-- Active Dispatch Queue --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-950 tracking-tight">Active Response Stream</h3>
                <a href="{{ route('hospital.queue') }}" class="btn-premium !py-2.5 !px-6 text-[9px] !rounded-xl">Full Command Queue</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-6 py-4">Incident Token</th>
                            <th class="px-6 py-4">Severity</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($emergencies as $emergency)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-6 py-5">
                                    <p class="text-sm font-black text-slate-950">Mission #{{ str_pad($emergency->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $emergency->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase tracking-widest {{ $emergency->severity === 'high' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-50 text-slate-500' }}">
                                        {{ $emergency->severity }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <x-status-badge :status="$emergency->status" />
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($emergency->status === 'pending')
                                            <a href="{{ route('hospital.queue') }}" class="px-4 py-2 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-xl shadow-slate-200">Initialize</a>
                                        @else
                                            <a href="{{ route('hospital.queue') }}" class="px-4 py-2 bg-white text-slate-400 border border-slate-100 text-[9px] font-black uppercase tracking-widest rounded-lg">Details</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Resource Quick View --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-950 tracking-tight">Resource Inventory</h3>
                    <a href="{{ route('hospital.resources') }}" class="text-[9px] font-black text-red-500 uppercase tracking-widest hover:underline">Manage All</a>
                </div>
                <div class="space-y-4">
                    @foreach($resources->take(3) as $resource)
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-white/40 border border-white/60">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $resource->type }}</p>
                                <p class="text-sm font-black text-slate-950">{{ $resource->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-red-500">{{ $resource->quantity }}</p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase">Available</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl bg-slate-950 !border-slate-800 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/10 blur-3xl rounded-full"></div>
                <h3 class="text-xl font-black mb-6 tracking-tight">Tactical Fleet Status</h3>
                <div class="space-y-4">
                    @foreach($ambulances->take(3) as $amb)
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-white/5 border border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black">{{ $amb->plate_number }}</p>
                                    <p class="text-[9px] font-bold text-slate-500 uppercase">{{ $amb->driver_name }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$amb->status" />
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('hospital.fleet') }}" class="mt-6 block text-center py-3 bg-white/5 hover:bg-white/10 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] transition-all">Command Fleet Management</a>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('hospitalDashboard', () => ({
                init() {
                    if (window.Echo) {
                        window.Echo.private(`emergency.hospital.{{ auth()->user()->hospital->id ?? 0 }}`)
                            .listen('.EmergencyCreated', (e) => {
                                window.location.reload();
                            })
                            .listen('.EmergencyStatusUpdated', (e) => {
                                window.location.reload();
                            });
                    }
                }
            }));
        });
    </script>
</x-app-layout>
