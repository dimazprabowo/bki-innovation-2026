<?php

namespace App\Livewire\MasterData;

use App\Enums\RiskLevel;
use App\Exports\ModulesExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Module;
use App\Services\ModuleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class ModuleManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $riskFilter = '';
    public $reviewStatusFilter = '';
    public $isActiveFilter = '';
    public bool $filterChanged = false;

    public $showDeleteModal = false;
    public $deletingModuleId;
    public $deletingModuleName;

    public $showRejectReviewModal = false;
    public $rejectingModuleId;
    public $rejectingModuleName;
    public $rejectionReason = '';

    public $showApproveReviewModal = false;
    public $approvingModuleId;
    public $approvingModuleName;
    public $approvalNote = '';

    public function mount()
    {
        $this->authorize('viewAny', Module::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingRiskFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingReviewStatusFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatedIsActiveFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function resetFilters()
    {
        $this->riskFilter = '';
        $this->reviewStatusFilter = '';
        $this->isActiveFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function getIsActiveOptionsProperty(): array
    {
        return [
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Nonaktif'],
        ];
    }

    public function getRiskLevelOptionsProperty(): array
    {
        return collect(RiskLevel::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function getReviewStatusOptionsProperty(): array
    {
        return collect(\App\Enums\ModuleReviewStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function create()
    {
        $this->authorize('create', Module::class);
        return $this->redirect(route('master-data.modules.create'), navigate: true);
    }

    public function edit($id)
    {
        $module = Module::findOrFail($id);
        $this->authorize('update', $module);
        return $this->redirect(route('master-data.modules.edit', $module), navigate: true);
    }

    public function confirmDelete($id)
    {
        $module = Module::findOrFail($id);
        $this->deletingModuleId = $module->id;
        $this->deletingModuleName = $module->name;
        $this->showDeleteModal = true;
    }

    public function delete(ModuleService $service)
    {
        try {
            $module = Module::findOrFail($this->deletingModuleId);
            $this->authorize('delete', $module);

            $service->delete($module);
            $this->notifySuccess('Modul berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat menghapus modul ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function toggleStatus($id, ModuleService $service)
    {
        try {
            $module = Module::findOrFail($id);
            $this->authorize('toggleStatus', $module);

            $service->toggleStatus($module);
            $status = $module->fresh()->is_active ? 'aktif' : 'non-aktif';
            $this->notifySuccess("Status modul berhasil diubah menjadi {$status}!");
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk mengubah status modul.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmApproveReview($id)
    {
        $module = Module::findOrFail($id);
        $this->approvingModuleId = $module->id;
        $this->approvingModuleName = $module->name;
        $this->approvalNote = '';
        $this->showApproveReviewModal = true;
    }

    public function closeApproveReviewModal()
    {
        $this->showApproveReviewModal = false;
        $this->approvingModuleId = null;
        $this->approvingModuleName = null;
        $this->approvalNote = '';
    }

    public function approveReview(ModuleService $service)
    {
        try {
            $this->validate([
                'approvalNote' => 'nullable|string|max:500',
            ], [
                'approvalNote.max' => 'Catatan persetujuan maksimal 500 karakter.',
            ]);

            $module = Module::findOrFail($this->approvingModuleId);
            $this->authorize('reviewModule', $module);

            $service->approveReview($module, auth()->id(), $this->approvalNote ?: null);
            $this->notifySuccess('Modul berhasil disetujui!');
            $this->closeApproveReviewModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan review modul.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors()->toArray());
            $this->notifyValidationError($e);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmRejectReview($id)
    {
        $module = Module::findOrFail($id);
        $this->rejectingModuleId = $module->id;
        $this->rejectingModuleName = $module->name;
        $this->rejectionReason = '';
        $this->showRejectReviewModal = true;
    }

    public function closeRejectReviewModal()
    {
        $this->showRejectReviewModal = false;
        $this->rejectingModuleId = null;
        $this->rejectingModuleName = null;
        $this->rejectionReason = '';
    }

    public function rejectReview(ModuleService $service)
    {
        try {
            $this->validate([
                'rejectionReason' => 'required|string|min:10|max:500',
            ], [
                'rejectionReason.required' => 'Alasan penolakan harus diisi.',
                'rejectionReason.min' => 'Alasan penolakan minimal 10 karakter.',
                'rejectionReason.max' => 'Alasan penolakan maksimal 500 karakter.',
            ]);

            $module = Module::findOrFail($this->rejectingModuleId);
            $this->authorize('reviewModule', $module);

            $service->rejectReview($module, auth()->id(), $this->rejectionReason);
            $this->notifySuccess('Modul berhasil ditolak!');
            $this->closeRejectReviewModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan review modul.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors()->toArray());
            $this->notifyValidationError($e);
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }


    public function exportExcel()
    {
        $this->authorize('exportExcel', Module::class);

        return (new ModulesExport($this->search, $this->riskFilter, $this->reviewStatusFilter, $this->isActiveFilter !== '' ? $this->isActiveFilter : null))
            ->download('modul-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(ModuleService $service)
    {
        $this->authorize('exportPdf', Module::class);

        $modules = Module::with('deliverables')->withCount('projects')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->riskFilter !== null && $this->riskFilter !== '', function ($q) {
                $q->where('risk_level', $this->riskFilter);
            })
            ->when($this->reviewStatusFilter !== null && $this->reviewStatusFilter !== '', function ($q) {
                $q->where('review_status', $this->reviewStatusFilter);
            })
            ->when($this->isActiveFilter !== null && $this->isActiveFilter !== '', function ($q) {
                $q->where('is_active', $this->isActiveFilter === '1');
            })
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('exports.modules-pdf', ['modules' => $modules]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'modul-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(ModuleService $service)
    {
        $modules = $service->getFiltered(
            $this->search,
            $this->riskFilter,
            $this->reviewStatusFilter,
            $this->isActiveFilter !== '' ? $this->isActiveFilter : null
        );

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$modules->total()} data modul.");
            $this->filterChanged = false;
        }

        return view('livewire.master-data.module-management', [
            'modules' => $modules,
        ]);
    }
}
