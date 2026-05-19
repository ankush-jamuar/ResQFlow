@props(['status'])

@php
    $configs = [
        'pending'    => ['bg' => 'bg-amber-50',  'text' => 'text-amber-600',  'border' => 'border-amber-100',  'pulse' => 'bg-amber-500'],
        'accepted'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-600',   'border' => 'border-blue-100',   'pulse' => 'bg-blue-500'],
        'dispatched' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-100', 'pulse' => 'bg-indigo-500'],
        'en_route'   => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-100', 'pulse' => 'bg-purple-500'],
        'arrived'    => ['bg' => 'bg-teal-50',   'text' => 'text-teal-600',   'border' => 'border-teal-100',   'pulse' => 'bg-teal-500'],
        'completed'  => ['bg' => 'bg-emerald-50','text' => 'text-emerald-600','border' => 'border-emerald-100','pulse' => 'bg-emerald-500'],
        'cancelled'  => ['bg' => 'bg-red-50',    'text' => 'text-red-600',    'border' => 'border-red-100',    'pulse' => 'bg-red-500'],
        'rejected'   => ['bg' => 'bg-slate-950', 'text' => 'text-slate-400',  'border' => 'border-slate-800',  'pulse' => 'bg-slate-700'],
    ];
    $config = $configs[$status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'border' => 'border-slate-100', 'pulse' => 'bg-slate-400'];
@endphp


<span 
    {{ $attributes->merge(['class' => "px-4 py-1.5 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.15em] rounded-full border shadow-sm transition-all duration-300 " . $config['bg'] . " " . $config['text'] . " " . $config['border']]) }}
    x-data="{ 
        localStatus: '{{ $status }}',
        get effectiveStatus() {
            return (typeof currentStatus !== 'undefined') ? currentStatus : this.localStatus;
        },
        get config() {
            const configs = {
                'pending':    {bg: 'bg-amber-50',  text: 'text-amber-600',  border: 'border-amber-100',  pulse: 'bg-amber-500'},
                'accepted':   {bg: 'bg-blue-50',   text: 'text-blue-600',   border: 'border-blue-100',   pulse: 'bg-blue-500'},
                'dispatched': {bg: 'bg-indigo-50', text: 'text-indigo-600', border: 'border-indigo-100', pulse: 'bg-indigo-500'},
                'en_route':   {bg: 'bg-purple-50', text: 'text-purple-600', border: 'border-purple-100', pulse: 'bg-purple-500'},
                'arrived':    {bg: 'bg-teal-50',   text: 'text-teal-600',   border: 'border-teal-100',   pulse: 'bg-teal-500'},
                'completed':  {bg: 'bg-emerald-50',text: 'text-emerald-600',border: 'border-emerald-100',pulse: 'bg-emerald-500'},
                'cancelled':  {bg: 'bg-red-50',    text: 'text-red-600',    border: 'border-red-100',    pulse: 'bg-red-500'},
                'rejected':   {bg: 'bg-slate-950', text: 'text-slate-400',  border: 'border-slate-800',  pulse: 'bg-slate-700'}
            };
            return configs[this.effectiveStatus] || {bg: 'bg-slate-50', text: 'text-slate-500', border: 'border-slate-100', pulse: 'bg-slate-400'};
        }
    }"
    :class="`${config.bg} ${config.text} ${config.border}`"
>
    <span 
        class="w-1.5 h-1.5 rounded-full {{ $config['pulse'] }} {{ in_array($status, ['pending', 'en_route', 'dispatched']) ? 'animate-pulse' : '' }}"
        :class="`${config.pulse} ${['pending', 'en_route', 'dispatched'].includes(effectiveStatus) ? 'animate-pulse' : ''}`"
    ></span>
    <span x-text="effectiveStatus.replace('_', ' ')">{{ str_replace('_', ' ', $status) }}</span>
</span>
