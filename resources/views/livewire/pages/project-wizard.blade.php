<div x-data="{ showStep: $wire.currentStep }">
    {{-- Back Button --}}
    <div class="mb-6" x-data="{ loading: false }">
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

    {{-- Step Indicator --}}
    <div class="mb-8 overflow-x-auto">
        <div class="flex items-center justify-between min-w-[280px] sm:min-w-0">
            @foreach(range(1, $totalSteps) as $step)
                @php($canAccess = $this->canGoToStep($step))
                <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <button
                            type="button"
                            wire:click="goToStep({{ $step }})"
                            @if(!$canAccess) disabled @endif
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm transition-colors flex-shrink-0
                                {{ $currentStep >= $step
                                    ? 'bg-indigo-600 text-white'
                                    : ($canAccess
                                        ? 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-indigo-600 hover:text-white'
                                        : 'bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500') }}
                                {{ $canAccess ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                            <span wire:loading.class.remove="inline" wire:loading.class.add="hidden" wire:target="goToStep({{ $step }})">{{ $step }}</span>
                            <svg wire:loading wire:target="goToStep({{ $step }})" class="animate-spin h-4 w-4 sm:h-5 sm:w-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        <span class="mt-1.5 sm:mt-2 text-[10px] sm:text-xs text-center hidden sm:block
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
                        <div class="h-0.5 flex-1 mx-1.5 sm:mx-2 transition-colors
                            {{ $currentStep > $step ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Step Content --}}
    <div wire:key="step-{{ $currentStep }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
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
    <div class="mt-6 flex flex-col-reverse sm:flex-row items-center sm:justify-between gap-3">
        <x-loading-button
            type="button"
            wire:click="previousStep"
            target="previousStep"
            variant="secondary"
            size="md"
            loadingText="Memuat..."
            class="w-full sm:w-auto {{ $currentStep === 1 ? 'opacity-50 pointer-events-none' : '' }}">
            Sebelumnya
        </x-loading-button>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <x-loading-button
                type="button"
                wire:click="saveDraft"
                target="saveDraft"
                variant="secondary"
                size="md"
                loadingText="Menyimpan..."
                class="flex-1 sm:flex-none">
                Simpan Draft
            </x-loading-button>

            @if($currentStep < $totalSteps)
                <x-loading-button
                    wire:key="wizard-next-btn"
                    type="button"
                    wire:click="nextStep"
                    target="nextStep"
                    variant="primary"
                    size="md"
                    loadingText="Memuat..."
                    class="flex-1 sm:flex-none">
                    Selanjutnya
                </x-loading-button>
            @else
                <x-loading-button
                    wire:key="wizard-submit-btn"
                    type="button"
                    wire:click="confirmSubmit"
                    target="confirmSubmit"
                    variant="success"
                    size="md"
                    loadingText="Memuat..."
                    class="flex-1 sm:flex-none">
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

    {{-- Submit Confirmation Modal --}}
    @if($showSubmitModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto" x-data x-transition.opacity>
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" @click="$wire.set('showSubmitModal', false)"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md z-10 overflow-hidden">
                    <div class="px-6 pt-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Ajukan Project?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Pastikan semua data project sudah benar. Setelah diajukan, project akan masuk ke proses review CoE dan tidak dapat diedit lagi hingga disetujui atau ditolak.
                        </p>
                    </div>
                    <div class="flex items-center justify-center gap-3 px-6 pb-6">
                        <x-cancel-button wire:click="$set('showSubmitModal', false)" target="$set('showSubmitModal', false)" variant="secondary" />
                        <button type="button"
                            wire:click="submitProject"
                            wire:loading.attr="disabled"
                            wire:target="submitProject"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="submitProject" class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <svg wire:loading wire:target="submitProject" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="submitProject" class="inline-block">Ya, Ajukan</span>
                            <span wire:loading wire:target="submitProject">Mengajukan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
