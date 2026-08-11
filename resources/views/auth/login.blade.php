<x-guest-layout>
    <div class="mb-6 text-center">
        <span class="text-xs font-medium uppercase tracking-wide text-teal-600">Portal Mahasiswa</span>
        <h1 class="mt-1 text-xl font-bold text-slate-900">Masuk ke akun Anda</h1>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-slate-700">
                Alamat email <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-heroicon-o-envelope class="h-4 w-4" />
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">
                Kata sandi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-heroicon-o-lock-closed class="h-4 w-4" />
                </span>
                <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                    class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-10 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500" />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600" tabindex="-1">
                    <x-heroicon-o-eye class="h-4 w-4" x-show="! showPassword" />
                    <x-heroicon-o-eye-slash class="h-4 w-4" x-show="showPassword" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500" />
                <span class="ms-2 text-sm text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-teal-600 hover:text-teal-500" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
            Masuk
        </button>
    </form>

    <p class="mt-6 border-t border-gray-100 pt-4 text-center text-xs text-slate-400">
        Staf program studi &amp; fakultas silakan masuk lewat
        <a href="{{ url('/admin/login') }}" class="font-medium text-slate-500 underline underline-offset-2 hover:text-slate-700">Panel Admin</a>.
    </p>
</x-guest-layout>
