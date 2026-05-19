<x-app-layout>
    <x-slot name="header">
        Mission Archives
    </x-slot>

    <div class="space-y-10">
        {{-- History Summary --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Total Missions</p>
                <p class="text-4xl font-black text-slate-950 tracking-tighter">{{ $history->total() }}</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Protocol Secured</span>
                </div>
            </div>
            <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-xl bg-slate-950 text-white">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Success Rate</p>
                <p class="text-4xl font-black text-emerald-500 tracking-tighter">100%</p>
                <p class="mt-4 text-[9px] font-bold text-slate-400 uppercase tracking-widest">Optimized Response Path</p>
            </div>
            <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Avg Response</p>
                <p class="text-4xl font-black text-slate-950 tracking-tighter">8.2<span class="text-lg text-slate-400 ml-1">m</span></p>
                <p class="mt-4 text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Global Benchmark</p>
            </div>
        </section>

        {{-- Archive Feed --}}
        <section class="space-y-6">
            @forelse($history as $em)
                <div class="glass-panel p-8 rounded-[3rem] border-white/40 shadow-2xl group hover:bg-white transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-slate-100 opacity-0 group-hover:opacity-100 blur-3xl transition-opacity"></div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-[1.5rem] bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 group-hover:text-red-500 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-black text-slate-950 tracking-tight uppercase">Mission #{{ str_pad($em->id, 6, '0', STR_PAD_LEFT) }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $em->created_at->format('M d, Y') }}</p>
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <p class="text-[9px] font-black text-slate-950 uppercase tracking-widest">{{ $em->hospital->name ?? 'Protocol Alpha' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 w-full md:w-auto">
                            <div class="text-right hidden sm:block">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Response Time</p>
                                <p class="text-sm font-black text-slate-950">{{ $em->duration_minutes ?? '--' }} Minutes</p>
                            </div>
                            <x-status-badge :status="$em->status" />
                            <button class="p-3 bg-white border border-slate-100 text-slate-400 hover:text-slate-950 rounded-xl transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center glass-panel rounded-[3rem] border-dashed border-2 border-slate-200">
                    <p class="text-slate-400 font-black uppercase tracking-widest">No Historical Missions Found</p>
                    <p class="text-sm text-slate-400 mt-2">Your mission archive is currently empty.</p>
                </div>
            @endforelse

            <div class="mt-10">
                {{ $history->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
