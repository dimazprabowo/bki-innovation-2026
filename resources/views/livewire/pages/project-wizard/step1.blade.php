{{-- Step 1: Project Info --}}
<div class="space-y-5">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Informasi Project</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Project *</label>
            <input
                type="text"
                wire:model="code"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="PRJ-001">
            @error('code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Project *</label>
            <input
                type="text"
                wire:model="name"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Nama project">
            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
        <textarea
            wire:model="description"
            rows="3"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Deskripsi project"></textarea>
        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritas *</label>
            <x-searchable-select
                wire:model="priority"
                :options="collect(\App\Enums\ProjectPriority::cases())->map(fn($p) => ['value' => $p->value, 'label' => $p->label()])->toArray()"
                placeholder="Pilih prioritas" />
            @error('priority') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkat Risiko <span class="text-xs text-gray-400">(otomatis dari modul)</span></label>
            <div class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                @php
                    $riskEnum = \App\Enums\RiskLevel::from($risk_level);
                @endphp
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-{{ $riskEnum->color() }}-500"></span>
                    {{ $riskEnum->label() }}
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-400">Risiko dihitung otomatis dari modul berisiko tertinggi yang dipilih.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
            <input
                type="date"
                wire:model="start_date"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
            @error('start_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
            <input
                type="date"
                wire:model="end_date"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
            @error('end_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
