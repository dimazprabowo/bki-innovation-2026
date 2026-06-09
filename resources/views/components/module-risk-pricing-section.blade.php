@props(['riskLevels' => []])

<div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-3">
    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Risk & Pricing</h4>
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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Dasar</label>
            <div x-data="{
                displayValue: '{{ $pricing_baseline ?? '' }}',
                formatValue(value) {
                    if (!value || value === '') return '';
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
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
                <input type="text"
                    x-model="displayValue"
                    @input="$wire.set('pricing_baseline', parseValue($el.value))"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Rp 0">
                @error('pricing_baseline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi (Hari)</label>
            <input wire:model="duration" type="number" min="0"
                placeholder="Masukkan durasi"
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
