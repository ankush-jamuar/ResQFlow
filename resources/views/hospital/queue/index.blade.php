<x-app-layout>
    <x-slot name="header">
        Rescue Mission Queue
    </x-slot>

    <div class="space-y-10" x-data="hospitalOperations()">
        {{-- Queue Intelligence --}}
        <section class="glass-panel rounded-[2.5rem] p-8 border-white/40 shadow-xl flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex gap-4 overflow-x-auto pb-2 md:pb-0 no-scrollbar">
                @foreach(['all', 'pending', 'accepted', 'en_route', 'completed'] as $st)
                    <button class="px-6 py-2.5 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all border {{ $st === 'all' ? 'bg-slate-950 text-white border-slate-950 shadow-lg' : 'bg-white text-slate-400 border-slate-100' }}">
                        {{ $st }}
                    </button>
                @endforeach
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Auto-Dispatch Protocol</p>
                <div class="w-12 h-6 bg-red-500 rounded-full relative">
                    <div class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full"></div>
                </div>
            </div>
        </section>

        {{-- Tactical Queue Table --}}
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-8 py-6">Mission Token</th>
                            <th class="px-8 py-6">Operative Status</th>
                            <th class="px-8 py-6">Priority</th>
                            <th class="px-8 py-6">Operational State</th>
                            <th class="px-8 py-6 text-right">Command</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($emergencies as $em)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-950 uppercase tracking-tight">Mission #{{ str_pad($em->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $em->created_at->format('H:i:s') }} — Sector {{ rand(1, 10) }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 uppercase relative">
                                            {{ substr($em->familyMember->name ?? $em->user->name ?? 'G', 0, 1) }}
                                            @if($em->family_member_id)
                                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center text-[8px] text-white border-2 border-white" title="Family Member SOS">
                                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 015-4.906z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                            @if($em->user?->medicalProfile?->allergies)
                                                <div class="mt-2 flex items-center gap-2">
                                                    <span class="px-2 py-0.5 bg-red-500 text-white text-[8px] font-black rounded uppercase tracking-tighter">ALLERGY</span>
                                                    <span class="text-[9px] font-bold text-red-600 truncate max-w-[150px]">{{ $em->user->medicalProfile->allergies }}</span>
                                                </div>
                                            @endif
                                            @if($em->user && $em->user->medications()->where('is_active', true)->exists())
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($em->user->medications()->where('is_active', true)->take(2)->get() as $med)
                                                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 text-[7px] font-black rounded border border-blue-100 uppercase">{{ $med->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
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
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2 items-center" x-data="{ 
                                        processing: false, 
                                        ambulanceId: '{{ $em->ambulance_id ?? '' }}',
                                        async updateState(status) {
                                            this.processing = true;
                                            try {
                                                const res = await fetch(`{{ route('emergency.updateStatus', $em->id) }}`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({ status: status, ambulance_id: this.ambulanceId })
                                                });
                                                
                                                if (res.ok) {
                                                    window.location.reload();
                                                } else {
                                                    const err = await res.json();
                                                    alert(err.message || 'Operation failed.');
                                                }
                                            } catch (e) {
                                                alert('Network error. Please try again.');
                                            } finally {
                                                this.processing = false;
                                            }
                                        },
                                        async rejectMission() {
                                            if (!confirm('Are you sure you want to reject this mission and reassign it?')) return;
                                            
                                            this.processing = true;
                                            try {
                                                const res = await fetch(`{{ route('emergency.reject', $em->id) }}`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    }
                                                });
                                                
                                                if (res.ok) {
                                                    window.location.reload();
                                                } else {
                                                    const err = await res.json();
                                                    alert(err.message || 'Operation failed.');
                                                }
                                            } catch (e) {
                                                alert('Network error. Please try again.');
                                            } finally {
                                                this.processing = false;
                                            }
                                        }
                                    }">
                                        
                                        {{-- Pending Actions --}}
                                        @if($em->status === 'pending')
                                            <button @click="updateState('accepted')" :disabled="processing" class="px-5 py-2.5 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 disabled:opacity-50">
                                                <span x-show="!processing">Accept Mission</span>
                                                <span x-show="processing">Processing...</span>
                                            </button>
                                            
                                            <button @click="rejectMission()" :disabled="processing" class="px-5 py-2.5 bg-white border border-red-100 text-red-500 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-red-50 transition-all disabled:opacity-50">
                                                Reject
                                            </button>
                                        @endif

                                        {{-- Accepted -> Dispatched Actions (Requires Ambulance Assignment) --}}
                                        @if($em->status === 'accepted')
                                            <select x-model="ambulanceId" class="text-[10px] font-bold uppercase tracking-widest rounded-xl border-slate-200 focus:ring-slate-500 py-2 min-w-[150px]">
                                                <option value="">Select Unit...</option>
                                                @foreach(auth()->user()->hospital->ambulances()->where('status', 'available')->get() as $amb)
                                                    <option value="{{ $amb->id }}">Unit {{ $amb->plate_number }} ({{ $amb->type }})</option>
                                                @endforeach
                                            </select>
                                            <button @click="if(!ambulanceId) { alert('Please select an ambulance first.'); return; } updateState('dispatched')" :disabled="processing" class="px-5 py-2.5 bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all disabled:opacity-50">
                                                <span x-show="!processing">Dispatch Unit</span>
                                                <span x-show="processing">Processing...</span>
                                            </button>
                                        @endif

                                        {{-- Dispatched -> En Route --}}
                                        @if($em->status === 'dispatched')
                                            <button @click="updateState('en_route')" :disabled="processing" class="px-5 py-2.5 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all disabled:opacity-50">
                                                <span x-show="!processing">Start Mission / En Route</span>
                                                <span x-show="processing">Processing...</span>
                                            </button>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Unit {{ $em->ambulance->plate_number ?? 'Unknown' }}</span>
                                        @endif

                                        {{-- En Route -> Arrived --}}
                                        @if($em->status === 'en_route')
                                            <button @click="updateState('arrived')" :disabled="processing" class="px-5 py-2.5 bg-teal-600 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-teal-700 transition-all disabled:opacity-50">
                                                <span x-show="!processing">Mark Arrived</span>
                                                <span x-show="processing">Processing...</span>
                                            </button>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Unit {{ $em->ambulance->plate_number ?? 'Unknown' }}</span>
                                        @endif

                                        {{-- Arrived -> Completed --}}
                                        @if($em->status === 'arrived')
                                            <button @click="updateState('completed')" :disabled="processing" class="px-5 py-2.5 bg-emerald-500 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all disabled:opacity-50">
                                                <span x-show="!processing">Complete Mission</span>
                                                <span x-show="processing">Processing...</span>
                                            </button>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Unit {{ $em->ambulance->plate_number ?? 'Unknown' }}</span>
                                        @endif

                                        {{-- Completed / Cancelled / Rejected --}}
                                        @if(in_array($em->status, ['completed', 'cancelled', 'rejected']))
                                            <button disabled class="px-5 py-2.5 bg-slate-50 text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-xl cursor-not-allowed border border-slate-100">
                                                Mission Closed
                                            </button>
                                        @endif

                                    </div>
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('hospitalOperations', () => ({
                init() {
                    // Listen for any emergency updates globally to refresh state
                    if (window.Echo) {
                        window.Echo.private(`emergency.hospital.{{ auth()->user()->hospital->id ?? 0 }}`)
                            .listen('.EmergencyCreated', (e) => {
                                window.location.reload(); // Simple reload for now, can be optimized later
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
