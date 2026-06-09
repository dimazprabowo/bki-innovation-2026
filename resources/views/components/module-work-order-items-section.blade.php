@props(['workOrderItems' => []])

<div x-data="{ expanded: true }" class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-3">
    <div class="flex items-center justify-between mb-3">
        <button type="button" @click="expanded = !expanded" class="flex items-center gap-2 group">
            <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg x-show="!expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Work Order Items</h4>
            <span class="px-2 py-0.5 text-xs font-semibold rounded-full @if(count($workOrderItems) > 0) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                {{ count($workOrderItems) }} item
            </span>
        </button>
        <button type="button" wire:click="addWorkOrderItem" wire:key="add-work-order-item-btn"
            wire:loading.attr="disabled" wire:target="addWorkOrderItem"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors whitespace-nowrap">
            <svg wire:loading.remove wire:target="addWorkOrderItem" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <svg wire:loading wire:target="addWorkOrderItem" class="animate-spin w-4 h-4" wire:key="add-loading-icon-work-order-item" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="addWorkOrderItem" class="hidden sm:inline">Tambah Item</span>
            <span wire:loading.remove wire:target="addWorkOrderItem" class="sm:hidden">Tambah</span>
            <span wire:loading wire:target="addWorkOrderItem" class="hidden sm:inline">Memproses</span>
            <span wire:loading wire:target="addWorkOrderItem" class="sm:hidden">...</span>
        </button>
    </div>

    <div x-show="expanded" x-collapse>

    <div x-data="{ draggedItem: null, dragOverIndex: null, touchStartX: 0, touchStartY: 0, isDragging: false, touchTimer: null }" class="space-y-3">
        @forelse($workOrderItems as $itemIndex => $item)
            <div wire:key="work-order-item-{{ $itemIndex }}"
                 data-item-index="{{ $itemIndex }}"
                 x-data="{ index: {{ $itemIndex }}, expanded: @js($errors->has('workOrderItems.'.$itemIndex.'.name') || $errors->has('workOrderItems.'.$itemIndex.'.nature') || $errors->has('workOrderItems.'.$itemIndex.'.is_active') || collect($item['subitems'] ?? [])->keys()->contains(fn($key) => $errors->has('workOrderItems.'.$itemIndex.'.subitems.'.$key.'.name') || $errors->has('workOrderItems.'.$itemIndex.'.subitems.'.$key.'.nature') || $errors->has('workOrderItems.'.$itemIndex.'.subitems.'.$key.'.is_active'))) }"
                 class="bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-200">
                <div class="p-4 cursor-move border border-transparent rounded-lg transition-colors"
                     :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-400 dark:border-blue-500': dragOverIndex === index && draggedItem !== null && draggedItem !== index, 'hover:border-blue-300 dark:hover:border-blue-600': !(dragOverIndex === index && draggedItem !== null && draggedItem !== index) }"
                     draggable="true"
                     @dragstart="draggedItem = index; $el.classList.add('opacity-50', 'ring-2', 'ring-blue-500')"
                     @dragend="draggedItem = null; dragOverIndex = null; $el.classList.remove('opacity-50', 'ring-2', 'ring-blue-500')"
                     @dragover.prevent="if (draggedItem !== null && draggedItem !== index) { dragOverIndex = index; }"
                     @dragleave="dragOverIndex = null"
                     @drop="if (draggedItem !== null && draggedItem !== index) { $wire.call('reorderWorkOrderItemsFromDrag', draggedItem, index); draggedItem = null; dragOverIndex = null; }"
                     @touchstart="touchStartX = $event.touches[0].clientX; touchStartY = $event.touches[0].clientY; isDragging = false; touchTimer = setTimeout(() => { if (!isDragging) { draggedItem = index; $el.classList.add('opacity-50', 'ring-2', 'ring-blue-500'); } }, 200);"
                     @touchmove="if (draggedItem !== null && $event.cancelable) { $event.preventDefault(); const touch = $event.touches[0]; const element = document.elementFromPoint(touch.clientX, touch.clientY); if (element) { const dropTarget = element.closest('[data-item-index]'); if (dropTarget) { const targetIndex = parseInt(dropTarget.getAttribute('data-item-index')); if (targetIndex !== undefined && targetIndex !== draggedItem) { dragOverIndex = targetIndex; } } } }"
                     @touchend="if (draggedItem !== null && dragOverIndex !== null && draggedItem !== dragOverIndex) { $wire.call('reorderWorkOrderItemsFromDrag', draggedItem, dragOverIndex); } draggedItem = null; dragOverIndex = null; isDragging = true; clearTimeout(touchTimer); $el.classList.remove('opacity-50', 'ring-2', 'ring-blue-500');"
                     @touchcancel="draggedItem = null; dragOverIndex = null; isDragging = true; clearTimeout(touchTimer); $el.classList.remove('opacity-50', 'ring-2', 'ring-blue-500');">
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
                                {{ $item['name'] ?? 'Item ' . ($itemIndex + 1) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $item['description'] ?? 'Belum ada deskripsi' }}
                            </p>
                        </div>
                        <button type="button" wire:click="removeWorkOrderItem({{ $itemIndex }})"
                            wire:loading.attr="disabled"
                            wire:target="removeWorkOrderItem({{ $itemIndex }})"
                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50 flex-shrink-0">
                            <svg wire:loading.class="hidden" wire:target="removeWorkOrderItem({{ $itemIndex }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <svg wire:loading wire:target="removeWorkOrderItem({{ $itemIndex }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                            <input wire:model="workOrderItems.{{ $itemIndex }}.name" type="text"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                                placeholder="Masukkan nama item (wajib)">
                            @error('workOrderItems.'.$itemIndex.'.name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea wire:model="workOrderItems.{{ $itemIndex }}.description" rows="3"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                                placeholder="Masukkan deskripsi item"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sifat <span class="text-red-500">*</span></label>
                            <select wire:model.live="workOrderItems.{{ $itemIndex }}.nature"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200">
                                <option value="mandatory">Wajib</option>
                                <option value="optional">Opsional</option>
                            </select>
                            @error('workOrderItems.'.$itemIndex.'.nature')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                            @if($item['nature'] === 'mandatory')
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Semua subitem akan otomatis wajib</p>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Subitem bisa wajib atau opsional</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status <span class="text-red-500">*</span></label>
                            <select wire:model="workOrderItems.{{ $itemIndex }}.is_active"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            @error('workOrderItems.'.$itemIndex.'.is_active')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Subitems -->
                    <div class="mt-3 ml-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Subitems</span>
                            <button type="button" wire:click="addWorkOrderSubitem({{ $itemIndex }})" wire:key="add-subitem-btn-{{ $itemIndex }}"
                                wire:loading.attr="disabled" wire:target="addWorkOrderSubitem({{ $itemIndex }})"
                                class="inline-flex items-center gap-2 px-2 py-1 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-colors">
                                <svg wire:loading.remove wire:target="addWorkOrderSubitem({{ $itemIndex }})" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <svg wire:loading wire:target="addWorkOrderSubitem({{ $itemIndex }})" class="animate-spin w-3 h-3" wire:key="add-loading-icon-subitem-{{ $itemIndex }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="addWorkOrderSubitem({{ $itemIndex }})">Tambah Subitem</span>
                                <span wire:loading wire:target="addWorkOrderSubitem({{ $itemIndex }})">Memproses</span>
                            </button>
                        </div>

                        <div x-data="{ draggedSubitem: null, dragOverSubitemIndex: null, touchStartX: 0, touchStartY: 0, isDragging: false, touchTimer: null }" class="space-y-2">
                            @forelse($item['subitems'] ?? [] as $subitemIndex => $subitem)
                                <div wire:key="subitem-{{ $itemIndex }}-{{ $subitemIndex }}"
                                     data-subitem-index="{{ $subitemIndex }}"
                                     x-data="{ subitemIndex: {{ $subitemIndex }}, expanded: @js($errors->has('workOrderItems.'.$itemIndex.'.subitems.'.$subitemIndex.'.name') || $errors->has('workOrderItems.'.$itemIndex.'.subitems.'.$subitemIndex.'.nature') || $errors->has('workOrderItems.'.$itemIndex.'.subitems.'.$subitemIndex.'.is_active')) }"
                                     class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 transition-all duration-200">
                                    <div class="p-3 cursor-move border border-transparent rounded transition-colors"
                                         :class="{ 'bg-green-50 dark:bg-green-900/20 border-green-400 dark:border-green-500': dragOverSubitemIndex === subitemIndex && draggedSubitem !== null && draggedSubitem !== subitemIndex, 'hover:border-green-300 dark:hover:border-green-600': !(dragOverSubitemIndex === subitemIndex && draggedSubitem !== null && draggedSubitem !== subitemIndex) }"
                                         draggable="true"
                                         @dragstart="draggedSubitem = subitemIndex; $el.classList.add('opacity-50', 'ring-2', 'ring-green-500')"
                                         @dragend="draggedSubitem = null; dragOverSubitemIndex = null; $el.classList.remove('opacity-50', 'ring-2', 'ring-green-500')"
                                         @dragover.prevent="if (draggedSubitem !== null && draggedSubitem !== subitemIndex) { dragOverSubitemIndex = subitemIndex; }"
                                         @dragleave="dragOverSubitemIndex = null"
                                         @drop="if (draggedSubitem !== null && draggedSubitem !== subitemIndex) { $wire.call('reorderWorkOrderSubitemsFromDrag', {{ $itemIndex }}, draggedSubitem, subitemIndex); draggedSubitem = null; dragOverSubitemIndex = null; }"
                                         @touchstart="touchStartX = $event.touches[0].clientX; touchStartY = $event.touches[0].clientY; isDragging = false; touchTimer = setTimeout(() => { if (!isDragging) { draggedSubitem = subitemIndex; $el.classList.add('opacity-50', 'ring-2', 'ring-green-500'); } }, 200);"
                                         @touchmove="if (draggedSubitem !== null && $event.cancelable) { $event.preventDefault(); const touch = $event.touches[0]; const element = document.elementFromPoint(touch.clientX, touch.clientY); if (element) { const dropTarget = element.closest('[data-subitem-index]'); if (dropTarget) { const targetIndex = parseInt(dropTarget.getAttribute('data-subitem-index')); if (targetIndex !== undefined && targetIndex !== draggedSubitem) { dragOverSubitemIndex = targetIndex; } } } }"
                                         @touchend="if (draggedSubitem !== null && dragOverSubitemIndex !== null && draggedSubitem !== dragOverSubitemIndex) { $wire.call('reorderWorkOrderSubitemsFromDrag', {{ $itemIndex }}, draggedSubitem, dragOverSubitemIndex); } draggedSubitem = null; dragOverSubitemIndex = null; isDragging = true; clearTimeout(touchTimer); $el.classList.remove('opacity-50', 'ring-2', 'ring-green-500');"
                                         @touchcancel="draggedSubitem = null; dragOverSubitemIndex = null; isDragging = true; clearTimeout(touchTimer); $el.classList.remove('opacity-50', 'ring-2', 'ring-green-500');">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-shrink-0 text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                                </svg>
                                            </div>
                                            <button type="button" @click="expanded = !expanded" class="flex-shrink-0 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                <svg x-show="expanded" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                <svg x-show="!expanded" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $subitem['name'] ?? 'Subitem ' . ($subitemIndex + 1) }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $subitem['description'] ?? 'Belum ada uraian' }}
                                                </p>
                                            </div>
                                            <button type="button" wire:click="removeWorkOrderSubitem({{ $itemIndex }}, {{ $subitemIndex }})"
                                                wire:loading.attr="disabled"
                                                wire:target="removeWorkOrderSubitem({{ $itemIndex }}, {{ $subitemIndex }})"
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50 flex-shrink-0">
                                                <svg wire:loading.class="hidden" wire:target="removeWorkOrderSubitem({{ $itemIndex }}, {{ $subitemIndex }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <svg wire:loading wire:target="removeWorkOrderSubitem({{ $itemIndex }}, {{ $subitemIndex }})" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div x-show="expanded" x-collapse class="p-3 pt-0">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <div class="md:col-span-2">
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Work Order <span class="text-red-500">*</span></label>
                                                <input wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.name" type="text"
                                                    class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                                                    placeholder="Masukkan nama work order (wajib)">
                                                @error('workOrderItems.'.$itemIndex.'.subitems.'.$subitemIndex.'.name')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Uraian</label>
                                                <textarea wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.description" rows="2"
                                                    class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200"
                                                    placeholder="Masukkan uraian work order"></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Sifat <span class="text-red-500">*</span></label>
                                                <select wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.nature"
                                                    @if($item['nature'] === 'mandatory') disabled @endif
                                                    class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200 @if($item['nature'] === 'mandatory') opacity-60 cursor-not-allowed bg-gray-100 dark:bg-gray-800 @endif">
                                                    <option value="mandatory">Wajib</option>
                                                    <option value="optional">Opsional</option>
                                                </select>
                                                @error('workOrderItems.'.$itemIndex.'.subitems.'.$subitemIndex.'.nature')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                                @if($item['nature'] === 'mandatory')
                                                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">Item parent wajib, subitem harus wajib</p>
                                                @endif
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status <span class="text-red-500">*</span></label>
                                                <select wire:model="workOrderItems.{{ $itemIndex }}.subitems.{{ $subitemIndex }}.is_active"
                                                    class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-500/40 dark:focus:border-blue-500 transition-all duration-200">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
                                                @error('workOrderItems.'.$itemIndex.'.subitems.'.$subitemIndex.'.is_active')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Belum ada subitem
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        Klik "Tambah Subitem" untuk menambahkan subitem
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Belum ada work order item
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    Klik "Tambah Item" untuk menambahkan work order item
                </p>
            </div>
        @endforelse
    </div>
    </div>
</div>
