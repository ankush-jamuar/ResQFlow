@props(['status', 'id' => ''])

@php
    $steps = [
        ['key' => 'pending',   'label' => 'SOS Received', 'desc' => 'Dispatching'],
        ['key' => 'accepted',  'label' => 'Confirmed',    'desc' => 'Assigning Unit'],
        ['key' => 'en_route',  'label' => 'Rescue Unit',  'desc' => 'Navigating'],
        ['key' => 'completed', 'label' => 'Success',      'desc' => 'Mission Over'],
    ];
@endphp

<div 
    id="{{ $id }}" 
    class="flex items-center justify-between w-full relative px-6"
    x-data="{ 
        get currentIndex() {
            const s = this.currentStatus || '{{ $status }}';
            if (s === 'pending') return 0;
            if (s === 'accepted') return 1;
            if (['dispatched', 'en_route'].includes(s)) return 2;
            if (['arrived', 'completed'].includes(s)) return 3;
            return 0;
        }
    }"
>
    {{-- Connectors --}}
    <div class="absolute top-6 left-12 right-12 h-1 bg-slate-100 -z-0 rounded-full">
        <div 
            class="h-full bg-red-500 transition-all duration-1000 ease-out relative shadow-[0_0_15px_rgba(239,68,68,0.5)]" 
            :style="`width: ${ (currentIndex / 3) * 100 }%`"
        >
            {{-- Lead Pulse --}}
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 bg-red-400 rounded-full animate-ping"></div>
        </div>
    </div>

    @foreach($steps as $index => $step)
        <div class="flex flex-col items-center relative z-10 timeline-step group">
            {{-- Pulse Glow for Active Step --}}
            <template x-if="currentIndex === {{ $index }} && currentStatus !== 'completed'">
                <div class="absolute -top-1 w-14 h-14 bg-red-500/20 rounded-full blur-xl animate-pulse"></div>
            </template>

            <div 
                class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-500 border-2 step-circle"
                :class="{
                    'bg-red-500 border-red-500 text-white shadow-lg shadow-red-200': currentIndex > {{ $index }},
                    'bg-white border-red-500 text-red-500 shadow-2xl shadow-red-100 scale-110': currentIndex === {{ $index }},
                    'bg-white border-slate-100 text-slate-300': currentIndex < {{ $index }}
                }"
            >
                <template x-if="currentIndex > {{ $index }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="currentIndex <= {{ $index }}">
                    <span class="text-sm font-black" :class="currentIndex === {{ $index }} ? 'animate-bounce-short' : ''">{{ $index + 1 }}</span>
                </template>
            </div>

            <div class="mt-4 text-center">
                <p 
                    class="text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-colors duration-300 step-text"
                    :class="currentIndex >= {{ $index }} ? 'text-slate-950' : 'text-slate-300'"
                >
                    {{ $step['label'] }}
                </p>
                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5 whitespace-nowrap">
                    {{ $step['desc'] }}
                </p>
            </div>
        </div>
    @endforeach
</div>
