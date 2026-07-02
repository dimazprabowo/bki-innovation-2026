<div wire:key="peralatan-detail-{{ $peralatanId }}" class="space-y-6">
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
            Kembali ke Daftar Peralatan
        </button>
    </div>

    {{-- Review Status Banner --}}
    <x-review-status-card
        :reviewStatus="$peralatan->review_status"
        :reviewer="$peralatan->reviewer"
        :reviewedAt="$peralatan->reviewed_at"
        :rejectionReason="$peralatan->rejection_reason"
        :approvalNote="$peralatan->approval_note"
        title="Review Peralatan"
    />

    {{-- Peralatan Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informasi Peralatan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Alat</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $peralatan->code }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Alat</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $peralatan->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lokasi</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $peralatan->location ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Kalibrasi</p>
                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                    {{ $peralatan->calibration_status->value === 'calibrated' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                    {{ $peralatan->calibration_status->value === 'expired' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                    {{ $peralatan->calibration_status->value === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                    {{ $peralatan->calibration_status->value === 'not_required' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}">
                    {{ $peralatan->calibration_status->label() }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Expired Kalibrasi</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $peralatan->calibration_expired_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kondisi</p>
                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                    {{ $peralatan->condition->value === 'suitable' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                    {{ $peralatan->condition->label() }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Kepemilikan</p>
                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                    {{ $peralatan->ownership_status->value === 'owned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                    {{ $peralatan->ownership_status->value === 'rented' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                    {{ $peralatan->ownership_status->value === 'borrowed' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                    {{ $peralatan->ownership_status->value === 'leased' ? 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300' : '' }}">
                    {{ $peralatan->ownership_status->label() }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium {{ $peralatan->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' }} mt-1">
                    {{ $peralatan->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Dibuat</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $peralatan->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        @if($peralatan->description)
            <div class="mt-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-wrap">{{ $peralatan->description }}</p>
            </div>
        @endif
    </div>

    {{-- Evidences --}}
    @if($peralatan->evidences->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Dokumen Evidence</h3>
            <div class="space-y-3">
                @foreach($peralatan->evidences as $evidence)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $evidence->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $evidence->file_name ?? '-' }}</p>
                        </div>
                        @if($evidence->file_path)
                            <button wire:click="downloadEvidence({{ $evidence->id }})"
                                wire:key="download-btn-{{ $evidence->id }}"
                                wire:loading.attr="disabled"
                                wire:target="downloadEvidence({{ $evidence->id }})"
                                class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium disabled:opacity-50">
                                <svg wire:loading.class="hidden" wire:target="downloadEvidence({{ $evidence->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <svg wire:loading wire:target="downloadEvidence({{ $evidence->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span wire:loading.class="hidden" wire:target="downloadEvidence({{ $evidence->id }})">Download</span>
                                <span wire:loading wire:target="downloadEvidence({{ $evidence->id }})">Memuat...</span>
                            </button>
                        @else
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">File belum diunggah</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3">
        @can('peralatan_update')
            <a href="{{ route('master-data.peralatan.edit', $peralatan) }}" wire:navigate x-data="{ loading: false }" @click="loading = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-show="!loading">Edit Peralatan</span>
                <span x-show="loading" x-cloak>Memuat...</span>
            </a>
        @endcan
    </div>
</div>
