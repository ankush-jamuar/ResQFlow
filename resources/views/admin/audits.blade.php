<x-app-layout>
    <x-slot name="header">
        Emergency Activity Audit Log
    </x-slot>

    <div class="space-y-10">
        <section class="glass-panel rounded-[3rem] p-10 border-white/40 shadow-2xl overflow-hidden">
            <h3 class="text-2xl font-black text-slate-950 uppercase tracking-tight mb-8">System Audit Trail</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-6 py-4">Timestamp</th>
                            <th class="px-6 py-4">Mission ID</th>
                            <th class="px-6 py-4">Actor</th>
                            <th class="px-6 py-4">Action</th>
                            <th class="px-6 py-4">State Transition</th>
                            <th class="px-6 py-4">System Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($audits as $audit)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                    {{ $audit->created_at->format('M d, H:i:s') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-black text-slate-950 uppercase tracking-tight">#{{ str_pad($audit->emergency_request_id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($audit->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[8px] font-black uppercase">
                                                {{ substr($audit->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-700">{{ $audit->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-500">SYSTEM AUTO</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-600">
                                        {{ str_replace('_', ' ', $audit->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($audit->old_state && $audit->new_state)
                                        <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                                            <span class="line-through">{{ $audit->old_state }}</span>
                                            <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            <span class="text-slate-950">{{ $audit->new_state }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-600 max-w-xs truncate">
                                    {{ $audit->notes ?? 'No additional context' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    No audit logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $audits->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
