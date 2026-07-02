{{-- Step 5: Additional Costs & Review --}}
<div class="space-y-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Biaya Tambahan & Review</h3>

    {{-- Additional Costs --}}
    <div>
        <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Biaya Tambahan</h4>
            <button
                type="button"
                wire:click="addAdditionalCost"
                wire:loading.attr="disabled"
                wire:target="addAdditionalCost"
                class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors disabled:opacity-50">
                <svg wire:loading.class.remove="inline" wire:loading.class.add="hidden" wire:target="addAdditionalCost" class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <svg wire:loading wire:target="addAdditionalCost" class="animate-spin w-4 h-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Tambah Biaya
            </button>
        </div>

        <div class="space-y-3">
            @forelse($additionalCosts as $index => $cost)
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3 sm:p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                        <div class="flex-1 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Biaya</label>
                                    <input
                                        type="text"
                                        wire:model.live="additionalCosts.{{ $index }}.name"
                                        placeholder="Nama biaya"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('additionalCosts.{$index}.name')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div x-data="{ formattedAmount: '' }"
                                     x-init="formattedAmount = $wire.additionalCosts[{{ $index }}].amount ? Number($wire.additionalCosts[{{ $index }}].amount).toLocaleString('id-ID') : ''">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jumlah</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 dark:text-gray-400 pointer-events-none">Rp</span>
                                        <input
                                            x-ref="amountInput"
                                            type="text"
                                            inputmode="numeric"
                                            :value="formattedAmount"
                                            @input="$wire.$set('additionalCosts.{{ $index }}.amount', $event.target.value.replace(/[^0-9]/g, '') || ''); formattedAmount = $event.target.value.replace(/[^0-9]/g, '') ? parseInt($event.target.value.replace(/[^0-9]/g, '')).toLocaleString('id-ID') : ''"
                                            placeholder="0"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500 pl-9">
                                    </div>
                                    @error('additionalCosts.{$index}.amount')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Catatan</label>
                                <input
                                    type="text"
                                    wire:model.live="additionalCosts.{{ $index }}.notes"
                                    placeholder="Catatan tambahan (opsional)"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('additionalCosts.{$index}.notes')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="removeAdditionalCost({{ $index }})"
                            wire:loading.attr="disabled"
                            wire:target="removeAdditionalCost({{ $index }})"
                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 self-end sm:mt-6 disabled:opacity-50">
                            <svg wire:loading.class="hidden" wire:target="removeAdditionalCost({{ $index }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <svg wire:loading wire:target="removeAdditionalCost({{ $index }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada biaya tambahan</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Klik "Tambah Biaya" untuk menambahkan biaya tambahan</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Cost Summary --}}
    <div class="p-4 sm:p-5 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 space-y-2">
        <div class="flex items-center justify-between flex-wrap gap-2 text-sm">
            <span class="text-gray-600 dark:text-gray-400">Biaya Modul</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format($this->base_cost, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between flex-wrap gap-2 text-sm">
            <span class="text-gray-600 dark:text-gray-400">Biaya Tambahan</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format($this->additional_cost_total, 0, ',', '.') }}</span>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex items-center justify-between flex-wrap gap-2">
            <span class="font-semibold text-gray-900 dark:text-gray-100">Total</span>
            <span class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($this->total_cost, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Review Summary --}}
    <div class="p-4 sm:p-5 rounded-lg border border-gray-200 dark:border-gray-700 space-y-3">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Ringkasan Project</h4>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Kode</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $code }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Nama</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Prioritas</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Enums\ProjectPriority::from($priority)->label() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Risiko</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Enums\RiskLevel::from($risk_level)->label() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Jumlah Modul</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ count($selectedModules) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Jumlah Personel</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ count(array_filter($personelAssignments, fn($a) => !empty($a['personel_id']))) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Jumlah Peralatan</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ count(array_filter($peralatanAssignments, fn($a) => !empty($a['peralatan_id']))) }}</dd>
            </div>
        </dl>
    </div>

    {{-- Module Details --}}
    @if(!empty($selectedModules))
        <div class="space-y-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Detail Modul</h4>
            @foreach($selectedModules as $item)
                @php
                    $module = collect($this->modules)->firstWhere('id', $item['module_id'] ?? null);
                    $subtotal = (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
                @endphp
                @if($module)
                    <div x-data="{ expanded: false }"
                         class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <button type="button" @click="expanded = !expanded"
                            class="w-full px-3 sm:px-4 py-3 bg-gray-50 dark:bg-gray-900/50 transition-colors hover:bg-gray-100 dark:hover:bg-gray-900 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <svg x-show="!expanded" x-cloak class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $module->name }}</span>
                                <span class="flex-shrink-0 inline-flex items-center whitespace-nowrap px-2 py-0.5 text-xs font-medium rounded-full {{ $module->risk_level->badgeClass() }}">
                                    {{ $module->risk_level->label() }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 hidden sm:inline">
                                Qty: {{ $item['quantity'] ?? 0 }} · Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </button>
                        <div x-show="expanded" x-collapse class="p-3 sm:p-4 bg-gray-50 dark:bg-gray-900/50">
                            <x-module-info :module="$module" />
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
