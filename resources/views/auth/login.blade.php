<x-guest-layout>
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-[900] text-slate-950 tracking-tight mb-2">Welcome Back</h1>
        <p class="text-slate-500 font-medium">Re-authenticate to central systems</p>
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="group">
            <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Email Protocol</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="operator@resqflow.com"
                    class="glass-input w-full !pl-12 !py-4 font-bold text-slate-900 placeholder:text-slate-300 transition-all border-white/20" />
            </div>
            @error('email') <p class="mt-2 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
        </div>

        <div class="group" x-data="{ show: false }">
            <div class="flex justify-between items-center mb-2 px-1">
                <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Access Key</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-slate-400 hover:text-red-500 transition-colors uppercase tracking-widest">Lost Key?</a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                    placeholder="••••••••"
                    class="glass-input w-full !pl-12 !py-4 font-bold text-slate-900 placeholder:text-slate-300 transition-all border-white/20" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-300 hover:text-slate-600 transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.254 0 2.438.283 3.5.789m2.813 2.813A10.081 10.081 0 0121.542 12c-1.274 4.057-5.064 7-9.542 7-1.254 0-2.438-.283-3.5-.789m-2.813-2.813L3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
            </div>
            @error('password') <p class="mt-2 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between px-1">
            <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative w-5 h-5">
                    <input id="remember_me" type="checkbox" name="remember" class="peer hidden">
                    <div class="absolute inset-0 bg-white/50 border border-white/40 rounded-md transition-all peer-checked:bg-red-500 peer-checked:border-red-500"></div>
                    <svg class="absolute inset-0 w-5 h-5 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest group-hover:text-slate-700 transition-colors">Maintain Session</span>
            </label>
        </div>

        <button type="submit" class="btn-premium w-full !py-4 shadow-xl shadow-red-100">
            INITIALIZE AUTHENTICATION
        </button>

        <p class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mt-8">
            New operative?
            <a href="{{ route('register') }}" class="text-red-500 hover:text-red-600 transition-colors">Register Account</a>
        </p>
    </form>
</x-guest-layout>
