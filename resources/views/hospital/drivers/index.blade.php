<x-app-layout>
    <x-slot name="header">
        Responder Intelligence
    </x-slot>

    <div class="space-y-10">
        {{-- Responder Grid --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($drivers as $driver)
                <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-xl group hover:bg-white transition-all duration-500">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-16 h-16 rounded-[2rem] bg-slate-900 border border-white/10 flex items-center justify-center text-red-500 text-2xl font-black">
                            {{ substr($driver->driver_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xl font-black text-slate-950 uppercase tracking-tight">{{ $driver->driver_name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Duty</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between items-center px-5 py-3 rounded-2xl bg-slate-50/50 border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Assigned Unit</p>
                            <p class="text-xs font-black text-slate-950 uppercase">{{ $driver->plate_number }}</p>
                        </div>
                        <div class="flex justify-between items-center px-5 py-3 rounded-2xl bg-slate-50/50 border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Missions Today</p>
                            <p class="text-xs font-black text-slate-950">{{ rand(2, 8) }}</p>
                        </div>
                        <div class="flex justify-between items-center px-5 py-3 rounded-2xl bg-slate-50/50 border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                            <x-status-badge :status="$driver->status" />
                        </div>
                    </div>

                    <button class="w-full py-4 bg-slate-950 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">View Operative Profile</button>
                </div>
            @endforeach
        </section>

        {{-- Add Operative CTA --}}
        <section class="glass-panel rounded-[3rem] p-12 border-dashed border-2 border-slate-200 flex flex-col items-center justify-center text-center group hover:border-red-200 transition-all cursor-pointer">
            <div class="w-16 h-16 bg-slate-50 rounded-[1.5rem] flex items-center justify-center text-slate-300 group-hover:bg-red-50 group-hover:text-red-500 transition-all mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <h3 class="text-xl font-black text-slate-400 group-hover:text-slate-950 transition-colors uppercase tracking-tighter">Enlist New Responder</h3>
            <p class="text-sm text-slate-400 mt-2">Expand your medical response network</p>
        </section>
    </div>
</x-app-layout>
