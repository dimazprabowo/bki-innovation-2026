@props(['riskLevels' => []])

<div x-data="{ expanded: true }"
    @module-validation-failed.window="if (($event.detail.errorKeys || []).some(k => ['risk_level','pricing_baseline','duration','is_active'].includes(k))) expanded = true"
    class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-3">
    <div class="flex items-center justify-between mb-3">
        <button type="button" @click="expanded = !expanded" class="flex items-center gap-2 group">
            <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg x-show="!expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Risk & Pricing</h4>
        </button>
    </div>

    <div x-show="expanded" x-collapse>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tingkat Risiko <span class="text-red-500">*</span>
            </label>
            <x-searchable-select
                wire:model.live="risk_level"
                :options="collect($riskLevels)->map(fn($level) => ['value' => $level->value, 'label' => $level->label()])->toArray()"
                placeholder="Pilih tingkat risiko"
                searchPlaceholder="Cari risiko..."
                :error="$errors->has('risk_level')"
            />
            @error('risk_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Dasar <span class="text-red-500">*</span></label>
            <div x-data="{
                displayValue: '',
                formatValue(value) {
                    if (!value || value === '') return '';
                    return Number(value).toLocaleString('id-ID');
                },
                parseValue(value) {
                    if (!value || value === '') return '';
                    return value.replace(/[^0-9]/g, '');
                },
                init() {
                    this.displayValue = this.formatValue(this.$wire.get('pricing_baseline'));
                    this.$watch('$wire.pricing_baseline', (value) => {
                        this.displayValue = this.formatValue(value);
                    });
                }
            }">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 dark:text-gray-400 pointer-events-none">Rp</span>
                    <input type="text"
                        x-model="displayValue"
                        @input="$wire.set('pricing_baseline', parseValue($el.value))"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white pl-9"
                        placeholder="0">
                </div>
                @error('pricing_baseline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi (Hari) <span class="text-red-500">*</span></label>
            <input wire:model="duration" type="number" min="0"
                placeholder="Masukkan durasi (wajib)"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            @error('duration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
            @error('is_active') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>
    </div>
</div>
