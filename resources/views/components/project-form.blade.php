<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Kode Project <span class="text-red-500">*</span>
            </label>
            <input wire:model="code" type="text" 
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Contoh: PRJ001">
            @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nama Project <span class="text-red-500">*</span>
            </label>
            <input wire:model="name" type="text" 
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Nama project">
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

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tingkat Risiko Project
            </label>
            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Risiko Otomatis:</span>
                    </div>
                    @php
                        $currentRiskLevel = collect($riskLevels)->firstWhere('value', $this->risk_level ?? 'low');
                    @endphp
                    @if($currentRiskLevel)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $currentRiskLevel->badgeClass() }}">
                            {{ $currentRiskLevel->label() }}
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                            Rendah
                        </span>
                    @endif
                </div>
                <div class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                    <p class="font-medium">📊 Logika Perhitungan:</p>
                    <ul class="ml-4 space-y-0.5 list-disc">
                        <li>Ada <strong>minimal 1 modul Tinggi</strong> → Project = <strong>Tinggi</strong></li>
                        <li>Tidak ada Tinggi tapi ada <strong>Sedang</strong> → Project = <strong>Sedang</strong></li>
                        <li>Semua <strong>Rendah</strong> → Project = <strong>Rendah</strong></li>
                    </ul>
                    <p class="mt-2 pt-2 border-t border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-400 font-medium">
                        ⚠️ Risiko <strong>Tinggi</strong> akan otomatis masuk ke <strong>CoE Review</strong>
                    </p>
                </div>
            </div>
            @error('risk_level') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
            <textarea wire:model="notes" rows="2" 
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Catatan tambahan"></textarea>
            @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Modul yang Digunakan</h4>
            <button type="button" wire:click="addModule"
                wire:loading.attr="disabled"
                wire:target="addModule"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                <svg wire:loading.class="hidden" wire:target="addModule" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <svg wire:loading wire:target="addModule" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="addModule">Tambah Modul</span>
                <span wire:loading wire:target="addModule">Menambahkan...</span>
            </button>
        </div>

        @if(empty($selectedModules))
            <div class="text-center py-8 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada modul dipilih</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Klik tombol "Tambah Modul" untuk memulai</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($selectedModules as $index => $module)
                    @php
                        $selectedModuleData = null;
                        if (!empty($module['module_id'])) {
                            $selectedModuleData = $availableModules->firstWhere('id', $module['module_id']);
                        }
                    @endphp
                    <div class="relative p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <button type="button" 
                            wire:click="confirmRemoveModule({{ $index }})" 
                            wire:loading.attr="disabled"
                            wire:target="confirmRemoveModule({{ $index }})"
                            class="absolute top-2 right-2 p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded transition-colors disabled:opacity-50"
                            title="Hapus modul">
                            <svg wire:loading.class="hidden" wire:target="confirmRemoveModule({{ $index }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <svg wire:loading wire:target="confirmRemoveModule({{ $index }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        
                        <!-- Baris 1: Modul & Risiko -->
                        <div class="grid grid-cols-12 gap-3 mb-3">
                            <div class="col-span-12 md:col-span-9">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Modul <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    wire:model.live="selectedModules.{{ $index }}.module_id"
                                    :options="collect($availableModules)->map(fn($m) => [
                                        'value' => $m->id, 
                                        'label' => $m->code . ' - ' . $m->name . ' | ' . 
                                                   ($m->risk_level->value === 'high' ? '🔴 Tinggi' : 
                                                   ($m->risk_level->value === 'medium' ? '🟡 Sedang' : '🟢 Rendah')) . 
                                                   ' | Rp ' . number_format($m->pricing_baseline ?? 0, 0, ',', '.')
                                    ])->toArray()"
                                    placeholder="Pilih Modul"
                                    searchPlaceholder="Cari modul..."
                                    :error="false"
                                />
                            </div>

                            <div class="col-span-12 md:col-span-3">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Risiko Modul</label>
                                @if($selectedModuleData)
                                    <div class="flex items-center justify-center h-[42px] px-3 py-2 text-sm rounded-lg {{ $selectedModuleData->risk_level->badgeClass() }} font-medium">
                                        {{ $selectedModuleData->risk_level->label() }}
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-[42px] px-3 py-2 text-sm bg-gray-100 dark:bg-gray-600 rounded-lg text-gray-500 dark:text-gray-400">
                                        -
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Baris 2: Qty, Harga, Subtotal -->
                        <div class="grid grid-cols-12 gap-3">
                            <div class="col-span-4 md:col-span-3">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Qty <span class="text-red-500">*</span></label>
                                <input wire:model.live="selectedModules.{{ $index }}.quantity" type="number" min="1"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="col-span-8 md:col-span-4">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Harga Satuan</label>
                                <div class="flex items-center h-[42px] px-3 py-2 text-sm bg-gray-100 dark:bg-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium">
                                    Rp {{ number_format($module['unit_price'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="col-span-12 md:col-span-5">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subtotal</label>
                                <div class="flex items-center h-[42px] px-3 py-2 text-sm bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-700 dark:text-blue-300 font-semibold">
                                    Rp {{ number_format(($module['quantity'] ?? 0) * ($module['unit_price'] ?? 0), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end pt-2 border-t border-gray-200 dark:border-gray-600">
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Estimasi</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format(collect($selectedModules)->sum(fn($m) => ($m['quantity'] ?? 0) * ($m['unit_price'] ?? 0)), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
