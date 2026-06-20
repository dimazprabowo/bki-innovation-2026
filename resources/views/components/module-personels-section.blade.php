@props(['personels' => [], 'competencies' => []])

<div x-data="{ expanded: true }"
    @module-validation-failed.window="if (($event.detail.errorKeys || []).some(k => k.startsWith('personels'))) expanded = true"
    class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-3">
    <div class="flex items-center justify-between mb-3">
        <button type="button" @click="expanded = !expanded" class="flex items-center gap-2 group">
            <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg x-show="!expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Personel</h4>
            <span class="px-2 py-0.5 text-xs font-semibold rounded-full @if(count($personels) > 0) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                {{ count($personels) }} personel
            </span>
        </button>
        <button type="button" wire:click="addPersonel" wire:key="add-personel-btn"
            wire:loading.attr="disabled" wire:target="addPersonel"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors whitespace-nowrap">
            <svg wire:loading.remove wire:target="addPersonel" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <svg wire:loading wire:target="addPersonel" class="animate-spin w-4 h-4" wire:key="add-loading-icon-personel" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="addPersonel" class="hidden sm:inline">Tambah Personel</span>
            <span wire:loading.remove wire:target="addPersonel" class="sm:hidden">Tambah</span>
            <span wire:loading wire:target="addPersonel" class="hidden sm:inline">Memproses</span>
            <span wire:loading wire:target="addPersonel" class="sm:hidden">...</span>
        </button>
    </div>

    <div x-show="expanded" x-collapse>

    @forelse($personels as $personelIndex => $personel)
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-3">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Jabatan <span class="text-red-500">*</span></label>
                        <input wire:model="personels.{{ $personelIndex }}.position_name" type="text"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                            placeholder="Masukkan nama jabatan (wajib)">
                        @error('personels.'.$personelIndex.'.position_name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jumlah <span class="text-red-500">*</span></label>
                        <input wire:model="personels.{{ $personelIndex }}.quantity" type="number" min="1"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                            placeholder="Masukkan jumlah (wajib)">
                        @error('personels.'.$personelIndex.'.quantity')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Kompetensi</label>
                        <x-multi-searchable-select
                            wire:model.live="personels.{{ $personelIndex }}.competencies"
                            :options="collect($competencies)->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                            placeholder="Pilih kompetensi"
                            searchPlaceholder="Cari kompetensi..."
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sifat <span class="text-red-500">*</span></label>
                        <select wire:model="personels.{{ $personelIndex }}.nature"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200">
                            <option value="mandatory">Wajib</option>
                            <option value="optional">Opsional</option>
                        </select>
                        @error('personels.'.$personelIndex.'.nature')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="button" wire:click="removePersonel({{ $personelIndex }})"
                    wire:key="remove-personel-{{ $personelIndex }}"
                    wire:loading.attr="disabled"
                    wire:target="removePersonel({{ $personelIndex }})"
                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-6 disabled:opacity-50">
                    <svg wire:loading.class="hidden" wire:target="removePersonel({{ $personelIndex }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <svg wire:loading wire:target="removePersonel({{ $personelIndex }})" wire:key="loading-personel-{{ $personelIndex }}" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    @empty
        <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Belum ada personel
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Klik "Tambah Personel" untuk menambahkan personel
            </p>
        </div>
    @endforelse
    </div>
</div>
