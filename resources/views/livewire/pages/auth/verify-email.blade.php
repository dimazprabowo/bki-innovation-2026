<div class="min-h-screen flex flex-col lg:flex-row relative">
    <!-- Dark Mode Toggle - Fixed Position -->
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

    <!-- Left Side - Branding Panel -->
    <x-auth-branding-panel />

    <!-- Right Side - Verify Email -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen lg:min-h-0 relative overflow-hidden">
        {{-- Mobile Background (visible only on mobile) --}}
        <div class="lg:hidden absolute inset-0 z-0 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900"></div>
        
        <div class="w-full max-w-md relative z-10">
            <!-- Mobile Logo -->
            <x-auth-mobile-logo />

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
                <div class="mb-8">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white text-center">Verifikasi Email</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm sm:text-base text-center">
                        Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik link yang kami kirimkan. Jika tidak menerima email, kami akan dengan senang hati mengirimkan ulang.
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                Link verifikasi baru telah dikirim ke alamat email Anda.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    <button wire:click="sendVerification" 
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        wire:target="sendVerification">

                        <span class="inline-flex items-center justify-center gap-2">

                            <!-- ICON LOADING -->
                            <svg wire:loading wire:target="sendVerification"
                                class="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <!-- TEKS NORMAL -->
                            <span wire:loading.remove wire:target="sendVerification">
                                Kirim Ulang Email Verifikasi
                            </span>

                            <!-- TEKS LOADING -->
                            <span wire:loading wire:target="sendVerification">
                                Mengirim...
                            </span>

                        </span>
                    </button>

                    <button wire:click="logout" type="button" 
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-base text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        wire:target="logout">

                        <span class="inline-flex items-center justify-center gap-2">

                            <!-- ICON LOADING -->
                            <svg wire:loading wire:target="logout"
                                class="animate-spin h-5 w-5 text-gray-700 dark:text-gray-300"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <!-- ICON NORMAL -->
                            <svg wire:loading.remove wire:target="logout" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>

                            <!-- TEKS NORMAL -->
                            <span wire:loading.remove wire:target="logout">
                                Logout & Kembali ke Login
                            </span>

                            <!-- TEKS LOADING -->
                            <span wire:loading wire:target="logout">
                                Logout...
                            </span>

                        </span>
                    </button>
                </div>
            </div>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                Butuh bantuan? <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">Hubungi Support</a>
            </p>
        </div>
    </div>
</div>
