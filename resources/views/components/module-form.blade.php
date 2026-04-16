<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Kode Modul <span class="text-red-500">*</span>
        </label>
        <input wire:model="code" type="text" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Contoh: MOD001">
        @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Nama Modul <span class="text-red-500">*</span>
        </label>
        <input wire:model="name" type="text" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Nama modul">
        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scope</label>
        <textarea wire:model="scope" rows="2" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Deskripsi scope pekerjaan"></textarea>
        @error('scope') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode</label>
        <input wire:model="method" type="text" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Metode pelaksanaan">
        @error('method') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi</label>
        <input wire:model="duration" type="text" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Contoh: 3 bulan">
        @error('duration') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Resource</label>
        <textarea wire:model="resource" rows="2" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Deskripsi resource yang dibutuhkan"></textarea>
        @error('resource') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deliverable</label>
        <textarea wire:model="deliverable" rows="2" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Deskripsi deliverable yang dihasilkan"></textarea>
        @error('deliverable') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Risk & Control</h4>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Tingkat Risiko <span class="text-red-500">*</span>
        </label>
        <x-searchable-select
            wire:model.live="risk_level"
            :options="collect($riskLevels)->map(fn($level) => ['value' => $level->value, 'label' => $level->label()])->toArray()"
            placeholder="Pilih Tingkat Risiko"
            searchPlaceholder="Cari risiko..."
            :error="$errors->has('risk_level')"
        />
        @error('risk_level') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Level Kontrol CoE <span class="text-red-500">*</span>
        </label>
        <x-searchable-select
            wire:model.live="coe_control_level"
            :options="collect($coeLevels)->map(fn($level) => ['value' => $level->value, 'label' => $level->label()])->toArray()"
            placeholder="Pilih Level CoE"
            searchPlaceholder="Cari level..."
            :error="$errors->has('coe_control_level')"
        />
        @error('coe_control_level') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Baseline (Rp)</label>
        <input wire:model="pricing_baseline" type="number" step="0.01" min="0"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="0">
        @error('pricing_baseline') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
        <textarea wire:model="notes" rows="2" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Catatan tambahan"></textarea>
        @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>
</div>
