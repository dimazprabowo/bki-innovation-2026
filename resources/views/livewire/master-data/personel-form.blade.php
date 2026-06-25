<div
    @if($this->hasProcessingCompetencyFiles())
        wire:poll.4s="refreshCompetencyFileStatus"
    @endif
>
    <div class="w-full">
        <form wire:submit="save">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kode Personel <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="code" type="text"
                                placeholder="Masukkan kode personel"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status Aktif <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="is_active"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            @error('is_active') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Personel <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="name" type="text"
                                placeholder="Masukkan nama personel"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <x-personel-competency-section
                        :competencies="$competencies"
                        :competencyOptions="$competencyOptions"
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

    {{-- Delete Competency Modal --}}
    <x-delete-modal
        :show="$showDeleteCompetencyModal"
        wire:model="showDeleteCompetencyModal"
        title="Hapus Kompetensi"
        message="Apakah Anda yakin ingin menghapus kompetensi ini?"
        confirmMethod="confirmDeleteCompetency"
    />
</div>
