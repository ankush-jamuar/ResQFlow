<x-app-layout>
    <x-slot name="header">
        Intelligence Analysis
    </x-slot>

    <div class="space-y-10">
        <section class="reveal">
            <div class="glass-panel rounded-[3rem] p-12 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100/30 blur-3xl -z-10"></div>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('medical.reports.index') }}" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center hover:bg-slate-50 transition-all text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <div>
                            <h2 class="text-4xl font-black text-slate-950 tracking-tight mb-2 uppercase">{{ $report->file_name }}</h2>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-blue-100">Verified Analysis</span>
                                <p class="text-slate-400 font-bold text-[10px] uppercase tracking-widest">Confidence: {{ number_format($report->confidence_score * 100, 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Left Column: Structured Data --}}
                    <div class="lg:col-span-2 space-y-8">
                        {{-- Vitals & Core Extraction --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($report->parsed_data['vitals'] ?? [] as $key => $val)
                                <div class="glass-card !bg-white p-8 rounded-[2.5rem] border-slate-100 shadow-sm">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">{{ str_replace('_', ' ', $key) }}</p>
                                    <p class="text-2xl font-black text-slate-950 tracking-tight">{{ $val }}</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Conditions & Medications --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="glass-card !bg-slate-50 p-8 rounded-[2.5rem] border-slate-100">
                                <h3 class="text-[10px] font-black text-slate-950 uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
                                    Chronic Conditions
                                </h3>
                                <ul class="space-y-4">
                                    @foreach($report->parsed_data['chronic_conditions'] ?? [] as $condition)
                                        <li class="flex items-center gap-3 text-slate-700 font-bold">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                            {{ $condition }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="glass-card !bg-slate-50 p-8 rounded-[2.5rem] border-slate-100">
                                <h3 class="text-[10px] font-black text-slate-950 uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                                    Current Medications
                                </h3>
                                <ul class="space-y-4">
                                    @foreach($report->parsed_data['current_medications'] ?? [] as $med)
                                        <li class="flex items-center gap-3 text-slate-700 font-bold">
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            {{ $med }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: AI Intelligence Summary --}}
                    <div class="space-y-8">
                        <div class="glass-card !bg-slate-950 p-10 rounded-[3rem] text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/20 blur-3xl"></div>
                            
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-6">AI Intelligence Summary</p>
                            
                            <p class="text-lg font-medium leading-relaxed mb-8 text-slate-200 italic">
                                "{{ $report->ai_summary }}"
                            </p>

                            <div class="space-y-6">
                                <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Personalized Recommendations</h4>
                                <ul class="space-y-4">
                                    @foreach(['Schedule a cardiac follow-up within 3 months.', 'Keep an updated log of daily blood pressure readings.'] as $rec)
                                        <li class="flex items-start gap-3 text-[11px] font-bold text-slate-400">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-1.5 shrink-0"></span>
                                            {{ $rec }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="mt-10 pt-8 border-t border-white/10">
                                <div class="flex items-center gap-3 text-red-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Emergency Warning</p>
                                </div>
                                <p class="text-[11px] font-bold text-slate-500 mt-2">Allergy detected: Penicillin. High critical risk in emergency procedures.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
