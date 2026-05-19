<x-app-layout>
    <x-slot name="header">
        Operational Resources
    </x-slot>

    <div class="space-y-10">
        {{-- Resource Metrics --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Total SKUs</p>
                <p class="text-3xl font-black text-slate-950">{{ $resources->count() }}</p>
                <p class="mt-4 text-[9px] font-black text-emerald-500 uppercase tracking-widest">In Stock</p>
            </div>
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl bg-slate-950 text-white">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Critical Shortage</p>
                <p class="text-3xl font-black text-red-500">{{ $resources->where('quantity', '<', 5)->count() }}</p>
                <p class="mt-4 text-[9px] font-black text-red-400 uppercase tracking-widest animate-pulse">Action Required</p>
            </div>
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Next Resupply</p>
                <p class="text-2xl font-black text-slate-950 uppercase tracking-tight">Tomorrow</p>
                <p class="mt-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Scheduled Sector 04</p>
            </div>
            <div class="glass-panel p-8 rounded-[2.5rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Inventory Value</p>
                <p class="text-3xl font-black text-slate-950">High</p>
                <p class="mt-4 text-[9px] font-black text-slate-400 uppercase tracking-widest italic uppercase">Nominal Coverage</p>
            </div>
        </section>

        {{-- Resource Management Table --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-2xl font-black text-slate-950 tracking-tight">Tactical Inventory</h3>
                <button class="btn-premium !py-3 !px-8 text-[10px] !rounded-2xl shadow-xl shadow-red-100">Provision Resource</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-8 py-6">Resource Profile</th>
                            <th class="px-8 py-6">Category</th>
                            <th class="px-8 py-6 text-center">Available Units</th>
                            <th class="px-8 py-6 text-right">Supply Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($resources as $res)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-400 group-hover:text-red-500 transition-colors shadow-sm border border-slate-50">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                        </div>
                                        <p class="text-sm font-black text-slate-950">{{ $res->name }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-4 py-1.5 bg-slate-100 text-slate-600 rounded-full text-[9px] font-black uppercase tracking-widest">
                                        {{ $res->type }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <p class="text-lg font-black {{ $res->quantity < 5 ? 'text-red-500' : 'text-slate-950' }}">{{ $res->quantity }}</p>
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $res->quantity < 5 ? 'CRITICAL' : 'RESERVE OK' }}</p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-3">
                                        <button class="px-5 py-2.5 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">Restock</button>
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
        </section>
    </div>
</x-app-layout>
