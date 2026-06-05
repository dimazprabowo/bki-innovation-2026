@props(['levelOptions' => []])

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Kode Kompetensi <span class="text-red-500">*</span>
        </label>
        <input wire:model="code" type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Contoh: KOM001">
        @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Nama Kompetensi <span class="text-red-500">*</span>
        </label>
        <input wire:model="name" type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Nama kompetensi">
        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level</label>
        <x-searchable-select
            wire:model="level"
            :options="collect($levelOptions)->filter(fn($opt) => $opt['value'] !== '')->values()->toArray()"
            placeholder="Pilih Level"
            searchPlaceholder="Cari level..."
            :error="$errors->has('level')"
        />
        @error('level') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Status <span class="text-red-500">*</span>
        </label>
        <select wire:model="is_active"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
        @error('is_active') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
        <textarea wire:model="description" rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Deskripsi kompetensi"></textarea>
        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>
</div>
