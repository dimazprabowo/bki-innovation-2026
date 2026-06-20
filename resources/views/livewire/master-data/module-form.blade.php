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
            <x-loading-button type="button" wire:click="cancel" target="cancel" variant="secondary" size="lg"
                loadingText="Memuat..." class="w-full sm:w-auto">
                Batal
            </x-loading-button>
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
