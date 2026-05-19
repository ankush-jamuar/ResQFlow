<x-app-layout>
    <x-slot name="header">
        Mission Intelligence Feed
    </x-slot>

    <div class="space-y-10" x-data="adminEmergencies()">
        {{-- Tactical Filters --}}
        <section class="glass-panel rounded-[2.5rem] p-8 border-white/40 shadow-xl">
            <form method="GET" action="{{ route('admin.emergencies') }}" class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[240px]">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Status Protocol</p>
                    <div class="flex gap-2">
                        @foreach(['all', 'pending', 'en_route', 'completed', 'cancelled'] as $st)
                            <a href="{{ route('admin.emergencies', ['status' => $st === 'all' ? '' : $st]) }}" 
                               class="px-4 py-2 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all border
                               {{ (request('status', '') === ($st === 'all' ? '' : $st)) ? 'bg-slate-950 text-white border-slate-950 shadow-lg' : 'bg-white text-slate-400 border-slate-100 hover:bg-slate-50' }}">
                                {{ $st }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="w-px h-12 bg-slate-100 mx-4 hidden lg:block"></div>
                <button type="submit" class="bg-red-500 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-red-100 mt-auto">Sync Stream</button>
                <a href="{{ route('admin.audits') ?? '#' }}" class="bg-slate-900 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl mt-auto ml-auto">View Audit Logs</a>
            </form>
        </section>

        {{-- Global Intelligence Table --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-8 py-6">Mission Identity</th>
                            <th class="px-8 py-6">Tactical Operative</th>
                            <th class="px-8 py-6">Facility Signal</th>
                            <th class="px-8 py-6">Severity</th>
                            <th class="px-8 py-6">Status State</th>
                            <th class="px-8 py-6 text-center">Time Delta</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($emergencies as $em)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-950 uppercase tracking-tight">Mission #{{ str_pad($em->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $em->created_at->format('M d, H:i:s') }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-900 border border-white/10 flex items-center justify-center text-[10px] font-black text-red-500 uppercase">
                                            {{ substr($em->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <p class="text-xs font-bold text-slate-600">{{ $em->user->name ?? 'UNIDENTIFIED' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm font-black text-slate-900 uppercase tracking-tight">
                                    {{ $em->hospital->name ?? 'PROTOCOL BREACH' }}
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-4 py-1.5 text-[9px] font-black rounded-full uppercase tracking-widest
                                        {{ $em->severity === 'high' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-50 text-slate-500 border border-slate-100' }}">
                                        {{ $em->severity }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <x-status-badge :status="$em->status" />
                                </td>
                                <td class="px-8 py-6 text-center text-sm font-black text-slate-950 uppercase">
                                    {{ $em->duration_minutes ? $em->duration_minutes.'m' : '--' }}
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @if(in_array($em->status, ['pending', 'accepted']))
                                        <button @click="$dispatch('open-reassign-modal', { id: {{ $em->id }}, hospitalId: {{ $em->hospital_id ?? 'null' }} })" 
                                                class="px-4 py-2 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all">
                                            Reassign
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-10">
                {{ $emergencies->links() }}
            </div>
        </section>
    </div>

    {{-- Force Reassign Modal --}}
    <div x-data="{ open: false, emergencyId: null, currentHospitalId: null, processing: false }" 
         @open-reassign-modal.window="open = true; emergencyId = $event.detail.id; currentHospitalId = $event.detail.hospitalId; processing = false;"
         x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="open = false" x-transition class="bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl relative">
            <button @click="open = false" x-show="!processing" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            
            <h3 class="text-xl font-black text-slate-950 mb-2">Force Reassign Mission</h3>
            <p class="text-xs font-bold text-slate-500 mb-6">Select a new facility for Mission #<span x-text="emergencyId"></span></p>

            <form @submit.prevent="
                processing = true;
                try {
                    const res = await fetch('/admin/emergencies/' + emergencyId + '/reassign', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ hospital_id: $event.target.hospital_id.value })
                    });
                    
                    if (res.ok) {
                        open = false;
                        // Dashboard will auto-refresh via Echo
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Reassignment failed.');
                    }
                } catch (e) {
                    alert('Network error. Please try again.');
                } finally {
                    processing = false;
                }
            " class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Target Facility</label>
                    <select name="hospital_id" class="w-full glass-input !bg-slate-50 border-slate-200 text-slate-950 font-medium" required>
                        <option value="">-- Select Active Facility --</option>
                        @foreach($availableHospitals as $hosp)
                            <option value="{{ $hosp->id }}" x-show="currentHospitalId != {{ $hosp->id }}">{{ $hosp->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                    <p class="text-[10px] text-red-800 font-medium leading-relaxed">
                        <strong class="font-black block mb-1 uppercase tracking-widest">Authority Override</strong>
                        This action will immediately revoke the current assignment and place the mission back into the pending queue for the selected facility.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="open = false" :disabled="processing" class="px-6 py-3 text-xs font-black text-slate-500 hover:text-slate-900 uppercase tracking-widest transition-colors disabled:opacity-50">Cancel</button>
                    <button type="submit" :disabled="processing" class="px-6 py-3 bg-red-500 text-white text-xs font-black rounded-xl hover:bg-red-600 transition-colors uppercase tracking-widest shadow-lg shadow-red-500/30 disabled:opacity-50">
                        <span x-show="!processing">Execute Reassignment</span>
                        <span x-show="processing">Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminEmergencies', () => ({
                init() {
                    // Admins need to know when ANY emergency is created or updated
                    if (window.Echo) {
                        window.Echo.private('admin.emergencies')
                            .listen('.EmergencyCreated', (e) => {
                                window.location.reload();
                            })
                            .listen('.EmergencyStatusUpdated', (e) => {
                                window.location.reload();
                            });
                    }
                }
            }));
        });
    </script>
</x-app-layout>
