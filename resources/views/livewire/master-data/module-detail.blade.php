<div wire:key="module-detail-{{ $moduleId }}" class="space-y-6">
    {{-- Back Button --}}
    <div x-data="{ loading: false }">
        <button type="button" @click="loading = true; $wire.goBack()"
            :disabled="loading"
            class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors disabled:opacity-50">
            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <svg x-show="loading" x-cloak class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Kembali ke Daftar Modul
        </button>
    </div>

    {{-- Review Status Banner --}}
    <x-review-status-card
        :reviewStatus="$module->review_status"
        :reviewer="$module->reviewer"
        :reviewedAt="$module->reviewed_at"
        :rejectionReason="$module->rejection_reason"
        :approvalNote="$module->approval_note"
        title="Review Modul"
    />

    {{-- Module Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informasi Modul</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Modul</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $module->code }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Modul</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $module->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durasi</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $module->duration ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Risk Level</p>
                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium {{ $module->risk_level->badgeClass() }} mt-1">
                    {{ $module->risk_level->label() }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pricing Baseline</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $module->pricing_baseline ? 'Rp ' . number_format($module->pricing_baseline, 0, ',', '.') : '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium {{ $module->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' }} mt-1">
                    {{ $module->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah Project</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $module->projects_count }} project</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Dibuat</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $module->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        @if($module->notes)
            <div class="mt-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Catatan</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-wrap">{{ $module->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Work Order References --}}
    @if($module->workOrderReferences->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Referensi Dokumen</h3>
            <div class="space-y-3">
                @foreach($module->workOrderReferences as $ref)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $ref->document_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ref->document_id ?? '-' }}</p>
                        </div>
                        @if($ref->file_path)
                            <a href="{{ asset('storage/' . $ref->file_path) }}" target="_blank"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                Lihat File
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Work Order Items --}}
    @if($module->workOrderItems->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Work Order Items</h3>
            <div class="space-y-3">
                @foreach($module->workOrderItems as $item)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">{{ $item->order }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $item->name }}</p>
                                @if($item->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item->description }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center whitespace-nowrap text-xs px-2 py-0.5 rounded-full {{ $item->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">{{ $item->nature_label }}</span>
                        </div>
                        @if($item->subitems->isNotEmpty())
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($item->subitems as $subitem)
                                    <div class="flex items-center gap-3 px-3 py-2 pl-11">
                                        <span class="flex-shrink-0 text-xs font-semibold text-gray-400 dark:text-gray-500">{{ $item->order }}.{{ $subitem->order }}</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $subitem->name }}</p>
                                            @if($subitem->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subitem->description }}</p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center whitespace-nowrap text-xs px-2 py-0.5 rounded-full {{ $subitem->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">{{ $subitem->nature_label }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Personels --}}
    @if($module->personels->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Personel</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($module->personels as $personel)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $personel->position_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ $personel->quantity }} &middot; {{ $personel->nature_label }}</p>
                                @if($personel->competencies->isNotEmpty())
                                    <div class="mt-2">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kompetensi:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($personel->competencies as $competency)
                                                <span class="inline-flex items-center whitespace-nowrap text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">{{ $competency->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tools --}}
    @if($module->tools->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tools / Peralatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($module->tools as $tool)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $tool->peralatan->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ $tool->quantity ?? 1 }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    @if($tool->requires_calibration)
                                        <span class="inline-flex items-center whitespace-nowrap gap-1 text-xs px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Perlu Kalibrasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center whitespace-nowrap gap-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Tidak Perlu Kalibrasi
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Deliverables --}}
    @if($module->deliverables->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Deliverables</h3>
            <div class="space-y-2">
                @foreach($module->deliverables as $deliverable)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">{{ $deliverable->order }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $deliverable->name }}</p>
                            @if($deliverable->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $deliverable->description }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center whitespace-nowrap text-xs px-2 py-0.5 rounded-full {{ $deliverable->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">{{ $deliverable->nature_label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3">
        @can('modules_update')
            <a href="{{ route('master-data.modules.edit', $module) }}" wire:navigate x-data="{ loading: false }" @click="loading = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-show="!loading">Edit Modul</span>
                <span x-show="loading" x-cloak>Memuat...</span>
            </a>
        @endcan
    </div>
</div>
