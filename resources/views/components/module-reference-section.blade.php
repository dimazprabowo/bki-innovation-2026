@props(['workOrderReferences'])

<div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-3">
    <div class="flex items-center justify-between mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Referensi Work Order
            </label>
            <span class="px-2 py-0.5 text-xs font-semibold rounded-full @if(count($workOrderReferences) > 0) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif sm:ml-2 mt-1 sm:mt-0">
                {{ count($workOrderReferences) }} referensi
            </span>
        </div>
        <button type="button" wire:click="addWorkOrderReference" wire:key="add-reference-btn"
            wire:loading.attr="disabled" wire:target="addWorkOrderReference"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors whitespace-nowrap">
            @if(count($workOrderReferences) === 0)
                <svg wire:loading.remove wire:target="addWorkOrderReference" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <svg wire:loading wire:target="addWorkOrderReference" class="animate-spin w-4 h-4" wire:key="add-loading-icon-empty" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="addWorkOrderReference" class="hidden sm:inline">Tambah Referensi</span>
                <span wire:loading.remove wire:target="addWorkOrderReference" class="sm:hidden">Tambah</span>
                <span wire:loading wire:target="addWorkOrderReference" class="hidden sm:inline">Memproses</span>
                <span wire:loading wire:target="addWorkOrderReference" class="sm:hidden">...</span>
            @else
                <svg wire:loading.remove wire:target="addWorkOrderReference" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <svg wire:loading wire:target="addWorkOrderReference" class="animate-spin w-4 h-4" wire:key="add-loading-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="addWorkOrderReference" class="hidden sm:inline">Tambah Referensi</span>
                <span wire:loading.remove wire:target="addWorkOrderReference" class="sm:hidden">Tambah</span>
                <span wire:loading wire:target="addWorkOrderReference" class="hidden sm:inline">Memproses</span>
                <span wire:loading wire:target="addWorkOrderReference" class="sm:hidden">...</span>
            @endif
        </button>
    </div>

    @if(count($workOrderReferences) > 0)
        <div class="space-y-3">
            @foreach($workOrderReferences as $index => $reference)
                @php
                    $isExisting = isset($reference['id']) && is_numeric($reference['id']);
                @endphp
                <div wire:key="reference-{{ $reference['id'] }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    @if($isExisting)
                        {{-- Existing Data - Read Only Display --}}
                        @php
                            $isProcessing = isset($reference['file_status']) && in_array($reference['file_status'], ['pending', 'processing']);
                            $isFailed = isset($reference['file_status']) && $reference['file_status'] === 'failed';
                            $isCompleted = isset($reference['file_status']) && $reference['file_status'] === 'completed';
                        @endphp
                        <div class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            @if($isProcessing)
                                <svg class="animate-spin w-5 h-5 text-blue-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            @elseif($isFailed)
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $reference['document_name'] ?? 'Referensi' }}</p>
                                @if(isset($reference['document_id']) && $reference['document_id'])
                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $reference['document_id'] }}</p>
                                @endif
                                @if(isset($reference['file_name']) && $reference['file_name'])
                                    @php
                                        $extension = strtoupper(pathinfo($reference['file_name'], PATHINFO_EXTENSION));
                                    @endphp
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reference['file_name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $extension }}
                                        @if(isset($reference['file_size']))
                                            • {{ number_format($reference['file_size'] / 1024, 1) }} KB
                                        @endif
                                    </p>
                                @endif
                                @if($isFailed && isset($reference['file_error']))
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $reference['file_error'] }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                @if(isset($reference['file_name']) && $reference['file_name'] && $isCompleted)
                                    <button type="button" wire:click="downloadWorkOrderReferenceFile({{ $index }})"
                                        wire:loading.attr="disabled" wire:target="downloadWorkOrderReferenceFile({{ $index }})"
                                        class="text-blue-500 hover:text-blue-700 p-1.5 transition-colors bg-transparent border-none cursor-pointer disabled:opacity-50"
                                        title="Download">
                                        <svg wire:loading.remove wire:target="downloadWorkOrderReferenceFile({{ $index }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <svg wire:loading wire:target="downloadWorkOrderReferenceFile({{ $index }})" wire:key="download-loading-{{ $index }}" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </button>
                                @endif
                                <button type="button" wire:click="removeWorkOrderReference({{ $index }})"
                                    wire:key="remove-reference-existing-{{ $index }}"
                                    wire:loading.attr="disabled"
                                    wire:target="removeWorkOrderReference({{ $index }})"
                                    class="text-red-400 hover:text-red-600 p-1 disabled:opacity-50" title="Hapus referensi">
                                    <svg wire:loading.class="hidden" wire:target="removeWorkOrderReference({{ $index }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <svg wire:loading wire:target="removeWorkOrderReference({{ $index }})" wire:key="loading-reference-existing-{{ $index }}" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- New Data - Input Fields --}}
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div class="flex-1 min-w-0 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <input type="text" wire:model="workOrderReferences.{{ $index }}.document_name"
                                        placeholder="Nama dokumen (wajib)"
                                        class="w-full px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:focus:ring-blue-600 transition-colors">
                                    @error('workOrderReferences.' . $index . '.document_name') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                                <div>
                                    <input type="text" wire:model="workOrderReferences.{{ $index }}.document_id"
                                        placeholder="Document ID"
                                        class="w-full px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:focus:ring-blue-600 transition-colors">
                                </div>
                            </div>
                            <button type="button"
                                wire:click="removeWorkOrderReference({{ $index }})"
                                wire:key="remove-reference-new-{{ $index }}"
                                wire:loading.attr="disabled"
                                wire:target="removeWorkOrderReference({{ $index }})"
                                class="text-red-400 hover:text-red-600 p-1 disabled:opacity-50" title="Hapus referensi">
                                <svg wire:loading.class="hidden" wire:target="removeWorkOrderReference({{ $index }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <svg wire:loading wire:target="removeWorkOrderReference({{ $index }})" wire:key="loading-reference-new-{{ $index }}" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- File Upload --}}
                        <div>
                            @if(isset($reference['file']) && $reference['file'] && is_object($reference['file']))
                            {{-- File Selected (New Upload) --}}
                            <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs text-blue-700 dark:text-blue-300 truncate flex-1">{{ $reference['file']->getClientOriginalName() }}</span>
                                <span class="text-xs text-blue-500 dark:text-blue-400">{{ number_format($reference['file']->getSize() / 1024, 0) }} KB</span>
                                <button type="button" wire:click="removeReferenceFile({{ $index }})"
                                    wire:loading.attr="disabled"
                                    wire:target="removeReferenceFile({{ $index }})"
                                    class="text-red-500 hover:text-red-700 p-0.5 disabled:opacity-50" title="Hapus file">
                                    <svg wire:loading.class="hidden" wire:target="removeReferenceFile({{ $index }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <svg wire:loading wire:target="removeReferenceFile({{ $index }})" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            {{-- Upload Area with Loading Progress --}}
                            <div x-data="{ uploading: false, progress: 0 }"
                                 x-key="reference-upload-{{ $index }}"
                                 x-on:livewire-upload-start="uploading = true"
                                 x-on:livewire-upload-finish="uploading = false; progress = 0"
                                 x-on:livewire-upload-cancel="uploading = false"
                                 x-on:livewire-upload-error="uploading = false"
                                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <label class="flex flex-col items-center justify-center w-full px-3 py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 transition-colors bg-white dark:bg-gray-800">
                                    <div x-show="!uploading" class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <span>Klik untuk upload file referensi</span>
                                    </div>
                                    <div x-show="uploading" x-cloak class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin w-3 h-3 text-blue-500" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-xs text-blue-600" x-text="progress + '%'"></span>
                                    </div>
                                    <input type="file" wire:model="workOrderReferences.{{ $index }}.file" class="hidden"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx">
                                </label>
                            </div>
                        @endif
                        @error('workOrderReferences.' . $index . '.file') 
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ get_upload_config_display('work_order_reference') }}
                        </p>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Belum ada referensi
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Klik "Tambah Referensi" untuk menambahkan referensi work order
            </p>
        </div>
    @endif
</div>
