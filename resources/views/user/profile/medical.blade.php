<x-app-layout>
    <x-slot name="header">
        Health & Family Ecosystem
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-10 pb-20" x-data="medicalEcosystem()">
        {{-- AI Command Center Header --}}
        <section class="glass-panel rounded-[3rem] p-12 border-white/40 shadow-2xl relative overflow-hidden flex flex-col lg:flex-row items-center gap-12 bg-slate-950 text-white">
            <div class="absolute -top-10 -left-10 w-64 h-64 bg-blue-500/10 blur-3xl -z-10 animate-pulse-soft"></div>
            
            <div class="relative group">
                <div class="absolute inset-0 bg-blue-500 blur-2xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                <div class="w-32 h-32 rounded-[2.5rem] bg-slate-900 border border-white/10 flex items-center justify-center text-blue-500 text-4xl font-black relative overflow-hidden">
                    {{ substr($user->name, 0, 1) }}
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                </div>
            </div>

            <div class="flex-1 text-center lg:text-left">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-4">
                    <h3 class="text-3xl font-black tracking-tight uppercase">{{ $user->name }}</h3>
                    <span class="px-4 py-1.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full text-[8px] font-black uppercase tracking-widest self-center lg:self-auto">AI Command Center Enabled</span>
                </div>
                <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                    @if($user->medicalProfile?->blood_group)
                        <div class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-xl">
                            <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Blood</span>
                            <span class="text-xs font-bold text-red-400">{{ $user->medicalProfile->blood_group }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-xl">
                        <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Preparedness</span>
                        <span class="text-xs font-bold text-emerald-400">{{ $risks['preparedness'] }}/100</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col gap-4 w-full lg:w-auto">
                <button @click="$dispatch('open-ai-modal')" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-blue-900/20 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Analyze New Report
                </button>
                <a href="#" class="px-8 py-4 bg-white/10 hover:bg-white/15 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all flex items-center justify-center gap-2 border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Medical ID Card
                </a>
            </div>
        </section>

        {{-- Ecosystem Navigation --}}
        <nav class="flex gap-2 p-2 bg-slate-100 rounded-[2rem] max-w-fit mx-auto border border-slate-200">
            <button @click="activeTab = 'intelligence'" :class="activeTab === 'intelligence' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Intelligence</button>
            <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Health History</button>
            <button @click="activeTab = 'family'" :class="activeTab === 'family' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Family</button>
            <button @click="activeTab = 'docs'" :class="activeTab === 'docs' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Documents</button>
        </nav>

        <div x-show="activeTab === 'intelligence'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-10">
            {{-- AI Intelligence Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Risk Radar --}}
                <div class="lg:col-span-2 glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl relative overflow-hidden">
                    <div class="flex justify-between items-center mb-10">
                        <h4 class="text-lg font-black text-slate-950 uppercase tracking-tight flex items-center gap-3">
                            <span class="w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                            AI Disease Risk Radar
                        </h4>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Last Synced: Just Now</span>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-12">
                        <div class="relative w-64 h-64 flex items-center justify-center">
                            {{-- Dynamic AI Radar Polygon --}}
                            @php
                                // Normalize risks to SVG coordinates (0-100 scale in 100x100 box)
                                // Center is 50,50. Max radius is 45.
                                $cardiacRadius = ($risks['cardiac'] / 100) * 45;
                                $diabetesRadius = ($risks['diabetes'] / 100) * 45;
                                $respiRadius = ($risks['respiratory'] / 100) * 45;

                                // Vertices (Approximate Triangle for simplicity)
                                $v1 = [50, 50 - $cardiacRadius]; // Top
                                $v2 = [50 + ($diabetesRadius * 0.866), 50 + ($diabetesRadius * 0.5)]; // Bottom Right
                                $v3 = [50 - ($respiRadius * 0.866), 50 + ($respiRadius * 0.5)]; // Bottom Left
                                $path = "M{$v1[0]} {$v1[1]} L{$v2[0]} {$v2[1]} L{$v3[0]} {$v3[1]} Z";
                            @endphp
                            
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="2,2"/>
                                <circle cx="50" cy="50" r="30" fill="none" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="2,2"/>
                                <circle cx="50" cy="50" r="15" fill="none" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="2,2"/>
                                
                                <!-- Dynamic Risk Polygon -->
                                <path d="{{ $path }}" fill="rgba(239, 68, 68, 0.2)" stroke="#ef4444" stroke-width="2" class="transition-all duration-700 ease-in-out"/>
                                
                                <line x1="50" y1="50" x2="50" y2="5" stroke="#cbd5e1" stroke-width="0.5"/>
                                <line x1="50" y1="50" x2="90" y2="75" stroke="#cbd5e1" stroke-width="0.5"/>
                                <line x1="50" y1="50" x2="10" y2="75" stroke="#cbd5e1" stroke-width="0.5"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center flex-col">
                                <span class="text-3xl font-black text-slate-950">{{ $risks['average'] }}%</span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase">Avg Risk</span>
                            </div>
                        </div>
                        
                        <div class="flex-1 space-y-6 w-full">
                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-slate-600">Cardiac Health</span>
                                    <span class="{{ $risks['cardiac'] > 40 ? 'text-red-500' : 'text-emerald-500' }}">{{ $risks['cardiac'] > 40 ? 'Elevated' : 'Stable' }} ({{ $risks['cardiac'] }}%)</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 rounded-full" style="width: {{ $risks['cardiac'] }}%"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-slate-600">Diabetes Profile</span>
                                    <span class="{{ $risks['diabetes'] > 30 ? 'text-red-500' : 'text-emerald-500' }}">{{ $risks['diabetes'] > 30 ? 'High' : 'Low' }} ({{ $risks['diabetes'] }}%)</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $risks['diabetes'] }}%"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-slate-600">Respiratory Stress</span>
                                    <span class="{{ $risks['respiratory'] > 30 ? 'text-blue-500' : 'text-slate-500' }}">{{ $risks['respiratory'] > 30 ? 'Alert' : 'Normal' }} ({{ $risks['respiratory'] }}%)</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $risks['respiratory'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- AI Intelligence Content --}}
                <div class="space-y-8">
                    @if($latestRecommendation)
                        <x-intelligence-card :recommendation="$latestRecommendation" />
                    @else
                        <div class="glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl bg-slate-50 text-center py-20">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6 text-slate-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No intelligence extracted yet.</p>
                            <p class="text-[10px] text-slate-400 mt-2">Upload a medical report to activate AI analysis.</p>
                        </div>
                    @endif
                    
                    @if(!empty($inherited))
                        <div class="p-8 bg-red-50 border border-red-100 rounded-[2.5rem] flex items-center gap-6">
                            <div class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-red-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">Hereditary Cluster Intelligence</p>
                                <p class="text-sm font-bold text-red-900 uppercase tracking-tight">Hereditary risk detected for: {{ implode(', ', array_keys($inherited)) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Medication Insights --}}
            <div class="glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl overflow-hidden relative">
                <div class="absolute top-0 right-0 p-10 opacity-10">
                    <svg class="w-32 h-32 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <h4 class="text-xl font-black text-slate-950 uppercase tracking-tight mb-8">Medication Intelligence Ecosystem</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($user->medications as $med)
                        <div class="p-6 rounded-[2rem] bg-slate-50 border border-slate-200 hover:border-blue-300 transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-blue-600 shadow-sm border border-slate-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-[8px] font-black uppercase">Active</span>
                            </div>
                            <h5 class="text-sm font-black text-slate-950 uppercase tracking-tight mb-1">{{ $med->name }}</h5>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">{{ $med->dosage }} • {{ $med->timing }}</p>
                            <div class="pt-4 border-t border-slate-200">
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-2">AI Insights</p>
                                <p class="text-[10px] text-slate-600 line-clamp-2">{{ $med->insights ?? 'No interactions detected with current regimen.' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center border-2 border-dashed border-slate-200 rounded-[2.5rem]">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No active medications registered.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'history'" x-transition class="space-y-10">
            {{-- Original Medical Form moved here --}}
            <form method="POST" action="{{ route('user.medical.update') }}" class="space-y-10">
                @csrf
                <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl space-y-6">
                        <h4 class="text-lg font-black text-slate-950 uppercase tracking-tight flex items-center gap-3 mb-6">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            Critical Health Data
                        </h4>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Blood Group</label>
                            <select name="blood_group" x-model="formData.blood_group" class="w-full glass-input text-slate-950 font-bold">
                                <option value="">Select</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $user->medicalProfile?->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" x-model="formData.date_of_birth" value="{{ old('date_of_birth', $user->medicalProfile?->date_of_birth?->format('Y-m-d')) }}" class="w-full glass-input text-slate-950 font-bold">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight_kg" x-model="formData.weight_kg" value="{{ old('weight_kg', $user->medicalProfile?->weight_kg) }}" class="w-full glass-input text-slate-950 font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Height (cm)</label>
                                <input type="number" step="0.1" name="height_cm" x-model="formData.height_cm" value="{{ old('height_cm', $user->medicalProfile?->height_cm) }}" class="w-full glass-input text-slate-950 font-bold">
                            </div>
                        </div>
                    </div>

                    <div class="glass-panel p-10 rounded-[3rem] border-white/40 shadow-2xl space-y-6">
                        <h4 class="text-lg font-black text-slate-950 uppercase tracking-tight flex items-center gap-3 mb-6">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            Medical Conditions
                        </h4>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Known Allergies</label>
                            <textarea name="allergies" x-model="formData.allergies" rows="2" class="w-full glass-input text-slate-950 font-medium" placeholder="E.g., Peanuts, Penicillin...">{{ old('allergies', $user->medicalProfile?->allergies) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Chronic Conditions</label>
                            <textarea name="chronic_conditions" x-model="formData.chronic_conditions" rows="2" class="w-full glass-input text-slate-950 font-medium" placeholder="E.g., Asthma, Hypertension...">{{ old('chronic_conditions', $user->medicalProfile?->chronic_conditions) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Current Medications</label>
                            <textarea name="current_medications" x-model="formData.current_medications" rows="2" class="w-full glass-input text-slate-950 font-medium" placeholder="E.g., Inhaler, Metformin...">{{ old('current_medications', $user->medicalProfile?->current_medications) }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="flex justify-end gap-4 pt-4">
                    <button type="submit" class="btn-premium !px-16 !py-4 text-[10px] !rounded-2xl shadow-2xl shadow-red-100 uppercase tracking-widest">Update Medical History</button>
                </div>
            </form>
        </div>

        <div x-show="activeTab === 'family'" x-transition class="space-y-10">
            {{-- Family / Emergency Contacts Ecosystem --}}
            <section class="glass-panel rounded-[3rem] p-12 border-white/40 shadow-2xl relative overflow-hidden bg-slate-950 text-white">
                <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-emerald-500/5 blur-3xl -z-10"></div>
                
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12">
                    <div>
                        <h3 class="text-3xl font-black tracking-tight uppercase mb-2">Family Health Network</h3>
                        <p class="text-xs text-slate-400 font-medium">Managing collective AI intelligence across your household.</p>
                    </div>
                    <button @click="$dispatch('open-family-modal')" class="w-full md:w-auto px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-emerald-900/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Family Member
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($user->familyMembers as $member)
                        <div class="p-8 rounded-[3rem] bg-white/5 border border-white/10 flex flex-col gap-6 relative group hover:bg-white/[0.07] transition-all">
                            <form action="{{ route('user.family.destroy', $member->id) }}" method="POST" class="absolute top-8 right-8 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Remove family member?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>

                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl font-black shadow-inner">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-lg font-black uppercase tracking-tight">{{ $member->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $member->relation }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                    <p class="text-[8px] text-slate-500 uppercase tracking-widest mb-1 font-black">Blood Group</p>
                                    <p class="text-xs font-black text-emerald-400">{{ $member->blood_group ?: '??' }}</p>
                                </div>
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                    <p class="text-[8px] text-slate-500 uppercase tracking-widest mb-1 font-black">AI Readiness</p>
                                    <p class="text-xs font-black text-blue-400">ACTIVE</p>
                                </div>
                            </div>

                            <button @click="openFamilyDrawer('{{ $member->id }}', '{{ $member->name }}')" class="w-full py-4 bg-white/10 hover:bg-white/20 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all border border-white/10 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                View Intelligence
                            </button>
                        </div>
                    @empty
                        <div class="p-12 rounded-[3rem] bg-white/5 border-2 border-dashed border-white/10 flex flex-col items-center justify-center col-span-full h-64 text-center">
                            <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mb-6 text-slate-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">No family network detected.</p>
                            <button @click="$dispatch('open-family-modal')" class="mt-4 text-emerald-400 hover:text-emerald-300 font-black text-[10px] uppercase tracking-widest">Connect First Member</button>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Family Intelligence Drawer --}}
            <div x-show="familyDrawer.open" 
                 class="fixed inset-0 z-[70] overflow-hidden" 
                 style="display: none;">
                <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="familyDrawer.open = false"></div>
                
                <section class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white shadow-2xl flex flex-col"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0">
                    
                    <div class="p-10 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-emerald-600 text-white rounded-[1.5rem] flex items-center justify-center text-2xl font-black shadow-xl shadow-emerald-200" x-text="familyDrawer.name.charAt(0)"></div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-950 uppercase tracking-tight" x-text="familyDrawer.name"></h3>
                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Dependent AI Profile</p>
                            </div>
                        </div>
                        <button @click="familyDrawer.open = false" class="w-12 h-12 bg-slate-200 hover:bg-slate-300 text-slate-600 rounded-2xl flex items-center justify-center transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-10 space-y-10">
                        <template x-if="familyDrawer.loading">
                            <div class="flex flex-col items-center justify-center h-64 space-y-4">
                                <svg class="animate-spin h-10 w-10 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Synchronizing Intelligence...</p>
                            </div>
                        </template>

                        <template x-if="!familyDrawer.loading && familyDrawer.data">
                            <div class="space-y-10">
                                {{-- Family Risk Radar --}}
                                <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-200">
                                    <div class="flex justify-between items-center mb-8">
                                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">AI Health Risks</h4>
                                        <span class="text-[8px] font-black text-emerald-600 uppercase bg-emerald-100 px-3 py-1 rounded-full">Calculated Just Now</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-6">
                                        <div class="text-center">
                                            <div class="text-xl font-black text-slate-950" x-text="familyDrawer.data.risks.cardiac + '%'"></div>
                                            <div class="text-[8px] font-bold text-slate-400 uppercase mt-1">Cardiac</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-black text-slate-950" x-text="familyDrawer.data.risks.diabetes + '%'"></div>
                                            <div class="text-[8px] font-bold text-slate-400 uppercase mt-1">Diabetes</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-black text-slate-950" x-text="familyDrawer.data.risks.respiratory + '%'"></div>
                                            <div class="text-[8px] font-bold text-slate-400 uppercase mt-1">Respi</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- AI Recommendation --}}
                                <div class="p-8 bg-blue-600 text-white rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                                    <p class="text-[9px] font-black uppercase tracking-widest mb-4 opacity-70">Primary Recommendation</p>
                                    <h5 class="text-sm font-black tracking-tight mb-4 leading-relaxed" x-text="familyDrawer.data.recommendation ? familyDrawer.data.recommendation.content : 'No specific alerts. Maintenance plan active.'"></h5>
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                        <span class="text-[8px] font-black uppercase tracking-widest" x-text="'Confidence: ' + (familyDrawer.data.recommendation ? Math.round(familyDrawer.data.recommendation.confidence_score * 100) : 95) + '%'"></span>
                                    </div>
                                </div>

                                {{-- Medications --}}
                                <div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Active Regimen</h4>
                                    <div class="space-y-4">
                                        <template x-for="med in familyDrawer.data.medications">
                                            <div class="p-6 bg-white border border-slate-100 rounded-2xl flex justify-between items-center">
                                                <div>
                                                    <p class="text-xs font-black text-slate-950 uppercase" x-text="med.name"></p>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase" x-text="med.dosage + ' • ' + (med.timing || 'As needed')"></p>
                                                </div>
                                                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="familyDrawer.data.medications.length === 0">
                                            <p class="text-[10px] text-slate-400 italic text-center py-4 border-2 border-dashed border-slate-100 rounded-2xl">No medications registered.</p>
                                        </template>
                                    </div>
                                </div>

                                {{-- Vault Count --}}
                                <div class="p-6 bg-slate-950 text-white rounded-2xl flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <span class="text-[10px] font-black uppercase tracking-widest" x-text="familyDrawer.data.reports_count + ' Documents in Vault'"></span>
                                    </div>
                                    <button @click="familyDrawer.open = false; activeTab = 'docs'" class="text-[8px] font-black uppercase tracking-widest text-emerald-400 hover:underline">View Vault</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="p-10 border-t border-slate-100 bg-slate-50/50">
                        <button @click="$dispatch('open-ai-modal'); familyDrawer.open = false;" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-emerald-900/10 flex items-center justify-center gap-2">
                            Analyze Member Document
                        </button>
                    </div>
                </section>
            </div>
        </div>

        <div x-show="activeTab === 'docs'" x-transition class="space-y-10">
            <section class="glass-panel rounded-[3rem] p-12 border-white/40 shadow-2xl">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-2xl font-black tracking-tight uppercase">Medical Documents Vault</h3>
                    <div class="flex gap-4">
                         <span class="px-4 py-2 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-black uppercase border border-slate-200">Private Storage Enabled</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($user->medicalReports->sortByDesc('created_at') as $report)
                        <div class="p-6 rounded-[2.5rem] bg-slate-50 border border-slate-200 hover:border-blue-300 transition-all flex flex-col gap-4 relative group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-blue-500 shadow-sm">
                                    @if($report->status === 'pending' || $report->status === 'processing')
                                        <svg class="w-6 h-6 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    @elseif($report->status === 'failed')
                                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-black text-slate-950 uppercase truncate">{{ $report->file_name }}</p>
                                        @if($report->familyMember)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-600 rounded text-[7px] font-black uppercase">{{ $report->familyMember->name }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ $report->created_at->format('M d, Y') }} • {{ $report->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            
                            @if($report->status === 'failed')
                                <div class="p-3 bg-red-50 rounded-xl border border-red-100">
                                    <p class="text-[8px] font-black text-red-600 uppercase tracking-widest mb-1">Extraction Error</p>
                                    <p class="text-[9px] text-red-700 leading-tight">{{ $report->error_reason ?? 'Unknown failure. Groq API timeout.' }}</p>
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase 
                                        {{ $report->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 
                                           ($report->status === 'failed' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600') }}">
                                        {{ $report->status }}
                                    </span>
                                    @if($report->status === 'completed' && $report->confidence_score > 0)
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">AI Accuracy: {{ round($report->confidence_score * 100) }}%</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4">
                                    <button @click="previewReport('{{ $report->getPreviewUrl() }}', '{{ $report->file_name }}')" class="text-[10px] font-black text-slate-600 hover:text-slate-950 uppercase tracking-widest transition-colors">Preview</button>
                                    <a href="{{ $report->getSignedUrl() }}" class="text-[10px] font-black text-blue-600 hover:text-blue-700 uppercase tracking-widest">Download</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center glass-panel rounded-[3rem] bg-slate-50 border-2 border-dashed border-slate-200">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 text-slate-300 shadow-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No documents in the vault.</p>
                            <button @click="$dispatch('open-ai-modal')" class="mt-4 text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Upload First Report</button>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Document Preview Modal --}}
            <div x-show="preview.open" 
                 class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 lg:p-12"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 style="display: none;">
                <div class="bg-white rounded-[3rem] w-full h-full max-w-6xl shadow-2xl relative flex flex-col overflow-hidden">
                    <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-950 uppercase tracking-tight" x-text="preview.fileName"></h3>
                                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Secure AI Preview Session</p>
                            </div>
                        </div>
                        <button @click="preview.open = false" class="w-12 h-12 bg-slate-200 hover:bg-slate-300 text-slate-600 rounded-2xl flex items-center justify-center transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex-1 bg-slate-100 relative">
                        <template x-if="preview.url">
                            <iframe :src="preview.url" class="w-full h-full border-none" allow="fullscreen"></iframe>
                        </template>
                        <div class="absolute inset-0 flex items-center justify-center -z-10">
                            <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-center gap-8">
                        <div class="flex items-center gap-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Encrypted Stream
                        </div>
                        <div class="flex items-center gap-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Self-Destructing Session
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Family Member Modal --}}
        <div x-data="{ open: false }" 
             @open-family-modal.window="open = true"
             x-show="open" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm"
             style="display: none;">
            <div @click.away="open = false" x-transition class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl relative">
                <button @click="open = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <h3 class="text-xl font-black text-slate-950 mb-6 uppercase tracking-tight">Register Family Member</h3>
                <form action="{{ route('user.family.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" name="name" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Relation</label>
                            <input type="text" name="relation" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950" placeholder="e.g., Spouse, Parent" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Blood Group</label>
                            <select name="blood_group" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950">
                                <option value="">Select</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}">{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Contact Number</label>
                            <input type="text" name="contact_number" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Allergies / Conditions</label>
                        <textarea name="allergies" rows="2" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950 text-sm"></textarea>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3 bg-slate-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all">Save Member</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- AI Parser Modal --}}
        <div x-data="{ open: false, targetFamilyId: '' }" 
             @open-ai-modal.window="open = true;"
             x-show="open" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm"
             style="display: none;">
            <div @click.away="!uploading ? open = false : null" x-transition class="bg-white rounded-[3rem] p-10 w-full max-w-md shadow-2xl relative text-center">
                <button x-show="!uploading" @click="open = false" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                
                <h3 class="text-2xl font-black text-slate-950 mb-3 tracking-tight uppercase">AI Report Analyzer</h3>
                <p class="text-xs text-slate-500 mb-8 font-medium">Extracting medical indicators across your ecosystem.</p>

                <div x-show="!uploading && !success" class="space-y-6">
                    <div class="text-left">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 text-center">Analyze For</label>
                        <select x-model="targetFamilyId" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950 font-black uppercase text-[10px] tracking-widest h-14 rounded-2xl">
                            <option value="">Me (Primary User)</option>
                            @foreach($user->familyMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->relation }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <input type="file" id="reportFile" class="hidden" @change="processAIReport($event.target.files[0], targetFamilyId)">
                        <label for="reportFile" class="cursor-pointer block w-full py-8 border-2 border-dashed border-slate-200 rounded-[2rem] hover:border-blue-500 hover:bg-blue-50 transition-all text-sm font-black text-slate-400 hover:text-blue-600 uppercase tracking-widest">
                            Drop Document Here
                        </label>
                    </div>
                </div>

                <div x-show="uploading" class="py-10">
                    <svg class="animate-spin w-12 h-12 text-blue-500 mx-auto mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest animate-pulse">Initializing Neural Core...</p>
                </div>

                <div x-show="success" class="py-8">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-lg font-black text-slate-900 mb-2 uppercase tracking-tight">Report Transferred</p>
                    <p class="text-[10px] text-slate-500 mb-8 font-medium">Analysis running in background. Intelligence will synchronize momentarily.</p>
                    <button @click="open = false; success = false;" class="w-full py-4 bg-slate-950 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">Dismiss</button>
                </div>
            </div>
        </div>

        {{-- Global Safety Guardian --}}
        <div class="max-w-6xl mx-auto mt-10 p-6 bg-slate-50 border border-slate-200 rounded-[2rem] text-center">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Medical Safety Layer Active</p>
            <p class="text-[10px] text-slate-600 italic">
                ResQFlow AI is an advisory tool. All extracted intelligence must be verified by a medical professional before taking action. 
                In case of emergency, always prioritize physical symptoms and contact emergency services immediately.
            </p>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('medicalEcosystem', () => ({
                activeTab: 'intelligence',
                formData: {
                    blood_group: '{{ $user->medicalProfile?->blood_group }}',
                    date_of_birth: '{{ $user->medicalProfile?->date_of_birth?->format("Y-m-d") }}',
                    weight_kg: '{{ $user->medicalProfile?->weight_kg }}',
                    height_cm: '{{ $user->medicalProfile?->height_cm }}',
                    allergies: `{{ $user->medicalProfile?->allergies }}`,
                    chronic_conditions: `{{ $user->medicalProfile?->chronic_conditions }}`,
                    current_medications: `{{ $user->medicalProfile?->current_medications }}`
                },
                preview: {
                    open: false,
                    url: '',
                    fileName: ''
                },
                familyDrawer: {
                    open: false,
                    loading: false,
                    id: null,
                    name: '',
                    data: null
                },
                uploading: false,
                success: false,
                
                previewReport(url, fileName) {
                    this.preview.url = url;
                    this.preview.fileName = fileName;
                    this.preview.open = true;
                },

                async openFamilyDrawer(id, name) {
                    this.familyDrawer.id = id;
                    this.familyDrawer.name = name;
                    this.familyDrawer.open = true;
                    this.familyDrawer.loading = true;
                    this.familyDrawer.data = null;

                    try {
                        const response = await fetch(`/family/members/${id}/health`);
                        const result = await response.json();
                        if (result.success) {
                            this.familyDrawer.data = result;
                        }
                    } catch (error) {
                        console.error('Failed to fetch family health data', error);
                    } finally {
                        this.familyDrawer.loading = false;
                    }
                },

                async processAIReport(file, familyId = null) {
                    if (!file) return;
                    this.uploading = true;
                    
                    const formData = new FormData();
                    formData.append('report', file);
                    if (familyId) formData.append('family_member_id', familyId);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route("medical.reports.store") }}', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (response.ok) {
                            this.uploading = false;
                            this.success = true;
                            // Optionally reload after a delay to show pending state
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } catch (error) {
                        alert('Report upload failed. Please try again.');
                        this.uploading = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
