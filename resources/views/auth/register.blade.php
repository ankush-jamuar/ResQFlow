<x-guest-layout>
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-[900] text-slate-950 tracking-tight mb-2">Join the Network</h1>
        <p class="text-slate-500 font-medium">Create your operative account</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div class="group">
            <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Full Identity</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="John Doe"
                    class="glass-input w-full !pl-12 !py-4 font-bold text-slate-900 placeholder:text-slate-300 transition-all border-white/20" />
            </div>
            @error('name') <p class="mt-2 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
        </div>

        <div class="group">
            <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Email Protocol</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    placeholder="operator@resqflow.com"
                    class="glass-input w-full !pl-12 !py-4 font-bold text-slate-900 placeholder:text-slate-300 transition-all border-white/20" />
            </div>
            @error('email') <p class="mt-2 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="group">
                <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Access Key</label>
                <input type="password" id="password" name="password" required autocomplete="new-password"
                    placeholder="••••••••"
                    class="glass-input w-full !py-4 font-bold text-slate-900 placeholder:text-slate-300 transition-all border-white/20" />
                @error('password') <p class="mt-2 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
            </div>
            <div class="group">
                <label for="password_confirmation" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Verify Key</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                    placeholder="••••••••"
                    class="glass-input w-full !py-4 font-bold text-slate-900 placeholder:text-slate-300 transition-all border-white/20" />
                @error('password_confirmation') <p class="mt-2 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="btn-premium w-full !py-4 shadow-xl shadow-red-100">
            REGISTER AS OPERATIVE
        </button>

        <p class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mt-8">
            Already registered?
            <a href="{{ route('login') }}" class="text-red-500 hover:text-red-600 transition-colors">Sign in</a>
        </p>
    </form>
</x-guest-layout>
