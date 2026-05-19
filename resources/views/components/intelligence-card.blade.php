<div class="glass-panel !bg-white/80 rounded-[3rem] p-10 shadow-2xl relative overflow-hidden group" 
     x-data="{ level: 1 }">
    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 blur-3xl -z-10 group-hover:scale-150 transition-transform duration-700"></div>
    
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Health Intelligence</h3>
            </div>
            <h2 class="text-3xl font-black text-slate-950 tracking-tight">{{ $recommendation->content }}</h2>
        </div>
        <div class="flex flex-col items-end">
            <div class="flex items-center gap-2 px-4 py-1.5 bg-slate-950 text-white rounded-full">
                <div class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></div>
                <span class="text-[10px] font-black uppercase tracking-widest">{{ number_format($recommendation->confidence_score * 100, 0) }}% Confidence</span>
            </div>
            <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">{{ str_replace('_', ' ', $recommendation->confidence_language) }}</p>
        </div>
    </div>

    {{-- Level 1: Simple Summary --}}
    <div x-show="level >= 1" x-transition class="mb-8">
        <p class="text-lg font-medium text-slate-600 leading-relaxed italic">
            "{{ $recommendation->rationale }}"
        </p>
    </div>

    {{-- Level 2: "Why this?" (Corroborating Factors) --}}
    <div x-show="level >= 2" x-transition class="mb-8 border-t border-slate-100 pt-8">
        <h4 class="text-[10px] font-black text-slate-950 uppercase tracking-widest mb-6">Corroborating Clinical Evidence</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($recommendation->supporting_evidence as $evidence)
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-600 leading-tight">{{ $evidence }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Level 3: Historical Stability --}}
    <div x-show="level >= 3" x-transition class="mb-8 border-t border-slate-100 pt-8">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-[10px] font-black text-slate-950 uppercase tracking-widest">Reasoning Consistency Trace</h4>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">Stable Baseline</span>
        </div>
        <p class="text-xs text-slate-500 font-medium leading-relaxed">
            This insight has remained consistent across {{ $recommendation->parent_recommendation_id ? 'multiple' : 'the latest' }} analysis cycles. Stability Hash: <code class="bg-slate-100 px-2 py-0.5 rounded">{{ substr($recommendation->stability_hash, 0, 8) }}</code>
        </p>
    </div>

    {{-- Controls --}}
    <div class="flex items-center gap-4 mt-8 pt-8 border-t border-slate-50">
        <button @click="level = level < 3 ? level + 1 : 1" 
                class="px-6 py-2.5 bg-slate-100 text-slate-600 text-[10px] font-black rounded-full hover:bg-slate-200 transition-all uppercase tracking-widest">
            <span x-text="level < 3 ? 'Deepen Analysis' : 'Show Summary Only'"></span>
        </button>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
            Last Synced: {{ $recommendation->created_at->diffForHumans() }}
        </p>
    </div>
</div>
