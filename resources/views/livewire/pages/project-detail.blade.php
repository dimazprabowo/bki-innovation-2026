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

    {{-- Project Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ substr($project->code, 0, 2) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $project->code }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3" x-data="{ tooltip: null }" @mouseleave.outside="tooltip = null">
                        <span class="relative inline-flex"
                            @mouseenter="tooltip = 'status'"
                            @mouseleave="tooltip = null">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->status->badgeClass() }} cursor-help">
                                Status: {{ $project->status->label() }}
                            </span>
                            <template x-if="tooltip === 'status'">
                                <div class="absolute z-50 bottom-full left-0 mb-2 w-64 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg shadow-lg pointer-events-none whitespace-normal">
                                    <p class="font-semibold mb-0.5">{{ $project->status->label() }}</p>
                                    <p class="text-gray-300 dark:text-gray-400 font-normal leading-relaxed">{{ $project->status->description() }}</p>
                                    <div class="absolute top-full left-3 w-2 h-2 bg-gray-900 dark:bg-gray-700 transform rotate-45 -mt-1"></div>
                                </div>
                            </template>
                        </span>

                        <span class="relative inline-flex"
                            @mouseenter="tooltip = 'approval'"
                            @mouseleave="tooltip = null">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->approval_status->badgeClass() }} cursor-help">
                                Approval: {{ $project->approval_status->label() }}
                            </span>
                            <template x-if="tooltip === 'approval'">
                                <div class="absolute z-50 bottom-full left-0 mb-2 w-64 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg shadow-lg pointer-events-none whitespace-normal">
                                    <p class="font-semibold mb-0.5">{{ $project->approval_status->label() }}</p>
                                    <p class="text-gray-300 dark:text-gray-400 font-normal leading-relaxed">{{ $project->approval_status->description() }}</p>
                                    <div class="absolute top-full left-3 w-2 h-2 bg-gray-900 dark:bg-gray-700 transform rotate-45 -mt-1"></div>
                                </div>
                            </template>
                        </span>

                        <span class="relative inline-flex"
                            @mouseenter="tooltip = 'priority'"
                            @mouseleave="tooltip = null">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->priority->badgeClass() }} cursor-help">
                                Prioritas: {{ $project->priority->label() }}
                            </span>
                            <template x-if="tooltip === 'priority'">
                                <div class="absolute z-50 bottom-full left-0 mb-2 w-64 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg shadow-lg pointer-events-none whitespace-normal">
                                    <p class="font-semibold mb-0.5">Prioritas: {{ $project->priority->label() }}</p>
                                    <p class="text-gray-300 dark:text-gray-400 font-normal leading-relaxed">{{ $project->priority->description() }}</p>
                                    <div class="absolute top-full left-3 w-2 h-2 bg-gray-900 dark:bg-gray-700 transform rotate-45 -mt-1"></div>
                                </div>
                            </template>
                        </span>

                        <span class="relative inline-flex"
                            @mouseenter="tooltip = 'risk'"
                            @mouseleave="tooltip = null">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $project->risk_level->badgeClass() }} cursor-help">
                                Risiko: {{ $project->risk_level->label() }}
                            </span>
                            <template x-if="tooltip === 'risk'">
                                <div class="absolute z-50 bottom-full left-0 mb-2 w-64 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg shadow-lg pointer-events-none whitespace-normal">
                                    <p class="font-semibold mb-0.5">Risiko: {{ $project->risk_level->label() }}</p>
                                    <p class="text-gray-300 dark:text-gray-400 font-normal leading-relaxed">{{ $project->risk_level->description() }}</p>
                                    <div class="absolute top-full left-3 w-2 h-2 bg-gray-900 dark:bg-gray-700 transform rotate-45 -mt-1"></div>
                                </div>
                            </template>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                @can('projects_update')
                    @if($project->status === \App\Enums\ProjectStatus::Draft && in_array($project->approval_status->value, ['none', 'rejected']))
                        <x-loading-button wire:click="submit" target="submit" variant="primary" size="md"
                            loadingText="Mengajukan...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </x-slot:icon>
                            Ajukan
                        </x-loading-button>
                    @endif
                @endcan

                @can('projects_approve')
                    @if($project->approval_status->value === 'coe_review')
                        <x-loading-button wire:click="approve" target="approve" variant="success" size="md"
                            loadingText="Menyetujui...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </x-slot:icon>
                            Setujui
                        </x-loading-button>
                        <x-loading-button wire:click="confirmReject" target="confirmReject" variant="warning" size="md"
                            loadingText="Loading...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </x-slot:icon>
                            Tolak
                        </x-loading-button>
                    @endif
                @endcan

                @can('projects_approve')
                    @if($project->status->value === 'active')
                        <x-loading-button wire:click="confirmClose" target="confirmClose" variant="danger" size="md"
                            loadingText="Loading...">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </x-slot:icon>
                            Tutup
                        </x-loading-button>
                    @endif
                @endcan

                @can('projects_update')
                    @if($project->status->isEditable())
                        <a href="{{ route('projects.edit', $project) }}" wire:navigate x-data="{ loading: false }" @click="loading = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!loading">Edit</span>
                            <span x-show="loading" x-cloak>Memuat...</span>
                        </a>
                    @endif
                @endcan

                @can('projects_work_order')
                    @if($project->status->value === 'active')
                        <a href="{{ route('projects.work-order', $project) }}" wire:navigate x-data="{ loading: false }" @click="loading = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!loading">Work Order</span>
                            <span x-show="loading" x-cloak>Memuat...</span>
                        </a>
                    @endif
                @endcan

                @can('projects_deliverables')
                    @if($project->status->value === 'active')
                        <a href="{{ route('projects.deliverables', $project) }}" wire:navigate x-data="{ loading: false }" @click="loading = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!loading">Deliverables</span>
                            <span x-show="loading" x-cloak>Memuat...</span>
                        </a>
                    @endif
                @endcan
            </div>
        </div>

        @if($project->description)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Deskripsi</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $project->description }}</p>
            </div>
        @endif
    </div>

    {{-- Rejection Reason --}}
    @if($project->approval_status->value === 'rejected' && $project->rejection_reason)
        <div class="p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300 mb-1">Alasan Penolakan</h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-400">{{ $project->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Project Info Grid --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Informasi Project</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Tanggal Mulai</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->start_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Tanggal Selesai</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->end_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Dibuat Oleh</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->creator?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Disetujui Oleh</dt>
                <dd class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $project->approver?->name ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Additional Costs --}}
    @if($project->additionalCosts->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Biaya Tambahan ({{ $project->additionalCosts->count() }})</h3>
            <div class="space-y-2">
                @foreach($project->additionalCosts as $cost)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cost->name }}</p>
                            @if($cost->notes)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $cost->notes }}</p>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($cost->amount, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Cost Summary --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Ringkasan Biaya</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Biaya Modul</span>
                <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($project->base_cost, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Biaya Tambahan</span>
                <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($project->additional_cost_total, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex items-center justify-between">
                <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($project->total_cost, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @php
        $moduleGroups = $project->modules->mapWithKeys(function ($module) use ($project) {
            $personels = $project->projectPersonels->where('module_id', $module->id);
            $peralatans = $project->projectPeralatans->where('module_id', $module->id);
            return [$module->id => ['module' => $module, 'personels' => $personels, 'peralatans' => $peralatans]];
        });
    @endphp

    {{-- Personel & Peralatan grouped by Module --}}
    @if($project->projectPersonels->isNotEmpty() || $project->projectPeralatans->isNotEmpty())
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Penugasan per Modul</h3>
            @foreach($moduleGroups as $group)
                @php
                    $module = $group['module'];
                    $personels = $group['personels'];
                    $peralatans = $group['peralatans'];
                @endphp
                @if($personels->isNotEmpty() || $peralatans->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        {{-- Module Header --}}
                        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $module->name }}</span>
                            <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full {{ $module->risk_level->badgeClass() }}">
                                {{ $module->risk_level->label() }}
                            </span>
                        </div>

                        <div class="p-5 space-y-4">
                            {{-- Personels in this module --}}
                            @if($personels->isNotEmpty())
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Personel ({{ $personels->count() }})</h4>
                                    <div class="space-y-2">
                                        @foreach($personels as $pp)
                                            @php
                                                $requiredCompetencies = $pp->personelSlot?->competencies ?? collect();
                                                $personelCompetencies = $pp->personel?->competencies ?? collect();
                                                $personelCompetencyIds = $personelCompetencies->pluck('id');
                                            @endphp
                                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <div class="flex-shrink-0 h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                                                        {{ substr($pp->personel?->name ?? '?', 0, 1) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $pp->personel?->name ?? '-' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $pp->personelSlot?->position_name ?? '-' }}
                                                            <span class="ml-1 px-1.5 py-0.5 rounded {{ $pp->personelSlot?->nature === 'mandatory' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                                                {{ $pp->personelSlot?->nature === 'mandatory' ? 'Wajib' : 'Opsional' }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                                @if($requiredCompetencies->isNotEmpty())
                                                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                        <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Kompetensi</p>
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($requiredCompetencies as $reqComp)
                                                                @php
                                                                    $fulfilled = $personelCompetencyIds->contains($reqComp->id);
                                                                @endphp
                                                                <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full {{ $fulfilled ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                                                    @if($fulfilled)
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                                    @else
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    @endif
                                                                    {{ $reqComp->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Peralatan in this module --}}
                            @if($peralatans->isNotEmpty())
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Peralatan ({{ $peralatans->count() }})</h4>
                                    <div class="space-y-2">
                                        @foreach($peralatans as $pp)
                                            @php
                                                $peralatan = $pp->peralatan;
                                                $requiresCalibration = $pp->tool?->requires_calibration ?? false;
                                                $calibrationStatus = $peralatan?->calibration_status;
                                                $isExpired = $peralatan?->calibration_status_expired ?? false;
                                                $expiredDate = $peralatan?->calibration_expired_date;
                                            @endphp
                                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-shrink-0 h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $peralatan?->name ?? '-' }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $peralatan?->code ?? '-' }} · Qty: {{ $pp->tool?->quantity ?? 1 }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 flex flex-wrap items-center gap-2">
                                                    @if($requiresCalibration)
                                                        <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Perlu Kalibrasi
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                            Tidak Perlu Kalibrasi
                                                        </span>
                                                    @endif
                                                    @if($calibrationStatus)
                                                        @php
                                                            $statusColor = match($calibrationStatus->value) {
                                                                'calibrated' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                                'expired' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                                'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                                'not_required' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                                            };
                                                        @endphp
                                                        <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full {{ $statusColor }}">
                                                            {{ $calibrationStatus->label() }}
                                                        </span>
                                                    @endif
                                                    @if($expiredDate)
                                                        <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full {{ $isExpired ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                            Exp: {{ $expiredDate->format('d M Y') }}
                                                            @if($isExpired)
                                                                <span class="font-semibold">(Expired)</span>
                                                            @endif
                                                        </span>
                                                    @endif
                                                    @if($peralatan?->condition)
                                                        <span class="inline-flex items-center text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                            Kondisi: {{ $peralatan->condition->label() }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($personels->isEmpty() && $peralatans->isEmpty())
                                <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada penugasan personel atau peralatan pada modul ini.</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Modules Detail (at bottom) --}}
    @if($project->modules->isNotEmpty())
        <div class="space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Detail Modul ({{ $project->modules->count() }})</h3>
            @foreach($project->modules as $module)
                @php
                    $subtotal = (float)($module->pivot->quantity ?? 0) * (float)($module->pivot->unit_price ?? 0);
                @endphp
                <div x-data="{ expanded: false }"
                     class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="expanded = !expanded"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 transition-colors hover:bg-gray-100 dark:hover:bg-gray-900 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg x-show="expanded" class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg x-show="!expanded" x-cloak class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $module->name }}</span>
                            <span class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full {{ $module->risk_level->badgeClass() }}">
                                {{ $module->risk_level->label() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Qty: {{ $module->pivot->quantity }} · Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </button>
                    <div x-show="expanded" x-collapse class="p-4 bg-gray-50 dark:bg-gray-900/50">
                        <div class="grid grid-cols-3 gap-3 mb-4 text-sm">
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Qty</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">{{ $module->pivot->quantity }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Harga Satuan</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($module->pivot->unit_price, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Subtotal</dt>
                                <dd class="font-semibold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($module->pivot->subtotal, 0, ',', '.') }}</dd>
                            </div>
                        </div>
                        @if($module->pivot->notes)
                            <div class="mb-4 text-sm">
                                <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Catatan</dt>
                                <dd class="text-gray-700 dark:text-gray-300">{{ $module->pivot->notes }}</dd>
                            </div>
                        @endif
                        <x-module-info :module="$module" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Reject Modal --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" @click="$wire.set('showRejectModal', false)"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md z-10 p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Tolak Project</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Jelaskan alasan penolakan project ini.</p>
                    <div class="mb-6 text-left">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="rejectionReason"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Minimal 10 karakter"></textarea>
                        @error('rejectionReason')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <button @click="$wire.set('showRejectModal', false)"
                            class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition">
                            Batal
                        </button>
                        <button wire:click="reject"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            wire:target="reject">
                            <svg wire:loading wire:target="reject" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Tolak Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Close Modal --}}
    @if($showCloseModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" @click="$wire.set('showCloseModal', false)"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md z-10 p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Tutup Project</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Project yang ditutup tidak dapat diedit atau diajukan kembali.</p>
                    <div class="mb-6 text-left">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alasan Penutupan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="closeReason"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Minimal 10 karakter"></textarea>
                        @error('closeReason')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <button @click="$wire.set('showCloseModal', false)"
                            class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition">
                            Batal
                        </button>
                        <button wire:click="closeProject"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            wire:target="closeProject">
                            <svg wire:loading wire:target="closeProject" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Tutup Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
