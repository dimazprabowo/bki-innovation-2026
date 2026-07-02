<div wire:poll.4s="refreshWorkOrderReferenceFileStatus" wire:key="module-form-{{ $editMode ? 'edit-'.$moduleId : 'create' }}" class="w-full">
    <form wire:submit="save">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kode Modul <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="code" type="text"
                            placeholder="Masukkan kode modul"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Modul <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text"
                            placeholder="Masukkan nama modul"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>

                @if($editMode)
                    @php
                        $reviewIcon = match($module->review_status->value) {
                            'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            'approved' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'rejected' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            default => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        };
                        $reviewBorder = match($module->review_status->value) {
                            'pending' => 'border-purple-200 dark:border-purple-800/50',
                            'approved' => 'border-green-200 dark:border-green-800/50',
                            'rejected' => 'border-red-200 dark:border-red-800/50',
                            default => 'border-gray-200 dark:border-gray-700',
                        };
                        $reviewIconBg = match($module->review_status->value) {
                            'pending' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                            'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                        };
                    @endphp
                    <div class="mt-6 border {{ $reviewBorder }} rounded-xl overflow-hidden">
                        <div class="flex items-start gap-4 p-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $reviewIconBg }} flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $reviewIcon }}"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-white">Status Review Modul</span>
                                    <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium {{ $module->review_status->badgeClass() }}">
                                        {{ $module->review_status->label() }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $module->review_status->description() }}</p>
                                @if($module->reviewer)
                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $module->reviewer->name }}
                                        </span>
                                        @if($module->reviewed_at)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $module->reviewed_at->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                @if($module->isRejected() && $module->rejection_reason)
                                    <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/10 rounded-lg">
                                        <p class="text-xs font-medium text-red-700 dark:text-red-400">Alasan Penolakan:</p>
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $module->rejection_reason }}</p>
                                    </div>
                                @endif
                                @if($module->isReviewed() && $module->approval_note)
                                    <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/10 rounded-lg">
                                        <p class="text-xs font-medium text-green-700 dark:text-green-400">Catatan Persetujuan:</p>
                                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">{{ $module->approval_note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Work Order References -->
                <x-module-reference-section :workOrderReferences="$workOrderReferences" />

                <x-module-work-order-items-section :workOrderItems="$workOrderItems" />

                <x-module-personels-section :personels="$personels" :competencies="$competencies" />

                <x-module-tools-section :tools="$tools" :peralatans="$peralatans" />

                <x-module-deliverables-section :deliverables="$deliverables" />

                <x-module-risk-pricing-section :riskLevels="$riskLevels" />

                <!-- Notes -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                    <textarea wire:model="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Masukkan catatan tambahan"></textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-5 py-4">
            <x-cancel-button wire:click="cancel" target="cancel" variant="secondary" size="lg" class="w-full sm:w-auto" />
            <x-loading-button type="submit" target="save" variant="primary" size="lg"
                loadingText="Menyimpan..." class="w-full sm:w-auto">
                {{ $editMode ? 'Update' : 'Simpan' }}
            </x-loading-button>
        </div>
    </form>

    {{-- Delete Work Order Item Modal --}}
    <x-delete-modal
        :show="$showDeleteWorkOrderItemModal"
        wire:model="showDeleteWorkOrderItemModal"
        title="Hapus Work Order Item"
        message="Apakah Anda yakin ingin menghapus work order item ini beserta semua subitemnya?"
        confirmMethod="confirmDeleteWorkOrderItem"
    />

    {{-- Delete Work Order Subitem Modal --}}
    <x-delete-modal
        :show="$showDeleteWorkOrderSubitemModal"
        wire:model="showDeleteWorkOrderSubitemModal"
        title="Hapus Subitem"
        message="Apakah Anda yakin ingin menghapus subitem ini?"
        confirmMethod="confirmDeleteWorkOrderSubitem"
    />

    {{-- Delete Work Order Reference Modal --}}
    <x-delete-modal
        :show="$showDeleteWorkOrderReferenceModal"
        wire:model="showDeleteWorkOrderReferenceModal"
        title="Hapus Referensi Work Order"
        message="Apakah Anda yakin ingin menghapus referensi work order ini?"
        confirmMethod="confirmDeleteWorkOrderReference"
    />

    {{-- Delete Personel Modal --}}
    <x-delete-modal
        :show="$showDeletePersonelModal"
        wire:model="showDeletePersonelModal"
        title="Hapus Personel"
        message="Apakah Anda yakin ingin menghapus personel ini?"
        confirmMethod="confirmDeletePersonel"
    />

    {{-- Delete Tool Modal --}}
    <x-delete-modal
        :show="$showDeleteToolModal"
        wire:model="showDeleteToolModal"
        title="Hapus Alat"
        message="Apakah Anda yakin ingin menghapus alat ini?"
        confirmMethod="confirmDeleteTool"
    />

    {{-- Delete Deliverable Modal --}}
    <x-delete-modal
        :show="$showDeleteDeliverableModal"
        wire:model="showDeleteDeliverableModal"
        title="Hapus Deliverable"
        message="Apakah Anda yakin ingin menghapus deliverable ini?"
        confirmMethod="confirmDeleteDeliverable"
    />
</div>
