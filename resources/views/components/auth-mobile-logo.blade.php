{{-- Auth Mobile Logo - Shown on mobile devices --}}
<div class="lg:hidden flex flex-col items-center mb-8">
    <div class="flex items-center space-x-3 mb-4">
        <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden">
            <img src="{{ email_logo_url() }}" alt="BKI Logo" class="w-full h-full object-contain rounded-lg">
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ app_name() }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">PT. Biro Klasifikasi Indonesia</p>
        </div>
    </div>
</div>
