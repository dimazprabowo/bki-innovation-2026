@props(['deliverables' => []])

<div x-data="{ expanded: true }" class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-3">
    <div class="flex items-center justify-between mb-3">
        <button type="button" @click="expanded = !expanded" class="flex items-center gap-2 group">
            <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg x-show="!expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Deliverable</h4>
            <span class="px-2 py-0.5 text-xs font-semibold rounded-full @if(count($deliverables) > 0) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                {{ count($deliverables) }} deliverable
            </span>
        </button>
        <button type="button" wire:click="addDeliverable" wire:key="add-deliverable-btn"
            wire:loading.attr="disabled" wire:target="addDeliverable"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors whitespace-nowrap">
            <svg wire:loading.remove wire:target="addDeliverable" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <svg wire:loading wire:target="addDeliverable" class="animate-spin w-4 h-4" wire:key="add-loading-icon-deliverable" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="addDeliverable" class="hidden sm:inline">Tambah Deliverable</span>
            <span wire:loading.remove wire:target="addDeliverable" class="sm:hidden">Tambah</span>
            <span wire:loading wire:target="addDeliverable" class="hidden sm:inline">Memproses</span>
            <span wire:loading wire:target="addDeliverable" class="sm:hidden">...</span>
        </button>
    </div>

    <div x-show="expanded" x-collapse>

    <div x-data="{ draggedDeliverable: null, dragOverDeliverableIndex: null, touchStartX: 0, touchStartY: 0, isDragging: false, touchTimer: null }" class="space-y-3">
        @forelse($deliverables as $delIndex => $del)
            <div wire:key="deliverable-{{ $delIndex }}"
                 data-deliverable-index="{{ $delIndex }}"
                 x-data="{ delIndex: {{ $delIndex }}, expanded: @js($errors->has('deliverables.'.$delIndex.'.name') || $errors->has('deliverables.'.$delIndex.'.nature') || $errors->has('deliverables.'.$delIndex.'.is_active')) }"
                 class="bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-200">
                <div class="p-4 cursor-move border border-transparent rounded-lg transition-colors"
                     :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-400 dark:border-blue-500': dragOverDeliverableIndex === delIndex && draggedDeliverable !== null && draggedDeliverable !== delIndex, 'hover:border-blue-300 dark:hover:border-blue-600': !(dragOverDeliverableIndex === delIndex && draggedDeliverable !== null && draggedDeliverable !== delIndex) }"
                     draggable="true"
                     @dragstart="draggedDeliverable = delIndex; $el.classList.add('opacity-50', 'ring-2', 'ring-blue-500')"
                     @dragend="draggedDeliverable = null; dragOverDeliverableIndex = null; $el.classList.remove('opacity-50', 'ring-2', 'ring-blue-500')"
                     @dragover.prevent="if (draggedDeliverable !== null && draggedDeliverable !== delIndex) { dragOverDeliverableIndex = delIndex; }"
                     @dragleave="dragOverDeliverableIndex = null"
                     @drop="if (draggedDeliverable !== null && draggedDeliverable !== delIndex) { $wire.call('reorderDeliverablesFromDrag', draggedDeliverable, delIndex); draggedDeliverable = null; dragOverDeliverableIndex = null; }"
                     @touchstart="touchStartX = $event.touches[0].clientX; touchStartY = $event.touches[0].clientY; isDragging = false; touchTimer = setTimeout(() => { if (!isDragging) { draggedDeliverable = delIndex; $el.classList.add('opacity-50', 'ring-2', 'ring-blue-500'); } }, 200);"
                     @touchmove="if (draggedDeliverable !== null && $event.cancelable) { $event.preventDefault(); const touch = $event.touches[0]; const element = document.elementFromPoint(touch.clientX, touch.clientY); if (element) { const dropTarget = element.closest('[data-deliverable-index]'); if (dropTarget) { const targetIndex = parseInt(dropTarget.getAttribute('data-deliverable-index')); if (targetIndex !== undefined && targetIndex !== draggedDeliverable) { dragOverDeliverableIndex = targetIndex; } } } }"
                     @touchend="if (draggedDeliverable !== null && dragOverDeliverableIndex !== null && draggedDeliverable !== dragOverDeliverableIndex) { $wire.call('reorderDeliverablesFromDrag', draggedDeliverable, dragOverDeliverableIndex); } draggedDeliverable = null; dragOverDeliverableIndex = null; isDragging = true; clearTimeout(touchTimer); $el.classList.remove('opacity-50', 'ring-2', 'ring-blue-500');"
                     @touchcancel="draggedDeliverable = null; dragOverDeliverableIndex = null; isDragging = true; clearTimeout(touchTimer); $el.classList.remove('opacity-50', 'ring-2', 'ring-blue-500');">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                            </svg>
                        </div>
                        <button type="button" @click="expanded = !expanded" class="flex-shrink-0 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg x-show="expanded" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg x-show="!expanded" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $del['name'] ?? 'Deliverable ' . ($delIndex + 1) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $del['description'] ?? 'Belum ada deskripsi' }}
                            </p>
                        </div>
                        <button type="button" wire:click="removeDeliverable({{ $delIndex }})"
                            wire:key="remove-deliverable-{{ $delIndex }}"
                            wire:loading.attr="disabled"
                            wire:target="removeDeliverable({{ $delIndex }})"
                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0 disabled:opacity-50">
                            <svg wire:loading.class="hidden" wire:target="removeDeliverable({{ $delIndex }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <svg wire:loading wire:target="removeDeliverable({{ $delIndex }})" wire:key="loading-deliverable-{{ $delIndex }}" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-collapse class="p-4 pt-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Item <span class="text-red-500">*</span></label>
                            <input wire:model="deliverables.{{ $delIndex }}.name" type="text"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                                placeholder="Masukkan nama deliverable (wajib)">
                            @error('deliverables.'.$delIndex.'.name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea wire:model="deliverables.{{ $delIndex }}.description" rows="3"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                                placeholder="Masukkan deskripsi deliverable"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sifat <span class="text-red-500">*</span></label>
                            <select wire:model="deliverables.{{ $delIndex }}.nature"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200">
                                <option value="mandatory">Wajib</option>
                                <option value="optional">Opsional</option>
                            </select>
                            @error('deliverables.'.$delIndex.'.nature')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status <span class="text-red-500">*</span></label>
                            <select wire:model="deliverables.{{ $delIndex }}.is_active"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            @error('deliverables.'.$delIndex.'.is_active')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Belum ada deliverable
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    Klik "Tambah Deliverable" untuk menambahkan deliverable
                </p>
            </div>
        @endforelse
    </div>
    </div>
</div>
