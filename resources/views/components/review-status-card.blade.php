@props([
    'reviewStatus',
    'reviewer' => null,
    'reviewedAt' => null,
    'rejectionReason' => null,
    'approvalNote' => null,
    'title' => 'Review',
])

@php
    $statusValue = $reviewStatus->value;

    $reviewIcon = match($statusValue) {
        'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'approved' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'rejected' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        default => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    };

    $reviewBorder = match($statusValue) {
        'pending' => 'border-purple-300 dark:border-purple-700',
        'approved' => 'border-green-300 dark:border-green-700',
        'rejected' => 'border-red-300 dark:border-red-700',
        default => 'border-gray-300 dark:border-gray-600',
    };

    $reviewIconBg = match($statusValue) {
        'pending' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
        default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    };

    $isRejected = $statusValue === 'rejected';
    $isApproved = $statusValue === 'approved';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 {{ $reviewBorder }} p-5">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $reviewIconBg }} flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $reviewIcon }}"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">{{ $title }}</h3>
                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium {{ $reviewStatus->badgeClass() }}">
                    {{ $reviewStatus->label() }}
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $reviewStatus->description() }}</p>
        </div>
    </div>
    @if($reviewer)
        <div class="flex items-center gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $reviewer->name }}
            </span>
            @if($reviewedAt)
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $reviewedAt->format('d M Y, H:i') }}
                </span>
            @endif
        </div>
    @endif
    @if($isRejected && $rejectionReason)
        <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/10 rounded-lg">
            <p class="text-sm font-medium text-red-700 dark:text-red-400">Alasan Penolakan:</p>
            <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $rejectionReason }}</p>
        </div>
    @endif
    @if($isApproved && $approvalNote)
        <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/10 rounded-lg">
            <p class="text-sm font-medium text-green-700 dark:text-green-400">Catatan Persetujuan:</p>
            <p class="text-sm text-green-600 dark:text-green-400 mt-1">{{ $approvalNote }}</p>
        </div>
    @endif
</div>
