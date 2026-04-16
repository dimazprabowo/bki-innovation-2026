<?php

namespace App\Livewire\MasterData;

use App\Enums\CoEControlLevel;
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
    public $showModal = false;
    public $editMode = false;

    public $moduleId;
    public $code;
    public $name;
    public $scope;
    public $method;
    public $resource;
    public $duration;
    public $deliverable;
    public $risk_level = 'low';
    public $pricing_baseline;
    public $coe_control_level = 'none';
    public $is_active = 1;
    public $notes;

    public $showDeleteModal = false;
    public $deletingModuleId;
    public $deletingModuleName;

    public function mount()
    {
        $this->authorize('viewAny', Module::class);
    }

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'max:50', $this->editMode ? 'unique:modules,code,' . $this->moduleId : 'unique:modules,code'],
            'name' => 'required|string|max:255',
            'scope' => 'nullable|string',
            'method' => 'nullable|string|max:255',
            'resource' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'deliverable' => 'nullable|string',
            'risk_level' => ['required', 'string', 'in:' . implode(',', RiskLevel::values())],
            'pricing_baseline' => 'nullable|numeric|min:0',
            'coe_control_level' => ['required', 'string', 'in:' . implode(',', CoEControlLevel::values())],
            'is_active' => 'required|in:0,1',
            'notes' => 'nullable|string',
        ];
    }

    public function validationAttributes()
    {
        return [
            'code' => 'kode modul',
            'name' => 'nama modul',
            'scope' => 'scope',
            'method' => 'metode',
            'resource' => 'resource',
            'duration' => 'durasi',
            'deliverable' => 'deliverable',
            'risk_level' => 'tingkat risiko',
            'pricing_baseline' => 'harga baseline',
            'coe_control_level' => 'level kontrol CoE',
            'is_active' => 'status aktif',
            'notes' => 'catatan',
        ];
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
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $module = Module::findOrFail($id);
        $this->authorize('update', $module);

        $this->moduleId = $module->id;
        $this->code = $module->code;
        $this->name = $module->name;
        $this->scope = $module->scope;
        $this->method = $module->method;
        $this->resource = $module->resource;
        $this->duration = $module->duration;
        $this->deliverable = $module->deliverable;
        $this->risk_level = $module->risk_level->value;
        $this->pricing_baseline = $module->pricing_baseline;
        $this->coe_control_level = $module->coe_control_level->value;
        $this->is_active = $module->is_active ? 1 : 0;
        $this->notes = $module->notes;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(ModuleService $service)
    {
        $this->validate();

        try {
            $data = [
                'code' => $this->code,
                'name' => $this->name,
                'scope' => $this->scope,
                'method' => $this->method,
                'resource' => $this->resource,
                'duration' => $this->duration,
                'deliverable' => $this->deliverable,
                'risk_level' => $this->risk_level,
                'pricing_baseline' => $this->pricing_baseline,
                'coe_control_level' => $this->coe_control_level,
                'is_active' => $this->is_active,
                'notes' => $this->notes,
            ];

            if ($this->editMode) {
                $module = Module::findOrFail($this->moduleId);
                $this->authorize('update', $module);
                $service->update($module, $data);
                $message = 'Modul berhasil diupdate!';
            } else {
                $this->authorize('create', Module::class);
                $service->create($data);
                $message = 'Modul berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            $this->closeModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
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

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset([
            'moduleId',
            'code',
            'name',
            'scope',
            'method',
            'resource',
            'duration',
            'deliverable',
            'risk_level',
            'pricing_baseline',
            'coe_control_level',
            'is_active',
            'notes',
        ]);

        $this->risk_level = RiskLevel::Low->value;
        $this->coe_control_level = CoEControlLevel::None->value;
        $this->is_active = 1;
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', Module::class);

        return (new ModulesExport($this->search, $this->riskFilter))
            ->download('modul-pengadaan-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(ModuleService $service)
    {
        $this->authorize('exportPdf', Module::class);

        $modules = Module::withCount('projects')
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
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('exports.modules-pdf', ['modules' => $modules]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'modul-pengadaan-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(ModuleService $service)
    {
        return view('livewire.master-data.module-management', [
            'modules' => $service->getFiltered(
                $this->search,
                $this->riskFilter,
                true
            ),
            'riskLevels' => RiskLevel::cases(),
            'coeLevels' => CoEControlLevel::cases(),
        ]);
    }
}
