<x-app-layout>
    <x-slot name="header">
        Medical Intelligence Hub
    </x-slot>

    <div class="space-y-10" x-data="{ uploading: false }">
        <section class="reveal">
            <div class="glass-panel rounded-[3rem] p-12 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/20 blur-3xl -z-10"></div>
                
                <div class="flex justify-between items-center mb-12">
                    <div>
                        <h2 class="text-4xl font-black text-slate-950 tracking-tight mb-2 uppercase">Your Health Dossier</h2>
                        <p class="text-slate-500 font-medium">Upload medical reports for instant AI health analysis & intelligence.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full text-[10px] font-black uppercase tracking-widest">AI Enabled</span>
                    </div>
                </div>

                {{-- Upload Zone --}}
                <form action="{{ route('medical.reports.store') }}" method="POST" enctype="multipart/form-data" 
                      class="mb-12" @submit="uploading = true">
                    @csrf
                    <div class="relative group">
                        <input type="file" name="report" id="report-upload" class="hidden" 
                               onchange="this.form.submit()" accept=".pdf,.jpg,.jpeg,.png">
                        <label for="report-upload" 
                               class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-slate-200 rounded-[2.5rem] bg-slate-50/50 hover:bg-white hover:border-blue-500 hover:shadow-2xl hover:shadow-blue-100 transition-all cursor-pointer group">
                            
                            <template x-if="!uploading">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 bg-blue-500 text-white rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-lg shadow-blue-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <p class="text-lg font-black text-slate-950 uppercase tracking-tight">Upload New Report</p>
                                    <p class="text-xs font-medium text-slate-400 mt-1">PDF, JPG, PNG (Max 10MB)</p>
                                </div>
                            </template>

                            <template x-if="uploading">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 border-4 border-blue-100 border-t-blue-500 rounded-full animate-spin mb-4"></div>
                                    <p class="text-lg font-black text-slate-950 uppercase tracking-tight animate-pulse">Processing Intelligence...</p>
                                </div>
                            </template>
                        </label>
                    </div>
                </form>

                {{-- Reports List --}}
                <div class="space-y-6">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Analyzed Intelligence</h3>
                    
                    @forelse($reports as $report)
                        <div class="glass-card !bg-white/60 p-8 rounded-[2.5rem] border-white/60 flex items-center justify-between group hover:shadow-xl transition-all">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 bg-slate-950 text-white rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xl font-black text-slate-950 tracking-tight">{{ $report->file_name }}</p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $report->created_at->format('M d, Y') }}</p>
                                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                        <p class="text-[9px] font-bold text-blue-500 uppercase tracking-widest">AI Confidence: {{ number_format($report->confidence_score * 100, 1) }}%</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                @if($report->status === 'completed')
                                    <a href="{{ route('medical.reports.show', $report) }}" 
                                       class="px-6 py-3 bg-slate-950 text-white text-[10px] font-black rounded-2xl hover:bg-slate-800 transition-all uppercase tracking-widest">View Analysis</a>
                                @elseif($report->status === 'processing')
                                    <span class="flex items-center gap-2 text-blue-500">
                                        <div class="w-3 h-3 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Analyzing...</span>
                                    </span>
                                @else
                                    <span class="px-6 py-3 bg-slate-100 text-slate-400 text-[10px] font-black rounded-2xl uppercase tracking-widest">Pending AI</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-20 flex flex-col items-center text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-6">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">No intelligence reports available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
