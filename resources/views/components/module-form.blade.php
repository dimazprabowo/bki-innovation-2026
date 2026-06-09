<div class="space-y-6">
    <!-- Basic Information -->
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

    </div>

    <!-- Work Order Items -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Work Order Items</h4>
            <button type="button" wire:click="addWorkOrderItem"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                + Tambah Item
            </button>
        </div>

        @forelse($workOrderItems as $itemIndex => $item)
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Urutan</label>
                            <input wire:model="workOrderItems.{{ $itemIndex }}.order" type="number" min="1"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Item</label>
                            <input wire:model="workOrderItems.{{ $itemIndex }}.name" type="text"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Nama item">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea wire:model="workOrderItems.{{ $itemIndex }}.description" rows="2"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Deskripsi item"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sifat</label>
                            <select wire:model="workOrderItems.{{ $itemIndex }}.nature"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="mandatory">Wajib</option>
                                <option value="optional">Opsional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                            <select wire:model="workOrderItems.{{ $itemIndex }}.is_active"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" wire:click="removeWorkOrderItem({{ $itemIndex }})"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Subitems -->
                <div class="mt-3 ml-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Subitems</span>
                        <button type="button" wire:click="addWorkOrderSubitem({{ $itemIndex }})"
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            + Tambah Subitem
                        </button>
                    </div>

                    @forelse($item['subitems'] ?? [] as $subitemIndex => $subitem)
                        <div class="bg-white dark:bg-gray-800 rounded p-3 mb-2 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Urutan</label>
                                        <input wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.order" type="number" min="1"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Work Order</label>
                                        <input wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.name" type="text"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                            placeholder="Nama work order">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Uraian</label>
                                        <textarea wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.description" rows="2"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                            placeholder="Uraian work order"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Sifat</label>
                                        <select wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.nature"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            <option value="mandatory">Wajib</option>
                                            <option value="optional">Opsional</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                                        <select wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.is_active"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeWorkOrderSubitem({{ $itemIndex }}, {{ $subitemIndex }})"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-4">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">Belum ada subitem</p>
                    @endforelse
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada work order item</p>
        @endforelse
    </div>

    <!-- Work Order References -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Referensi Work Order</h4>
            <button type="button" wire:click="addWorkOrderReference"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                + Tambah Referensi
            </button>
        </div>

        @forelse($workOrderReferences as $refIndex => $ref)
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Dokumen</label>
                            <input wire:model="workOrderReferences.{{ $refIndex }}.document_name" type="text"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Nama dokumen">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Document ID</label>
                            <input wire:model="workOrderReferences.{{ $refIndex }}.document_id" type="text"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Document ID">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">File</label>
                            <input type="file"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <button type="button" wire:click="removeWorkOrderReference({{ $refIndex }})"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada referensi</p>
        @endforelse
    </div>

    <!-- Teams -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Tim Pelaksana</h4>
            <button type="button" wire:click="addTeam"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                + Tambah Tim
            </button>
        </div>

        @forelse($teams as $teamIndex => $team)
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Jabatan</label>
                            <input wire:model="teams.{{ $teamIndex }}.position_name" type="text"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Nama jabatan">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jumlah</label>
                            <input wire:model="teams.{{ $teamIndex }}.quantity" type="number" min="1"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="1">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sifat</label>
                            <select wire:model="teams.{{ $teamIndex }}.nature"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="mandatory">Wajib</option>
                                <option value="optional">Opsional</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Kompetensi</label>
                            <x-multi-searchable-select
                                wire:model.live="teams.{{ $teamIndex }}.competencies"
                                :options="collect($competencies)->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                                placeholder="Pilih kompetensi"
                                searchPlaceholder="Cari kompetensi..."
                            />
                        </div>
                    </div>
                    <button type="button" wire:click="removeTeam({{ $teamIndex }})"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada tim</p>
        @endforelse
    </div>

    <!-- Tools -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Alat</h4>
            <button type="button" wire:click="addTool"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                + Tambah Alat
            </button>
        </div>

        @forelse($tools as $toolIndex => $tool)
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Alat</label>
                            <input wire:model="tools.{{ $toolIndex }}.name" type="text"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Nama alat">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jumlah</label>
                            <input wire:model="tools.{{ $toolIndex }}.quantity" type="number" min="1"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="1">
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="tools.{{ $toolIndex }}.requires_calibration"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Perlu Kalibrasi</span>
                            </label>
                        </div>
                    </div>
                    <button type="button" wire:click="removeTool({{ $toolIndex }})"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada alat</p>
        @endforelse
    </div>

    <!-- Deliverables -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Deliverable (Struktur)</h4>
            <button type="button" wire:click="addDeliverable"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                + Tambah Deliverable
            </button>
        </div>

        @forelse($deliverables as $delIndex => $del)
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Urutan</label>
                            <input wire:model="deliverables.{{ $delIndex }}.order" type="number" min="1"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Item</label>
                            <input wire:model="deliverables.{{ $delIndex }}.name" type="text"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Nama deliverable">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea wire:model="deliverables.{{ $delIndex }}.description" rows="2"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Deskripsi deliverable"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sifat</label>
                            <select wire:model="deliverables.{{ $delIndex }}.nature"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="mandatory">Wajib</option>
                                <option value="optional">Opsional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                            <select wire:model="deliverables.{{ $delIndex }}.is_active"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" wire:click="removeDeliverable({{ $delIndex }})"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada deliverable</p>
        @endforelse
    </div>

    <!-- Risk & Pricing -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Risk & Pricing</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Dasar (Rp)</label>
                <input wire:model="pricing_baseline" type="number" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="0">
                @error('pricing_baseline') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi (Hari)</label>
                <input wire:model="duration" type="number" min="0"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Contoh: 30">
                @error('duration') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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
        </div>
    </div>

    <!-- Notes -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
        <textarea wire:model="notes" rows="2"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Catatan tambahan"></textarea>
        @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>
</div>
