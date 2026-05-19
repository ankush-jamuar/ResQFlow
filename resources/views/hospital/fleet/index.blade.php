<x-app-layout>
    <x-slot name="header">
        Rescue Fleet Management
    </x-slot>

    <div class="space-y-10">
        {{-- Fleet Status Summary --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-xl flex items-center gap-6">
                <div class="w-16 h-16 bg-emerald-50 rounded-[1.5rem] flex items-center justify-center text-emerald-500">
                    <p class="text-3xl font-black">{{ $ambulances->where('status', 'available')->count() }}</p>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-950 uppercase tracking-tight">Units Available</p>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Ready for Dispatch</p>
                </div>
            </div>
            <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-xl flex items-center gap-6">
                <div class="w-16 h-16 bg-red-50 rounded-[1.5rem] flex items-center justify-center text-red-500">
                    <p class="text-3xl font-black">{{ $ambulances->where('status', 'en_route')->count() }}</p>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-950 uppercase tracking-tight">Active Missions</p>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Units In-Transit</p>
                </div>
            </div>
            <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-xl flex items-center gap-6 bg-slate-950 text-white">
                <div class="w-16 h-16 bg-white/10 rounded-[1.5rem] flex items-center justify-center text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-black uppercase tracking-tight">Avg Turnaround</p>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">14.2 Minutes</p>
                </div>
            </div>
        </section>

        {{-- Main Fleet Inventory --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-2xl font-black text-slate-950 tracking-tight">Operational Fleet</h3>
                <button class="btn-premium !py-3 !px-8 text-[10px] !rounded-2xl shadow-xl shadow-red-100">Add Rescue Unit</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-8 py-6">Unit ID</th>
                            <th class="px-8 py-6">Assigned Operative</th>
                            <th class="px-8 py-6 text-center">Telemetry</th>
                            <th class="px-8 py-6 text-center">Status</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ambulances as $amb)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 group-hover:text-red-500 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-950">{{ $amb->plate_number }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Last Maint: {{ $amb->last_maintenance_at?->format('M d') ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-900">{{ $amb->driver_name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Medical Responder</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-3 max-w-[120px] mx-auto">
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-[8px] font-black uppercase tracking-widest">
                                                <span class="text-slate-400">Fuel</span>
                                                <span class="{{ $amb->fuel_level < 20 ? 'text-red-500 animate-pulse' : 'text-slate-600' }}">{{ $amb->fuel_level }}%</span>
                                            </div>
                                            <div class="h-1 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full {{ $amb->fuel_level < 20 ? 'bg-red-500' : 'bg-slate-950' }}" style="width: {{ $amb->fuel_level }}%"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-[8px] font-black uppercase tracking-widest">
                                                <span class="text-slate-400">Oxygen</span>
                                                <span class="{{ $amb->oxygen_level < 30 ? 'text-red-500 animate-pulse' : 'text-blue-500' }}">{{ $amb->oxygen_level }}%</span>
                                            </div>
                                            <div class="h-1 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full {{ $amb->oxygen_level < 30 ? 'bg-red-500' : 'bg-blue-500' }}" style="width: {{ $amb->oxygen_level }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <x-status-badge :status="$amb->status" />
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-3">
                                        <form action="{{ route('hospital.fleet.maintenance', $amb) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-3 bg-white border border-slate-100 text-slate-400 hover:text-blue-600 rounded-xl transition-all shadow-sm" title="Log Maintenance">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </button>
                                        </form>
                                        <button class="p-3 bg-white border border-slate-100 text-slate-400 hover:text-slate-950 rounded-xl transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-10">
                {{ $ambulances->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
