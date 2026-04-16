<div>
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center gap-3">
        <!-- Search -->
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari project..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
        </div>

        <!-- Filter Status -->
        <div class="w-full lg:w-48">
            <x-searchable-select
                wire:model.live="statusFilter"
                :options="collect([['value' => '', 'label' => 'Semua Status']])->concat(collect($statuses)->map(fn($label, $key) => ['value' => $key, 'label' => $label])->values())->toArray()"
                placeholder="Semua Status"
                searchPlaceholder="Cari status..."
            />
        </div>

        <!-- Filter Risk -->
        <div class="w-full lg:w-48">
            <x-searchable-select
                wire:model.live="riskFilter"
                :options="collect([['value' => '', 'label' => 'Semua Risiko']])->concat(collect($riskLevels)->map(fn($r) => ['value' => $r->value, 'label' => $r->label()]))->toArray()"
                placeholder="Semua Risiko"
                searchPlaceholder="Cari risiko..."
            />
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 w-full lg:w-auto">
            @can('projects_export_excel')
                <x-loading-button wire:click="exportExcel" target="exportExcel" variant="success" size="md" loadingText="Exporting..." title="Export Excel">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </x-slot:icon>
                    Excel
                </x-loading-button>
            @endcan
            @can('projects_export_pdf')
                <x-loading-button wire:click="exportPdf" target="exportPdf" variant="danger" size="md" loadingText="Exporting..." title="Export PDF">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </x-slot:icon>
                    PDF
                </x-loading-button>
            @endcan
            @can('projects_create')
                <x-loading-button wire:click="create" target="create" variant="primary" size="md" loadingText="Memuat..." class="flex-1 lg:flex-none">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </x-slot:icon>
                    Buat Project
                </x-loading-button>
            @endcan
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Risiko</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">CoE Control</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modules</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($projects as $project)
                        <tr wire:key="project-{{ $project->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-xs">
                                            {{ substr($project->code, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $project->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $project->duration ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $project->risk_level->badgeClass() }}">
                                    {{ $project->risk_level->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $project->coe_control_level->badgeClass() }}">
                                    {{ $project->coe_control_level->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $project->status->badgeClass() }}">
                                    {{ $project->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    {{ $project->modules_count }} modul
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $project->creator?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:key="view-btn-{{ $project->id }}"
                                        wire:click="view({{ $project->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="view({{ $project->id }})"
                                        class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 disabled:opacity-50"
                                        title="Lihat">
                                        <svg wire:loading.class="hidden" wire:target="view({{ $project->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg wire:loading wire:target="view({{ $project->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </button>

                                    @can('projects_update')
                                    @if($project->status->isEditable())
                                        <button wire:key="edit-btn-{{ $project->id }}"
                                            wire:click="edit({{ $project->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="edit({{ $project->id }})"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 disabled:opacity-50"
                                            title="Edit">
                                            <svg wire:loading.class="hidden" wire:target="edit({{ $project->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <svg wire:loading wire:target="edit({{ $project->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    @endcan

                                    @can('projects_delete')
                                        <button wire:key="delete-btn-{{ $project->id }}"
                                            wire:click="confirmDelete({{ $project->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmDelete({{ $project->id }})"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50"
                                            title="Hapus">
                                            <svg wire:loading.class="hidden" wire:target="confirmDelete({{ $project->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <svg wire:loading wire:target="confirmDelete({{ $project->id }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tidak ada project ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $projects->links() }}
        </div>
    </div>

    @if($showModal && !$viewMode)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.closeModal()"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form wire:submit="save">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ $editMode ? 'Edit Project' : 'Buat Project Baru' }}
                            </h3>

                            <x-project-form 
                                :riskLevels="$riskLevels" 
                                :availableModules="$availableModules" 
                                :selectedModules="$selectedModules"
                                :availableUsers="$availableUsers"
                                :selectedResources="$selectedResources"
                                :selectedEquipments="$selectedEquipments"
                                :selectedAccommodations="$selectedAccommodations"
                            />
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <x-loading-button type="submit" target="save" variant="primary" size="lg"
                                loadingText="Menyimpan..." class="w-full sm:w-auto">
                                {{ $editMode ? 'Update' : 'Simpan' }}
                            </x-loading-button>
                            <x-loading-button type="button" @click="$wire.closeModal()" variant="secondary" size="lg"
                                class="mt-3 sm:mt-0 w-full sm:w-auto">
                                Batal
                            </x-loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showModal && $viewMode && $currentProject)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.closeModal()"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Project</h3>
                            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $currentProject->coe_control_level->badgeClass() }}">
                                {{ $currentProject->coe_control_level->label() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Kode Project</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $currentProject->code }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Nama Project</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $currentProject->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tingkat Risiko</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $currentProject->risk_level->badgeClass() }}">
                                    {{ $currentProject->risk_level->label() }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $currentProject->status->badgeClass() }}">
                                    {{ $currentProject->status->label() }}
                                </span>
                            </div>
                            @if($currentProject->scope)
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Scope</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $currentProject->scope }}</p>
                            </div>
                            @endif
                            @if($currentProject->creator)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $currentProject->creator->name }}</p>
                            </div>
                            @endif
                            @if($currentProject->approver)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Diapprove Oleh</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $currentProject->approver->name }}</p>
                            </div>
                            @endif
                        </div>

                        @if(in_array($currentProject->status->value, ['rejected', 'stopped']) && $currentProject->rejection_reason)
                        <div class="border-t border-gray-200 dark:border-gray-700 py-4">
                            <div class="p-4 rounded-lg {{ $currentProject->status->value === 'rejected' ? 'bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800' : 'bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800' }}">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        @if($currentProject->status->value === 'rejected')
                                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold {{ $currentProject->status->value === 'rejected' ? 'text-yellow-800 dark:text-yellow-300' : 'text-red-800 dark:text-red-300' }} mb-1">
                                            {{ $currentProject->status->value === 'rejected' ? 'Alasan Penolakan' : 'Alasan Stop' }}
                                        </h4>
                                        <p class="text-sm {{ $currentProject->status->value === 'rejected' ? 'text-yellow-700 dark:text-yellow-400' : 'text-red-700 dark:text-red-400' }}">
                                            {{ $currentProject->rejection_reason }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($currentProject->resources->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Resource / Tim Project</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($currentProject->resources as $resource)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                                        {{ substr($resource->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $resource->name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                            @if($resource->position)
                                                <span class="truncate">{{ $resource->position }}</span>
                                                <span>•</span>
                                            @endif
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ $resource->active_projects_count ?? 0 }} project
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($currentProject->modules->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Modul yang Digunakan</h4>
                            <div class="space-y-2">
                                @foreach($currentProject->modules as $module)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $module->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $module->code }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $module->risk_level->badgeClass() }}">
                                            {{ $module->risk_level->label() }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-xs">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Qty</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $module->pivot->quantity }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Harga Satuan</p>
                                            <p class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($module->pivot->unit_price, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-gray-500 dark:text-gray-400">Subtotal</p>
                                            <p class="font-semibold text-blue-600 dark:text-blue-400">Rp {{ number_format($module->pivot->subtotal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                <div class="flex justify-end pt-2 border-t border-gray-200 dark:border-gray-600">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Estimasi</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($currentProject->total_estimate, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        @can('projects_approve')
                            @if($currentProject->status->value === 'coe_review')
                                <x-loading-button wire:click="approve({{ $currentProject->id }})" target="approve" variant="success" size="lg"
                                    loadingText="Approving..." class="w-full sm:w-auto">
                                    <x-slot:icon>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </x-slot:icon>
                                    Approve Project
                                </x-loading-button>
                                
                                <x-loading-button wire:click="confirmReject({{ $currentProject->id }})" target="confirmReject" variant="warning" size="lg"
                                    loadingText="Loading..." class="w-full sm:w-auto">
                                    <x-slot:icon>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </x-slot:icon>
                                    Reject Project
                                </x-loading-button>
                                
                                <x-loading-button wire:click="confirmStop({{ $currentProject->id }})" target="confirmStop" variant="danger" size="lg"
                                    loadingText="Loading..." class="w-full sm:w-auto">
                                    <x-slot:icon>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </x-slot:icon>
                                    Stop Project
                                </x-loading-button>
                            @endif
                        @endcan
                        
                        @can('projects_update')
                            @if($currentProject->status->isSubmittable())
                                <x-loading-button wire:click="submit({{ $currentProject->id }})" target="submit" variant="primary" size="lg"
                                    loadingText="Submitting..." class="w-full sm:w-auto">
                                    <x-slot:icon>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                    </x-slot:icon>
                                    Submit Project
                                </x-loading-button>
                            @endif
                        @endcan
                        
                        <x-loading-button type="button" @click="$wire.closeModal()" variant="secondary" size="lg"
                            class="w-full sm:w-auto">
                            Tutup
                        </x-loading-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <x-delete-modal 
        :show="$showDeleteModal"
        wire:model="showDeleteModal"
        title="Hapus Project"
        message="Apakah Anda yakin ingin menghapus project"
        :itemName="$deletingProjectName"
        confirmMethod="delete"
    />

    <!-- Remove Module Confirmation Modal -->
    <x-delete-modal 
        :show="$showRemoveModuleModal"
        wire:model="showRemoveModuleModal"
        title="Hapus Modul"
        message="Apakah Anda yakin ingin menghapus modul"
        :itemName="$removingModuleName"
        confirmMethod="removeModule"
    />

    <!-- Reject Project Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.set('showRejectModal', false)"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="mt-3 w-full sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Reject Project
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Alasan Penolakan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        wire:model="rejectionReason" 
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Jelaskan alasan penolakan project ini (minimal 10 karakter)"></textarea>
                                    @error('rejectionReason') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <x-loading-button wire:click="reject" target="reject" variant="warning" size="lg"
                            loadingText="Rejecting..." class="w-full sm:w-auto">
                            Reject Project
                        </x-loading-button>
                        <x-loading-button type="button" @click="$wire.set('showRejectModal', false)" variant="secondary" size="lg"
                            class="mt-3 sm:mt-0 w-full sm:w-auto">
                            Batal
                        </x-loading-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stop Project Modal -->
    @if($showStopModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.set('showStopModal', false)"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <div class="mt-3 w-full sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Stop Project
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    ⚠️ <strong>Perhatian:</strong> Project yang di-stop akan menjadi <strong>data mati</strong> dan tidak dapat diedit atau diajukan kembali.
                                </p>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Alasan Stop <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        wire:model="stopReason" 
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Jelaskan alasan stop project ini (minimal 10 karakter)"></textarea>
                                    @error('stopReason') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <x-loading-button wire:click="stop" target="stop" variant="danger" size="lg"
                            loadingText="Stopping..." class="w-full sm:w-auto">
                            Stop Project
                        </x-loading-button>
                        <x-loading-button type="button" @click="$wire.set('showStopModal', false)" variant="secondary" size="lg"
                            class="mt-3 sm:mt-0 w-full sm:w-auto">
                            Batal
                        </x-loading-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
