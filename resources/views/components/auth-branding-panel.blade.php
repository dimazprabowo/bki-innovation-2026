{{-- Auth Branding Panel - Left Side with Professional Branding --}}
<div class="hidden lg:flex lg:w-1/2 xl:w-2/5 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 dark:from-blue-800 dark:via-blue-900 dark:to-gray-900 p-8 lg:p-12 flex-col justify-between relative overflow-hidden">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
    </div>
    
    {{-- Content --}}
    <div class="relative z-10">
        {{-- Top Section - Logo & Title --}}
        <div>
            {{-- Logo BKI --}}
            <div class="flex items-center space-x-3 mb-12">
                <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden">
                    <img src="{{ email_logo_url() }}" alt="BKI Logo" class="w-full h-full object-contain rounded-lg">
                </div>
                <div class="text-white">
                    <h1 class="text-2xl lg:text-3xl font-bold">{{ app_name() }}</h1>
                    <p class="text-sm text-blue-100">PT. Biro Klasifikasi Indonesia</p>
                </div>
            </div>
            
            {{-- Main Title --}}
            <div class="space-y-6 max-w-lg">
                <h2 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-white leading-tight">
                    {{ app_name() }}
                </h2>
                <p class="text-lg lg:text-xl text-blue-100 leading-relaxed">
                    Laravel boilerplate application with authentication, role-based access control, and user management.
                </p>
            </div>
        </div>
        
        {{-- Middle Section - Features --}}
        <div class="space-y-4 pt-8">
            <div class="flex items-start space-x-4 text-blue-50">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Authentication & Authorization</h3>
                    <p class="text-sm text-blue-200">Login, register, email verification, password reset</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4 text-blue-50">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Role-Based Access Control</h3>
                    <p class="text-sm text-blue-200">Flexible roles & permissions management</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4 text-blue-50">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-1">User & System Management</h3>
                    <p class="text-sm text-blue-200">Complete user CRUD and system configuration</p>
                </div>
            </div>
        </div>
        
    </div>
    <div class="relative z-10 text-blue-100 text-sm">
        <p>&copy; {{ date('Y') }} {{ app_name() }}. All rights reserved.</p>
    </div>
</div>
