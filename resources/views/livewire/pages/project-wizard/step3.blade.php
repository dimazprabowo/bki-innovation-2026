{{-- Step 3: Personel Assignment --}}
<div class="space-y-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Penugasan Personel</h3>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        Pilih personel untuk setiap posisi tim modul. Hanya personel dengan kompetensi yang sesuai yang ditampilkan.
    </p>

    @if(empty($this->selectedModuleIds()))
        <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
            <p class="text-sm text-yellow-800 dark:text-yellow-400">Pilih modul terlebih dahulu di langkah 2 untuk menugaskan personel.</p>
        </div>
    @else
        @php
            $byModule = $this->personelAssignmentsByModule;
        @endphp

        @if(empty($byModule))
            <div class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Modul yang dipilih belum memiliki personel</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($byModule as $moduleGroup)
                    @php
                        $module = $moduleGroup['module'];
                        $positions = $moduleGroup['positions'];
                        $totalSlots = collect($positions)->sum(fn ($p) => count($p['slots']));

                        $moduleAssignmentIndices = [];
                        foreach ($positions as $pos) {
                            foreach ($pos['slots'] as $slot) {
                                $idx = collect($this->personelAssignments)->search(fn ($a) => $a['module_personel_id'] == $pos['personel']->id && $a['slot'] == $slot['slot']);
                                if ($idx !== false) $moduleAssignmentIndices[] = $idx;
                            }
                        }
                        $indicesJson = json_encode($moduleAssignmentIndices);
                    @endphp

                    {{-- Module Section --}}
                    <div x-data="{ open: true }"
                         x-effect="let idx = {{ $indicesJson }}; let errs = $wire.errors; for (let k in errs) { if (idx.some(i => k.includes('personelAssignments.' + i + '.'))) { open = true; break; } }"
                         class="rounded-xl border border-gray-200 dark:border-gray-700">
                        {{-- Module Header --}}
                        <button type="button" @click="open = !open"
                            class="w-full px-5 py-4 bg-gradient-to-r from-indigo-50 to-indigo-100/50 dark:from-indigo-900/20 dark:to-indigo-800/10 border-b border-gray-200 dark:border-gray-700 transition-colors hover:from-indigo-100 hover:to-indigo-100/70 dark:hover:from-indigo-900/30 dark:hover:to-indigo-800/15">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 text-left">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $module->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $module->code }} &middot; {{ count($positions) }} posisi &middot; {{ $totalSlots }} slot</p>
                                </div>
                                <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-transform duration-200" :class="open ? '' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Positions --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($positions as $position)
                                @php
                                    $personel = $position['personel'];
                                    $competencyNames = $personel->competencies->pluck('name')->implode(', ');
                                @endphp
                                <div class="p-4">
                                    {{-- Position Header --}}
                                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $personel->position_name }}</span>
                                            <span class="text-xs text-gray-400">({{ count($position['slots']) }} slot)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($competencyNames)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                                    {{ $competencyNames }}
                                                </span>
                                            @endif
                                            <span class="text-xs px-2 py-0.5 rounded-full {{ $personel->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ $personel->nature === 'mandatory' ? 'Wajib' : 'Opsional' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Personel Slots --}}
                                    <div class="space-y-3">
                                        @foreach($position['slots'] as $slot)
                                            @php
                                                $globalIndex = collect($this->personelAssignments)->search(fn ($a) => $a['module_personel_id'] == $personel->id && $a['slot'] == $slot['slot']);
                                                $personelOptions = collect($this->personelsForSlot($personel->id))->map(fn ($p) => ['value' => $p['id'], 'label' => $p['label'], 'sublabel' => $p['sublabel'] ?? ''])->toArray();
                                            @endphp
                                            <div class="flex items-center gap-3 pl-2">
                                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs font-semibold">
                                                    {{ $slot['slot'] }}
                                                </span>
                                                <div class="flex-1">
                                                    <x-searchable-select
                                                        wire:model.live="personelAssignments.{{ $globalIndex }}.personel_id"
                                                        :options="$personelOptions"
                                                        placeholder="Pilih personel..."
                                                        searchPlaceholder="Cari personel..."
                                                        emptyText="Tidak ada personel tersedia"
                                                        noResultText="Personel tidak ditemukan" />
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
