@props(['module'])

@php
    $module = is_numeric($module) ? \App\Models\Module::with(['personels.competencies', 'tools.peralatan', 'deliverables', 'workOrderItems.subitems', 'workOrderReferences'])->find($module) : $module;
@endphp

@if($module)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
        {{-- Basic Info --}}
        <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Kode</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $module->code }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Durasi</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $module->duration }} hari</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Tingkat Risiko</dt>
                <dd>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $module->risk_level->badgeClass() }}">
                        {{ $module->risk_level->label() }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Harga Dasar</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format($module->pricing_baseline, 0, ',', '.') }}</dd>
            </div>
        </div>

        @if($module->notes)
            <div class="p-4 text-sm">
                <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Catatan Modul</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $module->notes }}</dd>
            </div>
        @endif

        {{-- Work Order References --}}
        @if($module->workOrderReferences->isNotEmpty())
            <div class="p-4">
                <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Dokumen Referensi</h5>
                <div class="space-y-1">
                    @foreach($module->workOrderReferences as $reference)
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span class="text-gray-700 dark:text-gray-300">{{ $reference->document_name }}</span>
                            @if($reference->document_id)
                                <span class="text-xs text-gray-400">({{ $reference->document_id }})</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Work Order Items --}}
        @if($module->workOrderItems->isNotEmpty())
            <div class="p-4">
                <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Work Order Items</h5>
                <div class="space-y-2">
                    @foreach($module->workOrderItems as $item)
                        <div class="text-sm">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item->name }}</span>
                                <span class="text-xs px-1.5 py-0.5 rounded {{ $item->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $item->nature === 'mandatory' ? 'Wajib' : 'Opsional' }}
                                </span>
                            </div>
                            @if($item->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item->description }}</p>
                            @endif
                            @if($item->subitems->isNotEmpty())
                                <ul class="mt-1 ml-4 space-y-0.5">
                                    @foreach($item->subitems as $subitem)
                                        <li class="text-xs text-gray-600 dark:text-gray-400">
                                            &bull; {{ $subitem->name }}
                                            <span class="text-gray-400">({{ $subitem->nature === 'mandatory' ? 'Wajib' : 'Opsional' }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Personels --}}
        @if($module->personels->isNotEmpty())
            <div class="p-4">
                <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Personel</h5>
                <div class="space-y-2">
                    @foreach($module->personels as $personel)
                        <div class="flex items-start justify-between gap-2 text-sm">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $personel->position_name }}</span>
                                <span class="text-gray-400 mx-1">&middot;</span>
                                <span class="text-gray-600 dark:text-gray-400">Qty: {{ $personel->quantity }}</span>
                                @if($personel->competencies->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($personel->competencies as $competency)
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">{{ $competency->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs italic text-gray-400 dark:text-gray-500 block mt-0.5">Tidak ada kompetensi yang dibutuhkan</span>
                                @endif
                            </div>
                            <span class="text-xs px-1.5 py-0.5 rounded {{ $personel->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $personel->nature === 'mandatory' ? 'Wajib' : 'Opsional' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tools --}}
        @if($module->tools->isNotEmpty())
            <div class="p-4">
                <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Peralatan</h5>
                <div class="space-y-2">
                    @foreach($module->tools as $tool)
                        <div class="flex items-center justify-between gap-2 text-sm">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $tool->peralatan?->name ?? '-' }}</span>
                                <span class="text-gray-400 mx-1">&middot;</span>
                                <span class="text-gray-600 dark:text-gray-400">Qty: {{ $tool->quantity }}</span>
                            </div>
                            <span class="text-xs {{ $tool->requires_calibration ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">
                                {{ $tool->requires_calibration ? 'Perlu Kalibrasi' : 'Tanpa Kalibrasi' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Deliverables --}}
        @if($module->deliverables->isNotEmpty())
            <div class="p-4">
                <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Deliverables</h5>
                <div class="space-y-2">
                    @foreach($module->deliverables as $deliverable)
                        <div class="flex items-start justify-between gap-2 text-sm">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $deliverable->name }}</span>
                                @if($deliverable->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $deliverable->description }}</p>
                                @endif
                            </div>
                            <span class="text-xs px-1.5 py-0.5 rounded {{ $deliverable->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $deliverable->nature === 'mandatory' ? 'Wajib' : 'Opsional' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
