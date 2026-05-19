<header 
    id="topbar"
    :class="sidebarOpen ? 'left-[288px]' : 'left-[96px]'"
    class="fixed top-6 right-6 h-20 glass-panel !bg-slate-950/40 border-white/5 backdrop-blur-2xl transition-all duration-500 z-40 px-10 flex items-center justify-between rounded-[2rem] shadow-2xl"
>
    <div class="flex items-center gap-8">
        <!-- Sidebar Toggle -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            class="group w-10 h-10 flex items-center justify-center rounded-2xl bg-white/5 hover:bg-red-500 transition-all border border-white/5 shadow-inner"
        >
            <div class="space-y-1.5">
                <div class="w-5 h-0.5 bg-slate-400 group-hover:bg-white transition-all" :class="sidebarOpen ? 'rotate-45 translate-y-2 w-4' : ''"></div>
                <div class="w-5 h-0.5 bg-slate-400 group-hover:bg-white transition-all" :class="sidebarOpen ? 'opacity-0' : ''"></div>
                <div class="w-5 h-0.5 bg-slate-400 group-hover:bg-white transition-all" :class="sidebarOpen ? '-rotate-45 -translate-y-2 w-4' : ''"></div>
            </div>
        </button>

        <!-- Command Breadcrumb -->
        <div class="hidden md:flex items-center gap-4 text-xs font-black uppercase tracking-[0.2em] text-slate-500">
            <span class="hover:text-white transition-colors cursor-pointer">Sector 01</span>
            <div class="w-1 h-1 rounded-full bg-slate-800"></div>
            <span class="text-white">{{ $header ?? 'Strategic Overview' }}</span>
        </div>
    </div>

    <div class="flex items-center gap-6">
        <!-- Resilience Center (PHASE 3) -->
        <div class="hidden lg:flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-white/5 border border-white/5" x-data="resilienceMonitor()">
            <div class="flex flex-col items-end">
                <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Signal Integrity</span>
                <span class="text-[10px] font-black transition-colors duration-300" 
                      :class="statusColor" 
                      x-text="statusText">INIT...</span>
            </div>
            <div class="relative w-3 h-3">
                <div class="absolute inset-0 rounded-full animate-ping opacity-75" :class="indicatorColor"></div>
                <div class="relative w-3 h-3 rounded-full" :class="indicatorColor"></div>
            </div>

            <!-- Detailed Stats Tooltip/Dropdown -->
            <div class="ml-4 pl-4 border-l border-white/10 hidden xl:flex flex-col items-start">
                <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Latency</span>
                <span class="text-[10px] font-black text-slate-300" x-text="latency + 'ms'">--</span>
            </div>
        </div>

        <!-- Notification Intel -->
        <div class="relative" x-data="notificationHub({{ auth()->user()->unreadNotifications->toJson() }})">
            <button 
                @click="open = !open"
                class="relative w-12 h-12 flex items-center justify-center rounded-2xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-all border border-white/5 group"
            >
                <svg class="w-6 h-6 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="notifications.length > 0" class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-slate-900 animate-pulse"></span>
            </button>

            <!-- Notification Dropdown -->
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="absolute right-0 mt-4 w-80 glass-panel !bg-slate-900 border-white/10 shadow-2xl rounded-3xl overflow-hidden z-50"
                x-cloak
            >
                <div class="p-5 border-b border-white/5 flex justify-between items-center">
                    <h4 class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Live Intel</h4>
                    <span class="px-3 py-1 bg-white/5 rounded-full text-[8px] font-black text-slate-400 uppercase" x-text="notifications.length + ' New'"></span>
                </div>
                
                <div class="max-h-[350px] overflow-y-auto no-scrollbar">
                    <template x-for="notif in notifications" :key="notif.id">
                        <div class="p-5 border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer" @click="markAsRead(notif.id)">
                            <p class="text-xs font-bold text-white mb-1" x-text="notif.data.title"></p>
                            <p class="text-[10px] text-slate-500 leading-relaxed" x-text="notif.data.message"></p>
                            <p class="text-[8px] font-black text-red-500 uppercase mt-2 tracking-widest">Just Now</p>
                        </div>
                    </template>

                    <template x-if="notifications.length === 0">
                        <div class="p-10 text-center">
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">No Active Alerts</p>
                        </div>
                    </template>
                </div>

                <div class="p-4 bg-white/5 text-center">
                    <form action="{{ route('notifications.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[9px] font-black text-slate-400 hover:text-white uppercase tracking-widest transition-colors w-full">Clear All Signal History</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Operative Profile -->
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 p-1.5 pr-6 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all group">
            <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center border border-white/10 text-red-500 font-black text-xs uppercase group-hover:scale-105 transition-transform">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="hidden sm:flex flex-col">
                <span class="text-[10px] font-black text-white uppercase tracking-tight">{{ explode(' ', auth()->user()->name)[0] }}</span>
                <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Profile</span>
            </div>
        </a>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('resilienceMonitor', () => ({
                state: 'initializing', // initializing, connected, reconnecting, degraded
                latency: 0,
                lastPing: null,

                init() {
                    this.monitorEcho();
                    this.startLatencyCheck();
                },

                monitorEcho() {
                    if (!window.Echo) {
                        this.state = 'degraded';
                        return;
                    }

                    if (window.Echo.connector.pusher.connection.state === 'connected') {
                        this.state = 'connected';
                    }

                    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                        if (states.current === 'connected') this.state = 'connected';
                        else if (states.current === 'connecting') this.state = 'reconnecting';
                        else if (states.current === 'unavailable' || states.current === 'failed') this.state = 'degraded';
                    });
                },

                startLatencyCheck() {
                    setInterval(() => {
                        const start = Date.now();
                        fetch('/api/ping', { method: 'HEAD' })
                            .then(() => {
                                this.latency = Date.now() - start;
                            })
                            .catch(() => {
                                this.state = 'degraded';
                            });
                    }, 5000);
                },

                get statusText() {
                    if (this.state === 'connected') return 'REALTIME CONNECTED';
                    if (this.state === 'reconnecting') return 'RE-ESTABLISHING...';
                    if (this.state === 'degraded') return 'SIGNAL DEGRADED (POLLING)';
                    return 'INITIALIZING...';
                },

                get statusColor() {
                    if (this.state === 'connected') return 'text-emerald-500';
                    if (this.state === 'reconnecting') return 'text-amber-500';
                    if (this.state === 'degraded') return 'text-red-500';
                    return 'text-slate-500';
                },

                get indicatorColor() {
                    if (this.state === 'connected') return 'bg-emerald-500';
                    if (this.state === 'reconnecting') return 'bg-amber-500';
                    if (this.state === 'degraded') return 'bg-red-500';
                    return 'bg-slate-500';
                }
            }));

            Alpine.data('notificationHub', (initialNotifications) => ({
                notifications: initialNotifications,
                open: false,
                userId: {{ auth()->id() }},

                init() {
                    if (window.Echo) {
                        window.Echo.private(`App.Models.User.${this.userId}`)
                            .notification((notification) => {
                                this.notifications.unshift({
                                    id: Math.random().toString(36).substr(2, 9),
                                    data: notification
                                });
                                this.playNotificationSound();
                                window.showToast(notification.message, notification.title);
                            });
                    }
                },

                markAsRead(id) {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                },

                playNotificationSound() {
                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.volume = 0.4;
                    audio.play().catch(e => console.warn('Audio play blocked:', e));
                }
            }));
        });

        window.showToast = function(message, title = 'System Update') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'glass-panel !bg-slate-950/90 border-red-500/30 p-6 rounded-2xl shadow-2xl min-w-[320px] reveal-fast mb-4';
            toast.innerHTML = `
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center text-red-500 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">${title}</p>
                        <p class="text-xs font-bold text-white leading-relaxed">${message}</p>
                    </div>
                </div>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full', 'transition-all', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        };
    </script>
</header>
