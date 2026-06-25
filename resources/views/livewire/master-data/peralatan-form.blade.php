<div wire:poll.4s="refreshEvidenceFileStatus" wire:key="peralatan-form-{{ $editMode ? 'edit-'.$peralatanId : 'create' }}">
    <div class="w-full">
        <form wire:submit="save">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kode Alat <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="code" type="text"
                                placeholder="Masukkan kode alat"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status Aktif <span class="text-red-500">*</span>
                            </label>
                            <x-searchable-select
                                wire:model="is_active"
                                :options="$this->isActiveOptions"
                                placeholder="Pilih status..."
                                searchPlaceholder="Cari status..."
                            />
                            @error('is_active') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Alat <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="name" type="text"
                                placeholder="Masukkan nama alat"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Deskripsi
                            </label>
                            <textarea wire:model="description" rows="3"
                                placeholder="Masukkan deskripsi alat"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Lokasi
                            </label>
                            <input wire:model="location" type="text"
                                placeholder="Masukkan lokasi alat"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status Kalibrasi <span class="text-red-500">*</span>
                            </label>
                            <x-searchable-select
                                wire:model="calibration_status"
                                :options="$this->calibrationStatusOptions"
                                placeholder="Pilih status kalibrasi..."
                                searchPlaceholder="Cari status..."
                            />
                            @error('calibration_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tanggal Expired Kalibrasi
                            </label>
                            <input wire:model="calibration_expired_date" type="date"
                                placeholder="Tanggal expired kalibrasi"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('calibration_expired_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kondisi Alat <span class="text-red-500">*</span>
                            </label>
                            <x-searchable-select
                                wire:model="condition"
                                :options="$this->conditionOptions"
                                placeholder="Pilih kondisi..."
                                searchPlaceholder="Cari kondisi..."
                            />
                            @error('condition') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status Kepemilikan <span class="text-red-500">*</span>
                            </label>
                            <x-searchable-select
                                wire:model="ownership_status"
                                :options="$this->ownershipStatusOptions"
                                placeholder="Pilih status kepemilikan..."
                                searchPlaceholder="Cari kepemilikan..."
                            />
                            @error('ownership_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Evidence Section -->
                    <x-peralatan-evidence-section
                        :evidences="$evidences"
                    />
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
    </div>

    {{-- Delete Evidence Modal --}}
    <x-delete-modal
        :show="$showDeleteEvidenceModal"
        wire:model="showDeleteEvidenceModal"
        title="Hapus Evidence"
        message="Apakah Anda yakin ingin menghapus evidence ini?"
        confirmMethod="confirmDeleteEvidence"
    />
</div>
