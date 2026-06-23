{{-- Step 4: Equipment Assignment --}}
<div class="space-y-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Penugasan Peralatan</h3>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        Pilih peralatan untuk setiap kebutuhan modul. Hanya peralatan yang sesuai dengan jenis yang dibutuhkan modul yang ditampilkan.
    </p>

    @if(empty($this->selectedModuleIds()))
        <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
            <p class="text-sm text-yellow-800 dark:text-yellow-400">Pilih modul terlebih dahulu di langkah 2 untuk menugaskan peralatan.</p>
        </div>
    @else
        @php
            $byModule = $this->peralatanAssignmentsByModule;
        @endphp

        @if(empty($byModule))
            <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v15a2 2 0 002 2h12a2 2 0 002-2v-4m-4.583-12.414l1.414-1.414a2 2 0 112.828 2.828l-1.414 1.414M14.5 6.5l-7 7-1.5-1.5 7-7 1.5 1.5z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Modul yang dipilih belum memiliki kebutuhan peralatan</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($byModule as $moduleGroup)
                    @php
                        $module = $moduleGroup['module'];
                        $moduleKey = 'module-peralatan-' . $module->id;
                        $tools = $moduleGroup['tools'];
                        $totalUnits = collect($tools)->sum(fn ($t) => count($t['slots']));

                        $moduleAssignmentIndices = [];
                        foreach ($tools as $toolGroup) {
                            foreach ($toolGroup['slots'] as $slot) {
                                $idx = collect($this->peralatanAssignments)->search(fn ($a) => $a['module_tool_id'] == $toolGroup['tool']->id && $a['slot'] == $slot['slot']);
                                if ($idx !== false) $moduleAssignmentIndices[] = $idx;
                            }
                        }
                        $indicesJson = json_encode($moduleAssignmentIndices);
                    @endphp

                    {{-- Module Section --}}
                    <div wire:key="{{ $moduleKey }}" x-data="{ open: true }"
                         x-effect="let idx = {{ $indicesJson }}; let errs = $wire.errors; for (let k in errs) { if (idx.some(i => k.includes('peralatanAssignments.' + i + '.'))) { open = true; break; } }"
                         class="rounded-xl border border-gray-200 dark:border-gray-700">
                        {{-- Module Header --}}
                        <button type="button" @click="open = !open"
                            class="w-full px-3 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-amber-50 to-amber-100/50 dark:from-amber-900/20 dark:to-amber-800/10 border-b border-gray-200 dark:border-gray-700 transition-colors hover:from-amber-100 hover:to-amber-100/70 dark:hover:from-amber-900/30 dark:hover:to-amber-800/15">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-amber-600 text-white flex items-center justify-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v15a2 2 0 002 2h12a2 2 0 002-2v-4m-4.583-12.414l1.414-1.414a2 2 0 112.828 2.828l-1.414 1.414M14.5 6.5l-7 7-1.5-1.5 7-7 1.5 1.5z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 text-left">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $module->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $module->code }} &middot; {{ count($tools) }} jenis peralatan &middot; {{ $totalUnits }} unit</p>
                                </div>
                                <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-transform duration-200" :class="open ? '' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Tools --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($tools as $toolGroup)
                                @php
                                    $tool = $toolGroup['tool'];
                                    $peralatanName = $tool->peralatan?->name ?? '-';
                                    $calibrationLabel = $tool->requires_calibration ? 'Perlu Kalibrasi' : 'Tanpa Kalibrasi';
                                    $toolKey = 'tool-' . $tool->id;
                                @endphp
                                <div wire:key="{{ $toolKey }}" class="p-3 sm:p-4">
                                    {{-- Tool Header --}}
                                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $peralatanName }}</span>
                                            <span class="text-xs text-gray-400 flex-shrink-0">({{ count($toolGroup['slots']) }} unit)</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-wrap flex-shrink-0">
                                            <span class="text-xs px-2 py-0.5 rounded-full {{ $tool->requires_calibration ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ $calibrationLabel }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Peralatan Unit Slots --}}
                                    @php
                                        $peralatanOptions = collect($this->peralatansForTool($tool->id))->map(fn ($p) => ['value' => $p['id'], 'label' => $p['label'], 'sublabel' => $p['sublabel'] ?? ''])->toArray();
                                    @endphp
                                    <div class="space-y-2 sm:space-y-3">
                                        @foreach($toolGroup['slots'] as $slot)
                                            @php
                                                $globalIndex = collect($this->peralatanAssignments)->search(fn ($a) => $a['module_tool_id'] == $tool->id && $a['slot'] == $slot['slot']);
                                            @endphp
                                            <div wire:key="{{ $toolKey }}-unit-{{ $slot['slot'] }}" class="flex items-center gap-2 sm:gap-3">
                                                <span class="flex-shrink-0 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 flex items-center justify-center text-[10px] sm:text-xs font-semibold">
                                                    {{ $slot['slot'] }}
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <x-searchable-select
                                                        wire:model.live="peralatanAssignments.{{ $globalIndex }}.peralatan_id"
                                                        :options="$peralatanOptions"
                                                        placeholder="Pilih peralatan..."
                                                        searchPlaceholder="Cari peralatan..."
                                                        emptyText="Tidak ada peralatan tersedia"
                                                        noResultText="Peralatan tidak ditemukan" />
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
