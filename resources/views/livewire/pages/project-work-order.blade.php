<div class="space-y-6">
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
                    <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-base shadow-md shadow-indigo-500/20">
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
        {{-- Overall Progress Bar --}}
        @php
            $totalAll = collect($groups)->sum('total');
            $checkedAll = collect($groups)->sum('checked');
            $overallProgress = $totalAll > 0 ? round(($checkedAll / $totalAll) * 100) : 0;
        @endphp
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Progress Keseluruhan</span>
                <span class="text-xs font-bold {{ $overallProgress === 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }}">{{ $checkedAll }}/{{ $totalAll }} &middot; {{ $overallProgress }}%</span>
            </div>
            <div class="h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full {{ $overallProgress === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} transition-all duration-500 ease-out" style="width: {{ $overallProgress }}%"></div>
            </div>
        </div>
    </div>

    {{-- Checklist per Module --}}
    @if(empty($groups))
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-16 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Tidak ada work order item</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Modul yang dipilih belum memiliki work order item yang aktif.</p>
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
                <span class="text-xs text-gray-500 dark:text-gray-400">Selesai</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span class="text-xs text-gray-500 dark:text-gray-400">Terkunci (selesaikan item wajib sebelumnya)</span>
            </div>
        </div>

        <div class="flex items-center gap-2 px-1">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Checklist Work Order per Modul</h2>
        </div>

        <div class="space-y-3">
            @foreach($groups as $group)
                @php
                    $module = $group['module'];
                    $moduleNumber = $group['module_number'];
                    $items = $group['items'];
                    $total = $group['total'];
                    $checked = $group['checked'];
                    $progress = $group['progress'];
                    $mandatoryCount = collect($items)->filter(fn ($i) => $i['item']->nature === 'mandatory')->count();
                    $optionalCount = count($items) - $mandatoryCount;
                    $isComplete = $total > 0 && $checked === $total;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-shadow hover:shadow-md"
                     x-data="{ open: {{ $progress < 100 ? 'true' : 'false' }} }">
                    {{-- Module Header --}}
                    <div class="px-5 py-4 cursor-pointer select-none transition-colors {{ $isComplete ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : 'bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-900/70' }} border-b border-gray-200 dark:border-gray-700"
                         @click="open = !open">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex-shrink-0 w-9 h-9 rounded-lg {{ $isComplete ? 'bg-emerald-500' : 'bg-indigo-600' }} text-white flex items-center justify-center font-bold text-sm shadow-sm transition-colors">
                                    {{ $moduleNumber }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $module->name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500">{{ $module->code }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                        <span class="text-[11px] font-medium {{ $isComplete ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }}">{{ $checked }}/{{ $total }} selesai</span>
                                        @if($mandatoryCount > 0)
                                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                            <span class="inline-flex items-center gap-1 text-[11px] text-red-500 dark:text-red-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                {{ $mandatoryCount }} wajib
                                            </span>
                                        @endif
                                        @if($optionalCount > 0)
                                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                                            <span class="text-[11px] text-gray-400">{{ $optionalCount }} opsional</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <div class="hidden sm:flex items-center gap-2">
                                    <div class="w-20 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full {{ $isComplete ? 'bg-emerald-500' : 'bg-indigo-500' }} transition-all duration-500 ease-out" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                                <span class="text-xs font-bold tabular-nums {{ $isComplete ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400' }}">{{ $progress }}%</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Collapsible Content --}}
                    <div x-show="open" x-collapse x-cloak>
                        {{-- Work Order References --}}
                        @if($module->workOrderReferences->isNotEmpty())
                            <div class="px-5 py-3 bg-blue-50/70 dark:bg-blue-900/10 border-b border-blue-100 dark:border-blue-900/20">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                    <h4 class="text-[11px] font-semibold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Dokumen Referensi</h4>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($module->workOrderReferences as $reference)
                                        <div class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-md bg-white/60 dark:bg-gray-800/40 border border-blue-100 dark:border-blue-900/20">
                                            <svg class="flex-shrink-0 w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span class="text-blue-700 dark:text-blue-300 truncate text-xs font-medium">{{ $reference->document_name }}</span>
                                            @if($reference->document_id)
                                                <span class="text-[10px] text-blue-400 font-mono ml-auto">{{ $reference->document_id }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Checklist Items --}}
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($items as $structuredItem)
                                @php
                                    $item = $structuredItem['item'];
                                    $number = $structuredItem['number'];
                                    $hasSubitems = $structuredItem['has_subitems'];
                                    $isMandatory = $item->nature === 'mandatory';
                                    $parentIsChecked = $hasSubitems ? ($structuredItem['all_subitems_checked'] ?? false) : ($checklist[$structuredItem['key']]['is_checked'] ?? false);
                                    $isLocked = $structuredItem['locked'] ?? false;
                                @endphp
                                @if(!$hasSubitems)
                                    @php $key = $structuredItem['key']; @endphp
                                @endif
                                <div class="px-5 py-3.5 {{ $isLocked ? 'opacity-40' : '' }} {{ $isMandatory && !$parentIsChecked ? 'bg-red-50/20 dark:bg-red-900/5' : '' }} transition-opacity">
                                    {{-- Item without subitems --}}
                                    @if(!$hasSubitems)
                                        <label class="flex items-center gap-3 {{ $isLocked ? 'cursor-not-allowed' : 'cursor-pointer' }} group py-0.5" @if($isLocked) wire:click="notifyLocked" @endif>
                                            <input type="checkbox"
                                                wire:model.live="checklist.{{ $key }}.is_checked"
                                                class="flex-shrink-0 w-4.5 h-4.5 rounded border-gray-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 transition-colors"
                                                @if($isLocked) disabled @endif
                                                wire:loading.attr="disabled"
                                                wire:target="updatedChecklist.{{ $key }}">
                                            <div class="flex-shrink-0 w-7 h-7 rounded-md {{ $parentIsChecked ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : ($isMandatory ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400') }} flex items-center justify-center text-xs font-bold transition-colors">
                                                {{ $number }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm {{ $parentIsChecked ? 'text-emerald-700 dark:text-emerald-400 line-through' : 'text-gray-900 dark:text-white' }} font-medium">
                                                    {{ $item->name }}
                                                </span>
                                                @if($item->description)
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-relaxed">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                            @if($isLocked)
                                                <svg class="flex-shrink-0 w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Selesaikan item wajib sebelumnya"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            @endif
                                            <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded font-semibold uppercase tracking-wide {{ $isMandatory ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ $isMandatory ? 'Wajib' : 'Opsional' }}
                                            </span>
                                        </label>
                                    @else
                                        {{-- Item with subitems --}}
                                        <div class="flex items-center gap-3 py-0.5">
                                            <div class="w-4.5 flex-shrink-0"></div>
                                            <div class="flex-shrink-0 w-7 h-7 rounded-md {{ $parentIsChecked ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : ($isMandatory ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400') }} flex items-center justify-center text-xs font-bold transition-colors">
                                                {{ $number }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-semibold {{ $parentIsChecked ? 'text-emerald-700 dark:text-emerald-400 line-through' : 'text-gray-900 dark:text-white' }}">{{ $item->name }}</span>
                                                @if($item->description)
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-relaxed">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                            <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded font-semibold uppercase tracking-wide {{ $isMandatory ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ $isMandatory ? 'Wajib' : 'Opsional' }}
                                            </span>
                                        </div>

                                        {{-- Subitems --}}
                                        <div class="mt-2 ml-14 space-y-0.5">
                                            @foreach($structuredItem['subitems'] as $sub)
                                                @php
                                                    $subitem = $sub['subitem'];
                                                    $subNumber = $sub['number'];
                                                    $subKey = $sub['key'];
                                                    $subIsMandatory = $subitem->nature === 'mandatory';
                                                    $subIsChecked = $checklist[$subKey]['is_checked'] ?? false;
                                                @endphp
                                                <label class="flex items-center gap-2.5 {{ $sub['locked'] ? 'cursor-not-allowed opacity-40' : 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30' }} px-3 py-2 rounded-lg transition-all group" @if($sub['locked']) wire:click="notifyLocked" @endif>
                                                    <input type="checkbox"
                                                        wire:model.live="checklist.{{ $subKey }}.is_checked"
                                                        class="flex-shrink-0 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 transition-colors"
                                                        @if($sub['locked']) disabled @endif
                                                        wire:loading.attr="disabled"
                                                        wire:target="updatedChecklist.{{ $subKey }}">
                                                    <div class="flex-shrink-0 w-6 h-6 rounded {{ $subIsChecked ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 dark:text-emerald-400' : ($subIsMandatory ? 'bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400' : 'bg-gray-50 dark:bg-gray-700/50 text-gray-400') }} flex items-center justify-center text-[10px] font-semibold transition-colors">
                                                        {{ $subNumber }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-sm {{ $subIsChecked ? 'text-emerald-700 dark:text-emerald-400 line-through' : 'text-gray-700 dark:text-gray-300' }}">
                                                            {{ $subitem->name }}
                                                        </span>
                                                        @if($subitem->description)
                                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-relaxed">{{ $subitem->description }}</p>
                                                        @endif
                                                    </div>
                                                    @if($sub['locked'])
                                                        <svg class="flex-shrink-0 w-3.5 h-3.5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Selesaikan item wajib sebelumnya"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    @endif
                                                    <span class="flex-shrink-0 text-[10px] px-1.5 py-0.5 rounded font-semibold uppercase tracking-wide {{ $subIsMandatory ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                        {{ $subIsMandatory ? 'Wajib' : 'Opsional' }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
