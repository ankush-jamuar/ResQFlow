<x-app-layout>
    <x-slot name="header">
        Global Fleet Monitor
    </x-slot>

    <div class="space-y-10">
        {{-- Fleet Analytics Cards --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Active Units</p>
                <p class="text-3xl font-black text-slate-950">{{ $ambulances->count() }}</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Global Status Live</span>
                </div>
            </div>
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Unit Readiness</p>
                <p class="text-3xl font-black text-emerald-500">94.2%</p>
                <div class="mt-4 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 w-[94%]"></div>
                </div>
            </div>
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl bg-slate-950 text-white">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Average Velocity</p>
                <p class="text-3xl font-black text-red-500">42<span class="text-lg text-slate-500 ml-1">km/h</span></p>
                <p class="mt-4 text-[9px] font-bold text-slate-400 uppercase tracking-widest">Peak Hour Delta: +8%</p>
            </div>
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Strategic Alerts</p>
                <p class="text-3xl font-black text-slate-950">0</p>
                <div class="mt-4 text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">All Systems Nominal</div>
            </div>
        </section>

        {{-- Tactical Fleet Table --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-950 tracking-tight leading-none">Fleet Inventory</h3>
                <button class="btn-premium !py-3 !px-8 text-[10px] !rounded-2xl shadow-xl shadow-red-100">Commission Unit</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-8 py-6">Unit Signature</th>
                            <th class="px-8 py-6">Operative Name</th>
                            <th class="px-8 py-6">Assigned Facility</th>
                            <th class="px-8 py-6">Tactical Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ambulances as $amb)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-400 group-hover:text-red-500 transition-colors shadow-sm border border-slate-50">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-950">{{ $amb->plate_number }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">ResQ-Unit #{{ $amb->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm font-bold text-slate-600">{{ $amb->driver_name }}</td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-900">{{ $amb->hospital->name ?? 'Protocol Error' }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ trim(explode(',', $amb->hospital->address ?? '')[0]) }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <x-status-badge :status="$amb->status" />
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
