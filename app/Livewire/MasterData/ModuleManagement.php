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

    public $showDeleteModal = false;
    public $deletingModuleId;
    public $deletingModuleName;

    public function mount()
    {
        $this->authorize('viewAny', Module::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRiskFilter()
    {
        $this->resetPage();
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
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
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
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function exportExcel()
    {
        $this->authorize('exportExcel', Module::class);

        return (new ModulesExport($this->search, $this->riskFilter))
            ->download('modul-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(ModuleService $service)
    {
        $this->authorize('exportPdf', Module::class);

        $modules = Module::with('deliverables')->withCount('projects')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%")
                      ->orWhere('scope', 'like', "%{$this->search}%");
                });
            })
            ->when($this->riskFilter !== null && $this->riskFilter !== '', function ($q) {
                $q->where('risk_level', $this->riskFilter);
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
        return view('livewire.master-data.module-management', [
            'modules' => $service->getFiltered(
                $this->search,
                $this->riskFilter,
                false
            ),
            'riskLevels' => RiskLevel::cases(),
        ]);
    }
}
