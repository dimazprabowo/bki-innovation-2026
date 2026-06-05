@props(['competencyOptions' => [], 'competencies' => []])

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Kode Personel <span class="text-red-500">*</span>
            </label>
            <input wire:model="code" type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Contoh: P001">
            @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nama Personel <span class="text-red-500">*</span>
            </label>
            <input wire:model="name" type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Nama personel">
            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Status <span class="text-red-500">*</span>
            </label>
            <select wire:model="is_active"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
            @error('is_active') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Kompetensi</h4>
            <button type="button" wire:click="addCompetency"
                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kompetensi
            </button>
        </div>

        @forelse($competencies as $index => $competency)
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-3" wire:key="competency-{{ $index }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kompetensi #{{ $index + 1 }}</span>
                    @if(count($competencies) > 1)
                        <button type="button" wire:click="removeCompetency({{ $index }})"
                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kompetensi <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="competencies.{{ $index }}.competency_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Pilih Kompetensi</option>
                            @foreach($competencyOptions as $option)
                                <option value="{{ $option->id }}">{{ $option->name }} ({{ $option->level_label }})</option>
                            @endforeach
                        </select>
                        @error("competencies.{$index}.competency_id") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            File Sertifikat <span class="text-red-500">*</span>
                        </label>
                        @if(!empty($competency['certificate_file_path']))
                            <div class="mb-2">
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    File saat ini: {{ $competency['certificate_file_name'] }}
                                </span>
                                @if($competency['certificate_file_status'] === 'processing')
                                    <span class="ml-2 text-xs text-yellow-600 dark:text-yellow-400">(Memproses...)</span>
                                @elseif($competency['certificate_file_status'] === 'failed')
                                    <span class="ml-2 text-xs text-red-600 dark:text-red-400">(Gagal)</span>
                                @endif
                            </div>
                        @endif
                        <input wire:model="competencies.{{ $index }}.certificate_file" type="file"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        @error("competencies.{$index}.certificate_file") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Penerbit <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="competencies.{{ $index }}.issuer" type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                            placeholder="Nama penerbit sertifikat">
                        @error("competencies.{$index}.issuer") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Expired <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="competencies.{{ $index }}.expired_date" type="date"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                        @error("competencies.{$index}.expired_date") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Belum ada kompetensi ditambahkan</p>
                <button type="button" wire:click="addCompetency"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kompetensi Pertama
                </button>
            </div>
        @endforelse
    </div>
</div>
