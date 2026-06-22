<div x-data="{ showStep: $wire.currentStep }">
    {{-- Back Button --}}
    <div class="mb-6">
        <button type="button" wire:click="goBack" wire:target="goBack"
            class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
            <svg wire:loading.class.remove="inline" wire:loading.class.add="hidden" wire:target="goBack" class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <svg wire:loading wire:target="goBack" class="animate-spin w-5 h-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Kembali ke Daftar Project
        </button>
    </div>

    {{-- Step Indicator --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @foreach(range(1, $totalSteps) as $step)
                @php($canAccess = $this->canGoToStep($step))
                <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <button
                            type="button"
                            wire:click="goToStep({{ $step }})"
                            @if(!$canAccess) disabled @endif
                            class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-colors
                                {{ $currentStep >= $step
                                    ? 'bg-indigo-600 text-white'
                                    : ($canAccess
                                        ? 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-indigo-600 hover:text-white'
                                        : 'bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500') }}
                                {{ $canAccess ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                            <span wire:loading.class.remove="inline" wire:loading.class.add="hidden" wire:target="goToStep({{ $step }})">{{ $step }}</span>
                            <svg wire:loading wire:target="goToStep({{ $step }})" class="animate-spin h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        <span class="mt-2 text-xs text-center hidden sm:block
                            {{ $currentStep >= $step ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-500' }}">
                            @switch($step)
                                @case(1) Info Project @break
                                @case(2) Modul @break
                                @case(3) Personel @break
                                @case(4) Peralatan @break
                                @case(5) Biaya & Review @break
                            @endswitch
                        </span>
                    </div>
                    @if($step < $totalSteps)
                        <div class="h-0.5 flex-1 mx-2 mt-[-20px] transition-colors
                            {{ $currentStep > $step ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Step Content --}}
    <div wire:key="step-{{ $currentStep }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        @if($currentStep === 1)
            @include('livewire.pages.project-wizard.step1')
        @elseif($currentStep === 2)
            @include('livewire.pages.project-wizard.step2')
        @elseif($currentStep === 3)
            @include('livewire.pages.project-wizard.step3')
        @elseif($currentStep === 4)
            @include('livewire.pages.project-wizard.step4')
        @elseif($currentStep === 5)
            @include('livewire.pages.project-wizard.step5')
        @endif
    </div>

    {{-- Navigation --}}
    <div class="mt-6 flex items-center justify-between">
        <x-loading-button
            type="button"
            wire:click="previousStep"
            target="previousStep"
            variant="secondary"
            size="md"
            loadingText="Memuat..."
            class="{{ $currentStep === 1 ? 'opacity-50 pointer-events-none' : '' }}">
            Sebelumnya
        </x-loading-button>

        <div class="flex items-center gap-3">
            <x-loading-button
                type="button"
                wire:click="saveDraft"
                target="saveDraft"
                variant="secondary"
                size="md"
                loadingText="Menyimpan...">
                Simpan Draft
            </x-loading-button>

            @if($currentStep < $totalSteps)
                <x-loading-button
                    type="button"
                    wire:click="nextStep"
                    target="nextStep"
                    variant="primary"
                    size="md"
                    loadingText="Memuat...">
                    Selanjutnya
                </x-loading-button>
            @else
                <x-loading-button
                    type="button"
                    wire:click="submitProject"
                    target="submitProject"
                    variant="success"
                    size="md"
                    loadingText="Mengajukan...">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </x-slot:icon>
                    Ajukan Project
                </x-loading-button>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modals --}}
    <x-delete-modal
        :show="$showDeleteModuleModal"
        wire:model="showDeleteModuleModal"
        title="Hapus Modul"
        message="Apakah Anda yakin ingin menghapus modul ini?"
        confirmMethod="confirmDeleteModule"
    />

    <x-delete-modal
        :show="$showDeleteCostModal"
        wire:model="showDeleteCostModal"
        title="Hapus Biaya Tambahan"
        message="Apakah Anda yakin ingin menghapus biaya tambahan ini?"
        confirmMethod="confirmDeleteAdditionalCost"
    />

</div>
