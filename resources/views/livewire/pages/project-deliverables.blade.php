<div class="space-y-6" wire:poll.visible="refreshFileStatus">
    {{-- Back Button --}}
    <div x-data="{ loading: false }">
        <button type="button" @click="loading = true; $wire.goBack()"
            :disabled="loading"
            class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors disabled:opacity-50">
            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <svg x-show="loading" x-cloak class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Kembali ke Daftar Project
        </button>
    </div>

    @php $groups = $this->moduleGroups; @endphp

    {{-- Project Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-base shadow-md shadow-emerald-500/20">
                        {{ substr($project->code, 0, 2) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">{{ $project->name }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">{{ $project->code }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->status->badgeClass() }}">
                            {{ $project->status->label() }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->risk_level->badgeClass() }}">
                            {{ $project->risk_level->label() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {{-- Overall Progress --}}
        @php
            $totalDeliverables = collect($groups)->sum(fn ($g) => count($g['deliverables']));
            $completedDeliverables = collect($groups)->sum(fn ($g) => $g['deliverables']->filter(fn ($d) => $g['uploadedFiles']->has($d->id))->count());
        @endphp
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Deliverables</span>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $completedDeliverables }}/{{ $totalDeliverables }} deliverables</span>
            </div>
        </div>
    </div>

    @if(empty($groups))
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-16 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Tidak ada deliverable</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Modul yang dipilih belum memiliki deliverable yang aktif.</p>
        </div>
    @else
        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 px-1">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-red-200 dark:bg-red-900/30"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Wajib</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-gray-300 dark:bg-gray-700"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Opsional</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-emerald-200 dark:bg-emerald-900/30"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Sudah diunggah</span>
            </div>
        </div>

        <div class="flex items-center gap-2 px-1">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Upload Deliverables per Modul</h2>
        </div>

        <div class="space-y-3">
            @foreach($groups as $group)
                @php
                    $module = $group['module'];
                    $deliverables = $group['deliverables'];
                    $uploadedFiles = $group['uploadedFiles'];
                    $moduleNumber = $loop->iteration;
                    $moduleHasUpload = $uploadedFiles->isNotEmpty();
                    $mandatoryDeliverables = $deliverables->filter(fn ($d) => $d->nature === 'mandatory');
                    $moduleComplete = $mandatoryDeliverables->isEmpty()
                        ? $moduleHasUpload
                        : $mandatoryDeliverables->every(fn ($d) => $uploadedFiles->has($d->id));
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md"
                     x-data="{ open: true }">
                    {{-- Module Header --}}
                    <div class="px-5 py-4 cursor-pointer select-none transition-colors {{ $moduleComplete ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : 'bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-900/70' }} border-b border-gray-200 dark:border-gray-700"
                         @click="open = !open">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex-shrink-0 w-9 h-9 rounded-lg {{ $moduleComplete ? 'bg-emerald-500' : 'bg-indigo-600' }} text-white flex items-center justify-center font-bold text-sm shadow-sm transition-colors">
                                    {{ $moduleNumber }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $module->name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500">{{ $module->code }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                        @php
                                            $completedDeliverables = $deliverables->filter(fn ($d) => $uploadedFiles->has($d->id))->count();
                                            $totalDeliverables = count($deliverables);
                                        @endphp
                                        <span class="text-[11px] font-medium {{ $moduleComplete ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400' }}">{{ $completedDeliverables }}/{{ $totalDeliverables }} deliverables</span>
                                        @if($mandatoryDeliverables->count() > 0)
                                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                            <span class="inline-flex items-center gap-1 text-[11px] text-red-500 dark:text-red-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                {{ $mandatoryDeliverables->count() }} wajib
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Collapsible Content --}}
                    <div x-show="open" x-collapse x-cloak>
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($deliverables as $deliverable)
                                @php
                                    $files = $uploadedFiles->get($deliverable->id, collect());
                                    $isMandatory = $deliverable->nature === 'mandatory';
                                    $hasFiles = $files->isNotEmpty();
                                @endphp
                                <div class="px-5 py-4 {{ $isMandatory && !$hasFiles ? 'bg-red-50/20 dark:bg-red-900/5' : '' }}">
                                    {{-- Deliverable Info --}}
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-lg {{ $hasFiles ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : ($isMandatory ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400') }} flex items-center justify-center transition-colors">
                                                @if($hasFiles)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                @elseif($isMandatory)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliverable->name }}</span>
                                                    @if($hasFiles)
                                                        <span class="flex-shrink-0 text-[10px] px-1.5 py-0.5 rounded-full font-semibold bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 tabular-nums">
                                                            {{ $files->count() }} file
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($deliverable->description)
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-relaxed">{{ $deliverable->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded font-semibold uppercase tracking-wide {{ $isMandatory ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                            {{ $isMandatory ? 'Wajib' : 'Opsional' }}
                                        </span>
                                    </div>

                                    {{-- Uploaded Files --}}
                                    @if($hasFiles)
                                        <div class="space-y-1.5 mb-3">
                                            @foreach($files as $file)
                                                @php
                                                    $isProcessing = $file->file_status === 'processing';
                                                    $isFailed = $file->file_status === 'failed';
                                                @endphp
                                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border transition-colors {{ $isProcessing ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : ($isFailed ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-gray-50 dark:bg-gray-700/40 border-gray-200 dark:border-gray-600/50 hover:border-gray-300 dark:hover:border-gray-600') }}">
                                                    <div class="flex-shrink-0 w-8 h-8 rounded-md flex items-center justify-center {{ $isProcessing ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-500' : ($isFailed ? 'bg-red-100 dark:bg-red-900/30 text-red-500' : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400') }}">
                                                        @if($isProcessing)
                                                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                        @elseif($isFailed)
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        @php
                                                            $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                                            $isPreviewable = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'pdf']);
                                                        @endphp
                                                        @if($isProcessing)
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $file->file_name }}</p>
                                                            <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium">Sedang memproses file...</p>
                                                        @elseif($isFailed)
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $file->file_name }}</p>
                                                            <p class="text-[11px] text-red-600 dark:text-red-400 font-medium">Gagal: {{ $file->file_error ?? 'Terjadi kesalahan' }}</p>
                                                        @else
                                                            @if($isPreviewable)
                                                                <button wire:click="previewDeliverable({{ $file->id }})"
                                                                    wire:key="preview-{{ $file->id }}"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="previewDeliverable({{ $file->id }})"
                                                                    class="text-sm font-medium text-gray-900 dark:text-white truncate text-left hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline transition-colors disabled:opacity-50"
                                                                    title="Klik untuk preview">
                                                                    <span wire:loading.class.add="hidden" wire:target="previewDeliverable({{ $file->id }})" wire:key="preview-name-{{ $file->id }}">{{ $file->file_name }}</span>
                                                                    <svg wire:loading wire:target="previewDeliverable({{ $file->id }})" wire:key="preview-spin-{{ $file->id }}" class="animate-spin inline w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $file->file_name }}</p>
                                                            @endif
                                                            <div class="flex items-center gap-2 mt-0.5">
                                                                @if($file->file_size)
                                                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 tabular-nums">{{ number_format($file->file_size / 1024, 1) }} KB</span>
                                                                    <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                                                @endif
                                                                <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $file->uploader?->name ?? '-' }}</span>
                                                                <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                                                <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $file->created_at?->format('d M Y, H:i') }}</span>
                                                            </div>
                                                            @if($file->notes)
                                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 italic leading-relaxed line-clamp-2">"{{ $file->notes }}"</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-1 flex-shrink-0">
                                                        @if(!$isProcessing && !$isFailed)
                                                            <button wire:click="downloadDeliverable({{ $file->id }})"
                                                                wire:key="dl-btn-{{ $file->id }}"
                                                                wire:loading.attr="disabled"
                                                                wire:target="downloadDeliverable({{ $file->id }})"
                                                                class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-md transition-all disabled:opacity-50"
                                                                title="Download">
                                                                <svg wire:loading.class.add="hidden" wire:target="downloadDeliverable({{ $file->id }})" wire:key="dl-icon-{{ $file->id }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                <svg wire:loading wire:target="downloadDeliverable({{ $file->id }})" wire:key="dl-spin-{{ $file->id }}" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                        @can('manageDeliverables', $project)
                                                            <button wire:click="confirmDelete({{ $file->id }})"
                                                                wire:key="del-btn-{{ $file->id }}"
                                                                wire:loading.attr="disabled"
                                                                wire:target="confirmDelete({{ $file->id }})"
                                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-all disabled:opacity-50"
                                                                title="Hapus">
                                                                <svg wire:loading.class.add="hidden" wire:target="confirmDelete({{ $file->id }})" wire:key="del-icon-{{ $file->id }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                <svg wire:loading wire:target="confirmDelete({{ $file->id }})" wire:key="del-spin-{{ $file->id }}" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Upload Form --}}
                                    @can('manageDeliverables', $project)
                                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 space-y-3">
                                            {{-- File Upload Area --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">File <span class="text-red-500">*</span></label>
                                                @if(isset($uploads[$deliverable->id]) && $uploads[$deliverable->id] && is_object($uploads[$deliverable->id]))
                                                    {{-- File Selected --}}
                                                    <div class="flex items-center gap-2 px-3 py-2.5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg">
                                                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <span class="text-xs text-indigo-700 dark:text-indigo-300 truncate flex-1 font-medium">{{ $uploads[$deliverable->id]->getClientOriginalName() }}</span>
                                                        <span class="text-xs text-indigo-500 dark:text-indigo-400">{{ number_format($uploads[$deliverable->id]->getSize() / 1024, 0) }} KB</span>
                                                    </div>
                                                @else
                                                    {{-- Upload Area with Progress --}}
                                                    <div x-data="{ uploading: false, progress: 0 }"
                                                         x-on:livewire-upload-start="uploading = true"
                                                         x-on:livewire-upload-finish="uploading = false; progress = 0"
                                                         x-on:livewire-upload-cancel="uploading = false"
                                                         x-on:livewire-upload-error="uploading = false"
                                                         x-on:livewire-upload-progress="progress = $event.detail.progress">
                                                        <label class="flex flex-col items-center justify-center w-full px-3 py-4 border-2 border-dashed rounded-lg cursor-pointer transition-colors {{ $errors->has("uploads.{$deliverable->id}") ? 'border-red-400 dark:border-red-500 bg-red-50/50 dark:bg-red-900/10' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500 bg-gray-50/50 dark:bg-gray-700/20' }}">
                                                            <div x-show="!uploading" class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                                </svg>
                                                                <span>Klik untuk memilih file</span>
                                                            </div>
                                                            <div x-show="uploading" x-cloak class="flex items-center justify-center gap-2">
                                                                <svg class="animate-spin w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                <span class="text-sm text-indigo-600 font-medium" x-text="progress + '%'"></span>
                                                            </div>
                                                            <input type="file" wire:model="uploads.{{ $deliverable->id }}" class="hidden"
                                                                accept=".{{ implode(',.', get_allowed_mimes_array('project_deliverable')) }}">
                                                        </label>
                                                    </div>
                                                @endif
                                                @if($errors->has("uploads.{$deliverable->id}"))
                                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $errors->first("uploads.{$deliverable->id}") }}</p>
                                                @endif
                                                <p class="mt-1.5 text-[11px] text-gray-400 dark:text-gray-500">{{ get_upload_config_display('project_deliverable') }}</p>
                                            </div>

                                            {{-- Notes --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Catatan</label>
                                                <textarea
                                                    wire:model.blur="notes.{{ $deliverable->id }}"
                                                    placeholder="Tambahkan catatan untuk file ini (opsional)"
                                                    rows="2"
                                                    class="w-full px-3 py-2 text-sm border rounded-lg transition-all resize-none {{ $errors->has("notes.{$deliverable->id}") ? 'border-red-400 dark:border-red-500 focus:ring-red-500/20' : 'border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400' }} dark:bg-gray-700 dark:text-white"></textarea>
                                                @if($errors->has("notes.{$deliverable->id}"))
                                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $errors->first("notes.{$deliverable->id}") }}</p>
                                                @endif
                                            </div>

                                            {{-- Upload Button --}}
                                            <div class="flex justify-end pt-1">
                                                <button type="button"
                                                    wire:key="upload-btn-{{ $deliverable->id }}"
                                                    wire:click="uploadDeliverable({{ $deliverable->id }}, {{ $module->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="uploadDeliverable({{ $deliverable->id }}, {{ $module->id }})"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                                    {{-- Normal icon --}}
                                                    <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="uploadDeliverable({{ $deliverable->id }}, {{ $module->id }})" wire:key="upload-icon-{{ $deliverable->id }}" class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    {{-- Spinner icon --}}
                                                    <svg wire:loading wire:target="uploadDeliverable({{ $deliverable->id }}, {{ $module->id }})" wire:key="upload-spinner-{{ $deliverable->id }}" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    {{-- Normal text --}}
                                                    <span wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="uploadDeliverable({{ $deliverable->id }}, {{ $module->id }})" wire:key="upload-text-{{ $deliverable->id }}" class="inline-block">Upload</span>
                                                    {{-- Loading text --}}
                                                    <span wire:loading wire:target="uploadDeliverable({{ $deliverable->id }}, {{ $module->id }})" wire:key="upload-loading-text-{{ $deliverable->id }}">Uploading...</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" @click="$wire.set('showDeleteModal', false)"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md z-10 p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2 2 0 01-2.083 1.327H7.932a2 2 0 01-2.083-1.327L4.265 6.79m15.968 0a48.66 48.66 0 00-7.655-.42c-2.57 0-5.14.137-7.655.42m0 0L5.94 5.5A2 2 0 017.887 3.5h8.226a2 2 0 011.947 2l.183 1.29"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Hapus Deliverable</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus file ini? Aksi ini tidak dapat dibatalkan.</p>
                    <div class="flex items-center justify-center gap-3">
                        <x-cancel-button wire:click="closeDeleteModal" target="closeDeleteModal" />
                        <button wire:click="deleteDeliverable"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            wire:target="deleteDeliverable">
                            <svg wire:loading wire:target="deleteDeliverable" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Preview Modal --}}
    <div x-data="{ show: false, fileName: '', dataUri: '', isPdf: false }"
         @open-preview.window="show = true; fileName = $event.detail.fileName; dataUri = $event.detail.dataUri; isPdf = $event.detail.isPdf"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="show = false"
         @click.self="show = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/70 transition-opacity" @click="show = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="fileName"></span>
                    </div>
                    <button @click="show = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                {{-- Content --}}
                <div class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900 flex items-center justify-center p-4">
                    <template x-if="!isPdf">
                        <img :src="dataUri" :alt="fileName" class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                    </template>
                    <template x-if="isPdf">
                        <iframe :src="dataUri" class="w-full h-[70vh] rounded-lg border-0" :title="fileName"></iframe>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
