{{-- Step 2: Modules --}}
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pilih Modul</h3>
        <button
            type="button"
            wire:click="addModule"
            wire:loading.attr="disabled"
            wire:target="addModule"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors disabled:opacity-50">
            <svg wire:loading.class.remove="inline" wire:loading.class.add="hidden" wire:target="addModule" class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <svg wire:loading wire:target="addModule" class="animate-spin w-4 h-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Tambah Modul
        </button>
    </div>

    @error('selectedModules')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror

    @php
        $moduleOptions = collect($this->modules)->map(function($m) {
            return [
                'value' => $m->id,
                'label' => $m->name,
                'sublabel' => $m->code . ' · Rp ' . number_format($m->pricing_baseline, 0, ',', '.'),
                'badge' => $m->risk_level->label(),
                'badgeClass' => $m->risk_level->badgeClass(),
            ];
        })->toArray();
    @endphp

    @forelse($selectedModules as $index => $item)
        @php
            $selectedModule = collect($this->modules)->firstWhere('id', $item['module_id'] ?? null);
            $subtotal = (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
            $hasError = $errors->has('selectedModules.'.$index.'.module_id') || $errors->has('selectedModules.'.$index.'.quantity');
        @endphp
        <div x-data="{ expanded: {{ $hasError ? 'true' : 'true' }} }"
             class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-visible">
            {{-- Header --}}
            <button type="button" @click="expanded = !expanded"
                class="w-full px-3 sm:px-4 py-3 bg-gray-50 dark:bg-gray-900/50 transition-colors hover:bg-gray-100 dark:hover:bg-gray-900 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg x-show="!expanded" x-cloak class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ $selectedModule ? $selectedModule->name : 'Modul belum dipilih' }}
                    </span>
                    @if($selectedModule)
                        <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full {{ $selectedModule->risk_level->badgeClass() }}">
                            {{ $selectedModule->risk_level->label() }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400 hidden sm:inline">Subtotal: Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    <span wire:click.stop="removeModule({{ $index }})" wire:target="removeModule({{ $index }})"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 cursor-pointer">
                        <svg wire:loading.class="hidden" wire:target="removeModule({{ $index }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <svg wire:loading wire:target="removeModule({{ $index }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </div>
            </button>

            {{-- Content --}}
            <div x-show="expanded" x-collapse class="p-3 sm:p-4 bg-gray-50 dark:bg-gray-900/50 overflow-visible">
                <div class="space-y-3">
                    <div x-data="{ moduleId: $wire.selectedModules[{{ $index }}].module_id }"
                         x-effect="if (moduleId !== $wire.selectedModules[{{ $index }}].module_id) { moduleId = $wire.selectedModules[{{ $index }}].module_id; if (moduleId) $wire.onModuleSelected({{ $index }}, moduleId) }">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Modul <span class="text-red-500">*</span></label>
                        <x-searchable-select
                            wire:model.live="selectedModules.{{ $index }}.module_id"
                            :options="$moduleOptions"
                            placeholder="Pilih modul"
                            searchPlaceholder="Cari modul..."
                            emptyText="Tidak ada modul tersedia"
                            noResultText="Modul tidak ditemukan" />
                        @error('selectedModules.'.$index.'.module_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($selectedModule)
                        <x-module-info :module="$selectedModule" />
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Qty <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                wire:model.live="selectedModules.{{ $index }}.quantity"
                                min="1"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="1">
                            @error('selectedModules.'.$index.'.quantity')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Harga Satuan</label>
                            <div class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($item['unit_price'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subtotal</label>
                            <div class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Catatan</label>
                        <input
                            type="text"
                            wire:model="selectedModules.{{ $index }}.notes"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Catatan tambahan (opsional)">
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada modul dipilih</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Klik "Tambah Modul" untuk menambahkan modul</p>
        </div>
    @endforelse

    {{-- Cost Summary --}}
    <div class="p-3 sm:p-4 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Biaya Modul</span>
            <span class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($this->base_cost, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
