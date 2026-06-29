<?php

namespace App\Livewire\MasterData;

use App\Exports\PeralatanExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Peralatan;
use App\Services\PeralatanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class PeralatanManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $calibrationStatusFilter = '';
    public $conditionFilter = '';
    public $ownershipStatusFilter = '';
    public $showDeleteModal = false;
    public $deletingPeralatanId;
    public $deletingPeralatanName;

    public $showRejectReviewModal = false;
    public $rejectingPeralatanId;
    public $rejectingPeralatanName;
    public $rejectionReason = '';

    public $showApproveReviewModal = false;
    public $approvingPeralatanId;
    public $approvingPeralatanName;
    public $approvalNote = '';

    public $reviewStatusFilter = '';

    public function mount()
    {
        $this->authorize('viewAny', Peralatan::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedCalibrationStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedConditionFilter()
    {
        $this->resetPage();
    }

    public function updatedOwnershipStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedReviewStatusFilter()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create', Peralatan::class);
        return $this->redirect(route('master-data.peralatan.create'), navigate: true);
    }

    public function edit($id)
    {
        $peralatan = Peralatan::findOrFail($id);
        $this->authorize('update', $peralatan);

        return $this->redirect(route('master-data.peralatan.edit', $peralatan), navigate: true);
    }

    public function confirmDelete($id)
    {
        $peralatan = Peralatan::findOrFail($id);
        $this->deletingPeralatanId = $peralatan->id;
        $this->deletingPeralatanName = $peralatan->name;
        $this->showDeleteModal = true;
    }

    public function delete(PeralatanService $service)
    {
        try {
            $peralatan = Peralatan::findOrFail($this->deletingPeralatanId);
            $this->authorize('delete', $peralatan);

            $service->delete($peralatan);
            $this->notifySuccess('Peralatan berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat menghapus peralatan ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function toggleStatus($id, PeralatanService $service)
    {
        try {
            $peralatan = Peralatan::findOrFail($id);
            $this->authorize('toggleStatus', $peralatan);

            $service->toggleStatus($peralatan);
            $status = $peralatan->fresh()->is_active ? 'aktif' : 'non-aktif';
            $this->notifySuccess("Status peralatan berhasil diubah menjadi {$status}!");
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk mengubah status peralatan.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmApproveReview($id)
    {
        $peralatan = Peralatan::findOrFail($id);
        $this->approvingPeralatanId = $peralatan->id;
        $this->approvingPeralatanName = $peralatan->name;
        $this->approvalNote = '';
        $this->showApproveReviewModal = true;
    }

    public function closeApproveReviewModal()
    {
        $this->showApproveReviewModal = false;
        $this->approvingPeralatanId = null;
        $this->approvingPeralatanName = null;
        $this->approvalNote = '';
    }

    public function approveReview(PeralatanService $service)
    {
        try {
            $this->validate([
                'approvalNote' => 'nullable|string|max:500',
            ], [
                'approvalNote.max' => 'Catatan persetujuan maksimal 500 karakter.',
            ]);

            $peralatan = Peralatan::findOrFail($this->approvingPeralatanId);
            $this->authorize('reviewPeralatan', $peralatan);

            $service->approveReview($peralatan, auth()->id(), $this->approvalNote ?: null);
            $this->notifySuccess('Peralatan berhasil disetujui!');
            $this->closeApproveReviewModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan review peralatan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors()->toArray());
            $this->notifyValidationError($e);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmRejectReview($id)
    {
        $peralatan = Peralatan::findOrFail($id);
        $this->rejectingPeralatanId = $peralatan->id;
        $this->rejectingPeralatanName = $peralatan->name;
        $this->rejectionReason = '';
        $this->showRejectReviewModal = true;
    }

    public function closeRejectReviewModal()
    {
        $this->showRejectReviewModal = false;
        $this->rejectingPeralatanId = null;
        $this->rejectingPeralatanName = null;
        $this->rejectionReason = '';
    }

    public function rejectReview(PeralatanService $service)
    {
        try {
            $this->validate([
                'rejectionReason' => 'required|string|min:10|max:500',
            ], [
                'rejectionReason.required' => 'Alasan penolakan harus diisi.',
                'rejectionReason.min' => 'Alasan penolakan minimal 10 karakter.',
                'rejectionReason.max' => 'Alasan penolakan maksimal 500 karakter.',
            ]);

            $peralatan = Peralatan::findOrFail($this->rejectingPeralatanId);
            $this->authorize('reviewPeralatan', $peralatan);

            $service->rejectReview($peralatan, auth()->id(), $this->rejectionReason);
            $this->notifySuccess('Peralatan berhasil ditolak!');
            $this->closeRejectReviewModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan review peralatan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors()->toArray());
            $this->notifyValidationError($e);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', Peralatan::class);

        return (new PeralatanExport(
            $this->search,
            $this->calibrationStatusFilter ?: null,
            $this->conditionFilter ?: null,
            $this->ownershipStatusFilter ?: null,
            $this->reviewStatusFilter ?: null
        ))
            ->download('peralatan-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $this->authorize('exportPdf', Peralatan::class);

        $peralatan = Peralatan::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%")
                      ->orWhere('location', 'like', "%{$this->search}%");
                });
            })
            ->when($this->calibrationStatusFilter, function ($q) {
                $q->where('calibration_status', $this->calibrationStatusFilter);
            })
            ->when($this->conditionFilter, function ($q) {
                $q->where('condition', $this->conditionFilter);
            })
            ->when($this->ownershipStatusFilter, function ($q) {
                $q->where('ownership_status', $this->ownershipStatusFilter);
            })
            ->when($this->reviewStatusFilter !== null && $this->reviewStatusFilter !== '', function ($q) {
                $q->where('review_status', $this->reviewStatusFilter);
            })
            ->where('is_active', true)
            ->with('evidences')
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('exports.peralatan-pdf', ['peralatan' => $peralatan]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'peralatan-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function getCalibrationStatusOptionsProperty(): array
    {
        return collect(\App\Enums\CalibrationStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getConditionOptionsProperty(): array
    {
        return collect(\App\Enums\EquipmentCondition::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getOwnershipStatusOptionsProperty(): array
    {
        return collect(\App\Enums\OwnershipStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function render(PeralatanService $service)
    {
        return view('livewire.master-data.peralatan-management', [
            'peralatan' => $service->getFiltered(
                $this->search,
                false,
                $this->calibrationStatusFilter ?: null,
                $this->conditionFilter ?: null,
                $this->ownershipStatusFilter ?: null,
                10,
                $this->reviewStatusFilter ?: null
            ),
            'reviewStatuses' => \App\Enums\PeralatanReviewStatus::cases(),
        ]);
    }
}
