<x-app-layout>
    <x-slot name="header">
        Facility Intelligence
    </x-slot>

    <div class="space-y-10">
        {{-- Controls --}}
        <section class="flex flex-col md:flex-row justify-between items-center gap-6 glass-panel rounded-[2.5rem] p-8 border-white/40 shadow-xl">
            <form method="GET" action="{{ route('admin.hospitals') }}" class="flex flex-1 gap-4 w-full">
                <div class="relative flex-1">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Facilities..." class="glass-input !py-3 !pl-14 text-sm font-bold text-slate-950 w-full">
                </div>
                <select name="city" class="glass-input !py-3 !px-6 text-sm font-bold text-slate-950">
                    <option value="">Global Coverage</option>
                    @foreach($citiesList as $city)
                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-slate-950 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-slate-200">Scan</button>
            </form>
            <button class="btn-premium !py-3 !px-8 text-[10px] !rounded-2xl">Register Facility</button>
        </section>

        {{-- Intelligence Table --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-8 py-6">Facility Identity</th>
                            <th class="px-8 py-6">Operative Assigned</th>
                            <th class="px-8 py-6 text-center">Historical Load</th>
                            <th class="px-8 py-6 text-right">Operational Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($hospitals as $hosp)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center transition-colors shadow-sm {{ $hosp->is_suspended ? 'text-slate-200' : 'text-slate-400 group-hover:text-red-500' }}">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black {{ $hosp->is_suspended ? 'text-slate-400 line-through' : 'text-slate-950' }}">
                                                {{ $hosp->name }}
                                                @if($hosp->is_suspended)
                                                    <span class="ml-2 text-[8px] bg-red-500 text-white px-2 py-0.5 rounded-full uppercase tracking-widest align-middle">Suspended</span>
                                                @endif
                                            </p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $hosp->address }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 uppercase">
                                            {{ substr($hosp->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <p class="text-xs font-bold text-slate-600">{{ $hosp->user->name ?? 'Protocol Error' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="px-4 py-1.5 bg-slate-100 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                            {{ $hosp->emergency_requests_count }} Signals
                                        </span>
                                        <div class="flex gap-2 mt-1">
                                            <span class="text-[9px] font-bold text-{{ $hosp->reliabilityColor() }}-500" title="Reliability Score">
                                                {{ $hosp->reliability_score }}%
                                            </span>
                                            @if($hosp->warning_points > 0)
                                                <span class="text-[9px] font-bold text-red-500" title="Warning Points">
                                                    {{ $hosp->warning_points }} ⚠️
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        @if($hosp->user && !$hosp->is_suspended)
                                            <form method="POST" action="{{ route('admin.loginAsHospital') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $hosp->user->id }}">
                                                <button type="submit" class="px-4 py-2 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all">Impersonate</button>
                                            </form>
                                        @endif
                                        
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open" @click.away="open = false" class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-slate-950 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                            </button>
                                            <div x-show="open" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white border border-slate-100 ring-1 ring-black ring-opacity-5 z-10 overflow-hidden">
                                                <div class="py-1">
                                                    @if($hosp->is_suspended)
                                                        <form method="POST" action="{{ route('admin.hospitals.reinstate', $hosp->id) }}">
                                                            @csrf
                                                            <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-bold text-emerald-600 hover:bg-emerald-50 uppercase tracking-widest">Reinstate Facility</button>
                                                        </form>
                                                    @else
                                                        <button @click="open = false; $dispatch('open-warning-modal', { id: {{ $hosp->id }}, name: '{{ $hosp->name }}' })" class="w-full text-left px-4 py-2 text-[10px] font-bold text-amber-600 hover:bg-amber-50 uppercase tracking-widest">Issue Warning</button>
                                                        <form method="POST" action="{{ route('admin.hospitals.suspend', $hosp->id) }}">
                                                            @csrf
                                                            <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-bold text-red-600 hover:bg-red-50 uppercase tracking-widest" onclick="return confirm('Are you sure you want to suspend this facility? They will not receive new emergencies.')">Suspend Facility</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-10">
                {{ $hospitals->links() }}
            </div>
        </section>
    </div>

    {{-- Issue Warning Modal --}}
    <div x-data="{ open: false, hospitalId: null, hospitalName: '' }" 
         @open-warning-modal.window="open = true; hospitalId = $event.detail.id; hospitalName = $event.detail.name;"
         x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="open = false" x-transition class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl relative">
            <button @click="open = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            
            <h3 class="text-xl font-black text-slate-950 mb-2">Issue Official Warning</h3>
            <p class="text-xs font-bold text-slate-500 mb-6">Facility: <span class="text-slate-900" x-text="hospitalName"></span></p>

            <form :action="'/admin/hospitals/' + hospitalId + '/warn'" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Warning Reason / Details</label>
                    <textarea name="reason" rows="4" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950 font-medium" placeholder="E.g., Repeated failure to dispatch units within 5 minutes..." required></textarea>
                </div>
                
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-[10px] text-amber-800 font-medium leading-relaxed">
                        <strong class="font-black block mb-1 uppercase tracking-widest">Protocol Notice</strong>
                        Issuing this warning will add 1 warning point and reduce the facility's reliability score by 5%. The facility admin will be notified immediately.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="open = false" class="px-6 py-3 text-xs font-black text-slate-500 hover:text-slate-900 uppercase tracking-widest transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-red-500 text-white text-xs font-black rounded-xl hover:bg-red-600 transition-colors uppercase tracking-widest shadow-lg shadow-red-500/30">Issue Warning</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
