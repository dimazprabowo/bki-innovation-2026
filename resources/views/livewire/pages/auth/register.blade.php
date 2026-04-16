<div class="min-h-screen flex flex-col lg:flex-row relative">
    {{-- Dark Mode Toggle - Fixed Position --}}
    <div class="fixed top-4 right-4 z-50">
        <button @click="darkMode = !darkMode" 
                class="p-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-700">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>
    </div>

    {{-- Left Side - Branding Panel --}}
    <x-auth-branding-panel />

    {{-- Right Side - Register Form --}}
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen lg:min-h-0 relative overflow-hidden">
        {{-- Mobile Background (visible only on mobile) --}}
        <div class="lg:hidden absolute inset-0 z-0 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900"></div>
        
        <div class="w-full max-w-md relative z-10">
            {{-- Mobile Logo --}}
            <x-auth-mobile-logo />

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
                <div class="mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Daftar Akun</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm sm:text-base">Buat akun baru untuk mengakses sistem</p>
                </div>

                <form wire:submit="register" class="space-y-6">
                    {{-- Name --}}
                    <div>
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email Address --}}
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" placeholder="nama@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit" 
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        wire:target="register">

                        <span class="inline-flex items-center justify-center gap-2">
                            {{-- Loading Icon --}}
                            <svg wire:loading wire:target="register"
                                class="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            {{-- Normal Text --}}
                            <span wire:loading.remove wire:target="register">
                                Daftar
                            </span>

                            {{-- Loading Text --}}
                            <span wire:loading wire:target="register">
                                Memproses...
                            </span>
                        </span>
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors">
                            Sudah punya akun? Login
                        </a>
                    </div>
                </form>
            </div>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                Butuh bantuan? <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">Hubungi Support</a>
            </p>
        </div>
    </div>
</div>
