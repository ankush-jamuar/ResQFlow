<x-app-layout>
    <x-slot name="header">
        Family Health Ecosystem
    </x-slot>

    <div class="space-y-10" x-data="{ showModal: false }">
        <section class="reveal">
            <div class="glass-panel rounded-[3rem] p-12 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-rose-100/20 blur-3xl -z-10"></div>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                    <div>
                        <h2 class="text-4xl font-black text-slate-950 tracking-tight mb-2 uppercase">Your Protected Circle</h2>
                        <p class="text-slate-500 font-medium">Manage health profiles for your dependents to ensure rapid response context.</p>
                    </div>
                    <button @click="showModal = true" 
                            class="px-8 py-4 bg-slate-950 text-white text-[10px] font-black rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-100 uppercase tracking-[0.2em]">
                        Add Family Member
                    </button>
                </div>

                {{-- Members Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($members as $member)
                        <div class="glass-card !bg-white/60 p-8 rounded-[2.5rem] border-white/60 relative group hover:shadow-2xl transition-all">
                            <form action="{{ route('family.members.destroy', $member) }}" method="POST" class="absolute top-6 right-6">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-200 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>

                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 bg-rose-500 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-lg shadow-rose-100">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xl font-black text-slate-950 tracking-tight">{{ $member->name }}</p>
                                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">{{ $member->relation }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-slate-100/50">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Blood Group</span>
                                    <span class="text-xs font-black text-slate-950">{{ $member->blood_group ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Allergies</span>
                                    <p class="text-[11px] font-bold text-slate-600">{{ $member->allergies ?? 'None Reported' }}</p>
                                </div>
                                <div>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Conditions</span>
                                    <p class="text-[11px] font-bold text-slate-600">{{ $member->chronic_conditions ?? 'None Reported' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 flex flex-col items-center text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-6">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">No family members registered</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Add Member Modal --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-950/60 backdrop-blur-sm" x-cloak>
            <div @click.away="showModal = false" class="bg-white w-full max-w-xl rounded-[3rem] p-12 shadow-2xl reveal relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/10 blur-3xl"></div>
                
                <h3 class="text-3xl font-black text-slate-950 tracking-tight mb-8 uppercase">Register Dependent</h3>
                
                <form action="{{ route('family.members.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Full Name</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Relationship</label>
                            <select name="relation" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-rose-500">
                                <option value="Spouse">Spouse</option>
                                <option value="Child">Child</option>
                                <option value="Parent">Parent</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Blood Group</label>
                            <input type="text" name="blood_group" placeholder="e.g. O+" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-rose-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Emergency Contact</label>
                            <input type="text" name="contact_number" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Known Allergies</label>
                        <textarea name="allergies" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-rose-500" rows="2"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Chronic Conditions</label>
                        <textarea name="chronic_conditions" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-rose-500" rows="2"></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 py-4 bg-rose-500 text-white text-[10px] font-black rounded-2xl hover:bg-rose-600 transition-all uppercase tracking-widest shadow-xl shadow-rose-100">Add Member</button>
                        <button type="button" @click="showModal = false" class="px-8 py-4 bg-slate-100 text-slate-500 text-[10px] font-black rounded-2xl hover:bg-slate-200 transition-all uppercase tracking-widest">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
