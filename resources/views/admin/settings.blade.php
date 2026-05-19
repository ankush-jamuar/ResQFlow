<x-app-layout>
    <x-slot name="header">
        System Infrastructure
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-10 pb-20">
        {{-- Section 1: Dispatch Control --}}
        <section class="glass-panel rounded-[3rem] p-12 border-white/40 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-red-500/5 blur-3xl -z-10"></div>
            
            <div class="flex items-center gap-6 mb-12">
                <div class="w-16 h-16 bg-red-50 rounded-[1.5rem] flex items-center justify-center text-red-500 border border-red-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-950 tracking-tight">Dispatch Intelligence</h3>
                    <p class="text-slate-500 font-medium text-sm">Configure automated response protocols</p>
                </div>
            </div>

            <div class="space-y-8">
                <div class="flex items-center justify-between p-6 rounded-[2rem] bg-slate-50/50 border border-slate-100">
                    <div>
                        <p class="text-sm font-black text-slate-950">AI Routing Core</p>
                        <p class="text-xs text-slate-500 mt-1">Automatically select hospitals based on real-time traffic and capacity.</p>
                    </div>
                    <div class="relative inline-flex items-center cursor-pointer">
                        <div class="w-12 h-6 bg-red-500 rounded-full"></div>
                        <div class="absolute right-1 w-4 h-4 bg-white rounded-full transition-all"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-6 rounded-[2rem] bg-slate-50/50 border border-slate-100">
                    <div>
                        <p class="text-sm font-black text-slate-950">High-Priority Intercept</p>
                        <p class="text-xs text-slate-500 mt-1">Override standard queue for life-threatening Category 1 calls.</p>
                    </div>
                    <div class="relative inline-flex items-center cursor-pointer">
                        <div class="w-12 h-6 bg-slate-200 rounded-full"></div>
                        <div class="absolute left-1 w-4 h-4 bg-white rounded-full transition-all"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">Max Dispatch Radius (km)</label>
                        <input type="number" value="15" class="glass-input w-full font-bold text-slate-950">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-2">Unit Timeout (s)</label>
                        <input type="number" value="60" class="glass-input w-full font-bold text-slate-950">
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 2: Notifications --}}
        <section class="glass-panel rounded-[3rem] p-12 border-white/40 shadow-2xl relative overflow-hidden">
            <div class="flex items-center gap-6 mb-12">
                <div class="w-16 h-16 bg-blue-50 rounded-[1.5rem] flex items-center justify-center text-blue-500 border border-blue-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-950 tracking-tight">Signal Protocols</h3>
                    <p class="text-slate-500 font-medium text-sm">Manage emergency broadcast alerts</p>
                </div>
            </div>

            <div class="space-y-6">
                @foreach(['Push Notifications', 'Real-time Dashboards', 'SMS Fallback', 'Satellite Relay'] as $protocol)
                    <div class="flex items-center justify-between p-4 px-8 rounded-2xl bg-white/40 border border-white/60">
                        <span class="text-xs font-black text-slate-950 uppercase tracking-widest">{{ $protocol }}</span>
                        <div class="w-10 h-5 bg-emerald-500/20 border border-emerald-500/40 rounded-full relative">
                            <div class="absolute right-1 top-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex justify-end gap-4">
            <button class="px-10 py-4 bg-white border border-slate-100 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-50 transition-all">Revert to Defaults</button>
            <button class="btn-premium !px-12 !py-4 text-[10px] !rounded-2xl shadow-2xl shadow-red-100">Commit Changes</button>
        </div>
    </div>
</x-app-layout>
