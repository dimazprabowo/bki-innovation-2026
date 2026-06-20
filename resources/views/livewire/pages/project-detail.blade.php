<div class="space-y-6">
    {{-- Back Button --}}
    <div>
        <button type="button" wire:click="goBack" wire:target="goBack"
            class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
            <svg wire:loading.class.remove="inline" wire:loading.class.add="hidden" wire:target="goBack" class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <svg wire:loading wire:target="goBack" class="animate-spin w-5 h-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Kembali ke Daftar Project
        </button>
    </div>

    {{-- Project Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ substr($project->code, 0, 2) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $project->code }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->status->badgeClass() }}">
                            {{ $project->status->label() }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->approval_status->badgeClass() }}">
                            {{ $project->approval_status->label() }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->priority->badgeClass() }}">
                            {{ $project->priority->label() }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->risk_level->badgeClass() }}">
                            {{ $project->risk_level->label() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                @can('projects_update')
                    @if($project->status === \App\Enums\ProjectStatus::Draft && in_array($project->approval_status->value, ['none', 'rejected']))
                        <x-loading-button wire:click="submit" target="submit" variant="primary" size="md"
                            loadingText="Mengajukan...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </x-slot:icon>
                            Ajukan
                        </x-loading-button>
                    @endif
                @endcan

                @can('projects_approve')
                    @if($project->approval_status->value === 'coe_review')
                        <x-loading-button wire:click="approve" target="approve" variant="success" size="md"
                            loadingText="Menyetujui...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </x-slot:icon>
                            Setujui
                        </x-loading-button>
                        <x-loading-button wire:click="confirmReject" target="confirmReject" variant="warning" size="md"
                            loadingText="Loading...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </x-slot:icon>
                            Tolak
                        </x-loading-button>
                    @endif
                @endcan

                @can('projects_approve')
                    @if(in_array($project->status->value, ['active', 'on_progress', 'completed']))
                        <x-loading-button wire:click="confirmClose" target="confirmClose" variant="danger" size="md"
                            loadingText="Loading...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </x-slot:icon>
                            Tutup
                        </x-loading-button>
                    @endif
                @endcan

                @can('projects_update')
                    @if($project->status->isEditable())
                        <a href="{{ route('projects.edit', $project) }}" wire:navigate x-data="{ loading: false }" @click="loading = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!loading">Edit</span>
                            <span x-show="loading" x-cloak>Memuat...</span>
                        </a>
                    @endif
                @endcan
            </div>
        </div>

        @if($project->description)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Deskripsi</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $project->description }}</p>
            </div>
        @endif
    </div>

    {{-- Rejection Reason --}}
    @if($project->approval_status->value === 'rejected' && $project->rejection_reason)
        <div class="p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300 mb-1">Alasan Penolakan</h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-400">{{ $project->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Project Info Grid --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Informasi Project</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Tanggal Mulai</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->start_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Tanggal Selesai</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->end_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Dibuat Oleh</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->creator?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Disetujui Oleh</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->approver?->name ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Cost Summary --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Ringkasan Biaya</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Biaya Modul</span>
                <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($project->base_cost, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Biaya Tambahan</span>
                <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($project->additional_cost_total, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex items-center justify-between">
                <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($project->total_cost, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Modules --}}
    @if($project->modules->isNotEmpty())
        <div class="space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Detail Modul ({{ $project->modules->count() }})</h3>
            @foreach($project->modules as $module)
                @php
                    $subtotal = (float)($module->pivot->quantity ?? 0) * (float)($module->pivot->unit_price ?? 0);
                @endphp
                <div x-data="{ expanded: false }"
                     class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="expanded = !expanded"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 transition-colors hover:bg-gray-100 dark:hover:bg-gray-900 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg x-show="!expanded" x-cloak class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $module->name }}</span>
                            <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full {{ $module->risk_level->badgeClass() }}">
                                {{ $module->risk_level->label() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Qty: {{ $module->pivot->quantity }} · Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </button>
                    <div x-show="expanded" x-collapse class="p-4 bg-gray-50 dark:bg-gray-900/50">
                        <div class="grid grid-cols-3 gap-3 mb-4 text-sm">
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Qty</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">{{ $module->pivot->quantity }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Harga Satuan</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($module->pivot->unit_price, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Subtotal</dt>
                                <dd class="font-semibold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($module->pivot->subtotal, 0, ',', '.') }}</dd>
                            </div>
                        </div>
                        @if($module->pivot->notes)
                            <div class="mb-4 text-sm">
                                <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Catatan</dt>
                                <dd class="text-gray-700 dark:text-gray-300">{{ $module->pivot->notes }}</dd>
                            </div>
                        @endif
                        <x-module-info :module="$module" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Personels --}}
    @if($project->projectPersonels->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Personel ({{ $project->projectPersonels->count() }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($project->projectPersonels as $pp)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
                            {{ substr($pp->personel?->name ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $pp->personel?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $pp->module?->name ?? '-' }} · {{ $pp->personelSlot?->position_name ?? '-' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Equipment --}}
    @if($project->projectPeralatans->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Peralatan ({{ $project->projectPeralatans->count() }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($project->projectPeralatans as $pp)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $pp->peralatan?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $pp->module?->name ?? '-' }}
                                @if($pp->tool?->requires_calibration)
                                    · Perlu Kalibrasi
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Additional Costs --}}
    @if($project->additionalCosts->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Biaya Tambahan ({{ $project->additionalCosts->count() }})</h3>
            <div class="space-y-2">
                @foreach($project->additionalCosts as $cost)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cost->name }}</p>
                            @if($cost->notes)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $cost->notes }}</p>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($cost->amount, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.set('showRejectModal', false)"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="mt-3 w-full sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Tolak Project</h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Alasan Penolakan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        wire:model="rejectionReason"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Jelaskan alasan penolakan project ini (minimal 10 karakter)"></textarea>
                                    @error('rejectionReason')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <x-loading-button wire:click="reject" target="reject" variant="warning" size="lg"
                            loadingText="Menolak..." class="w-full sm:w-auto">
                            Tolak Project
                        </x-loading-button>
                        <x-loading-button type="button" @click="$wire.set('showRejectModal', false)" variant="secondary" size="lg"
                            class="mt-3 sm:mt-0 w-full sm:w-auto">
                            Batal
                        </x-loading-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Close Modal --}}
    @if($showCloseModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.set('showCloseModal', false)"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <div class="mt-3 w-full sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Tutup Project</h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Project yang ditutup tidak dapat diedit atau diajukan kembali.</p>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Alasan Penutupan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        wire:model="closeReason"
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Jelaskan alasan penutupan project ini (minimal 10 karakter)"></textarea>
                                    @error('closeReason')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <x-loading-button wire:click="closeProject" target="closeProject" variant="danger" size="lg"
                            loadingText="Menutup..." class="w-full sm:w-auto">
                            Tutup Project
                        </x-loading-button>
                        <x-loading-button type="button" @click="$wire.set('showCloseModal', false)" variant="secondary" size="lg"
                            class="mt-3 sm:mt-0 w-full sm:w-auto">
                            Batal
                        </x-loading-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
