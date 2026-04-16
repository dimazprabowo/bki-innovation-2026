<?php

namespace App\Livewire\Pages;

use App\Enums\RiskLevel;
use App\Exports\ProjectsExport;
use App\Livewire\Traits\HasNotification;
use App\Models\Module;
use App\Models\Project;
use App\Models\User;
use App\Services\ModuleService;
use App\Services\ProjectService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectManagement extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification;

    public $search = '';
    public $statusFilter = '';
    public $riskFilter = '';
    public $showModal = false;
    public $editMode = false;
    public $viewMode = false;

    public $projectId;
    public $code;
    public $name;
    public $scope;
    public $method;
    public $duration;
    public $deliverable;
    public $risk_level = 'low';
    public $notes;

    public $selectedModules = [];
    public $availableModules = [];

    public $selectedResources = [];
    public $availableUsers = [];

    public $selectedEquipments = [];
    public $selectedAccommodations = [];

    public $showDeleteModal = false;
    public $deletingProjectId;
    public $deletingProjectName;

    public $showRemoveModuleModal = false;
    public $removingModuleIndex;
    public $removingModuleName;
    public $removingType = 'module';

    public $showRejectModal = false;
    public $rejectingProjectId;
    public $rejectionReason = '';

    public $showStopModal = false;
    public $stoppingProjectId;
    public $stopReason = '';

    public $currentProject;

    public function mount()
    {
        $this->authorize('viewAny', Project::class);
    }

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'max:50', $this->editMode ? 'unique:projects,code,' . $this->projectId : 'unique:projects,code'],
            'name' => 'required|string|max:255',
            'scope' => 'nullable|string',
            'method' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'deliverable' => 'nullable|string',
            'risk_level' => ['required', 'string', 'in:' . implode(',', RiskLevel::values())],
            'notes' => 'nullable|string',
        ];
    }

    public function validationAttributes()
    {
        return [
            'code' => 'kode project',
            'name' => 'nama project',
            'scope' => 'scope',
            'method' => 'metode',
            'duration' => 'durasi',
            'deliverable' => 'deliverable',
            'risk_level' => 'tingkat risiko',
            'notes' => 'catatan',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingRiskFilter()
    {
        $this->resetPage();
    }

    public function create(ModuleService $moduleService)
    {
        $this->authorize('create', Project::class);
        $this->resetForm();
        $this->availableModules = $moduleService->getActiveModules();
        $this->availableUsers = User::active()
            ->withCount(['projectResources as active_projects_count' => function($query) {
                $query->whereIn('status', ['draft', 'coe_review', 'approved']);
            }])
            ->orderBy('name')
            ->get();
        $this->editMode = false;
        $this->viewMode = false;
        $this->showModal = true;
    }

    public function edit($id, ModuleService $moduleService)
    {
        $project = Project::with(['modules', 'resources', 'equipments', 'accommodations'])->findOrFail($id);
        $this->authorize('update', $project);

        $this->projectId = $project->id;
        $this->code = $project->code;
        $this->name = $project->name;
        $this->scope = $project->scope;
        $this->method = $project->method;
        $this->duration = $project->duration;
        $this->deliverable = $project->deliverable;
        $this->risk_level = $project->risk_level->value;
        $this->notes = $project->notes;

        $this->selectedModules = $project->modules->map(function ($module) {
            return [
                'module_id' => $module->id,
                'quantity' => $module->pivot->quantity,
                'unit_price' => $module->pivot->unit_price,
                'notes' => $module->pivot->notes,
            ];
        })->toArray();

        $this->selectedResources = $project->resources->map(function ($user) {
            return [
                'user_id' => $user->id,
            ];
        })->toArray();

        $this->selectedEquipments = $project->equipments->map(function ($equipment) {
            return [
                'name' => $equipment->name,
                'specification' => $equipment->specification,
                'quantity' => $equipment->quantity,
                'unit' => $equipment->unit,
                'notes' => $equipment->notes,
            ];
        })->toArray();

        $this->selectedAccommodations = $project->accommodations->map(function ($accommodation) {
            return [
                'type' => $accommodation->type,
                'description' => $accommodation->description,
                'quantity' => $accommodation->quantity,
                'unit' => $accommodation->unit,
                'estimated_cost' => $accommodation->estimated_cost,
                'notes' => $accommodation->notes,
            ];
        })->toArray();

        $this->availableModules = $moduleService->getActiveModules();
        $this->availableUsers = User::active()
            ->withCount(['projectResources as active_projects_count' => function($query) {
                $query->whereIn('status', ['draft', 'coe_review', 'approved']);
            }])
            ->orderBy('name')
            ->get();
        $this->editMode = true;
        $this->viewMode = false;
        $this->showModal = true;
    }

    public function view($id)
    {
        $this->currentProject = Project::with([
            'modules', 
            'creator', 
            'approver', 
            'resources' => function($query) {
                $query->withCount(['projectResources as active_projects_count' => function($q) {
                    $q->whereIn('status', ['draft', 'coe_review', 'approved']);
                }]);
            }
        ])->findOrFail($id);
        $this->viewMode = true;
        $this->showModal = true;
    }

    public function addModule()
    {
        $this->selectedModules[] = [
            'module_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'notes' => '',
        ];
    }

    public function addResource()
    {
        $this->selectedResources[] = [
            'user_id' => '',
        ];
    }

    public function addEquipment()
    {
        $this->selectedEquipments[] = [
            'name' => '',
            'specification' => '',
            'quantity' => 1,
            'unit' => '',
            'notes' => '',
        ];
    }

    public function addAccommodation()
    {
        $this->selectedAccommodations[] = [
            'type' => 'accommodation',
            'description' => '',
            'quantity' => 1,
            'unit' => '',
            'estimated_cost' => 0,
            'notes' => '',
        ];
    }

    public function confirmRemoveResource($index)
    {
        $this->removingModuleIndex = $index;
        $this->removingType = 'resource';
        
        // Get user name untuk ditampilkan di modal
        if (isset($this->selectedResources[$index]['user_id']) && !empty($this->selectedResources[$index]['user_id'])) {
            $userId = $this->selectedResources[$index]['user_id'];
            $user = User::find($userId);
            $this->removingModuleName = $user ? $user->name : 'Resource ini';
        } else {
            $this->removingModuleName = 'Resource ini';
        }
        
        $this->showRemoveModuleModal = true;
    }

    public function confirmRemoveEquipment($index)
    {
        $this->removingModuleIndex = $index;
        $this->removingType = 'equipment';
        $this->removingModuleName = $this->selectedEquipments[$index]['name'] ?? 'Alat ini';
        $this->showRemoveModuleModal = true;
    }

    public function confirmRemoveAccommodation($index)
    {
        $this->removingModuleIndex = $index;
        $this->removingType = 'accommodation';
        $this->removingModuleName = $this->selectedAccommodations[$index]['description'] ?? 'Item ini';
        $this->showRemoveModuleModal = true;
    }

    public function confirmRemoveModule($index)
    {
        $this->removingModuleIndex = $index;
        $this->removingType = 'module';
        
        // Get module name untuk ditampilkan di modal
        if (isset($this->selectedModules[$index]['module_id']) && !empty($this->selectedModules[$index]['module_id'])) {
            $moduleId = $this->selectedModules[$index]['module_id'];
            $module = Module::find($moduleId);
            $this->removingModuleName = $module ? $module->name : 'Modul ini';
        } else {
            $this->removingModuleName = 'Modul ini';
        }
        
        $this->showRemoveModuleModal = true;
    }

    public function removeModule()
    {
        if ($this->removingModuleIndex !== null) {
            switch ($this->removingType) {
                case 'resource':
                    unset($this->selectedResources[$this->removingModuleIndex]);
                    $this->selectedResources = array_values($this->selectedResources);
                    $message = 'Resource SDM berhasil dihapus dari list!';
                    break;
                case 'equipment':
                    unset($this->selectedEquipments[$this->removingModuleIndex]);
                    $this->selectedEquipments = array_values($this->selectedEquipments);
                    $message = 'Alat berhasil dihapus dari list!';
                    break;
                case 'accommodation':
                    unset($this->selectedAccommodations[$this->removingModuleIndex]);
                    $this->selectedAccommodations = array_values($this->selectedAccommodations);
                    $message = 'Akomodasi/Transportasi berhasil dihapus dari list!';
                    break;
                default:
                    unset($this->selectedModules[$this->removingModuleIndex]);
                    $this->selectedModules = array_values($this->selectedModules);
                    $this->calculateProjectRiskLevel();
                    $message = 'Modul berhasil dihapus dari list!';
                    break;
            }
            
            $this->showRemoveModuleModal = false;
            $this->removingModuleIndex = null;
            $this->notifySuccess($message);
        }
    }

    public function updatedSelectedModules($value, $key)
    {
        if (str_contains($key, 'module_id')) {
            $index = (int) explode('.', $key)[0];
            $moduleId = $this->selectedModules[$index]['module_id'];
            
            if ($moduleId) {
                // Modul dipilih - set harga dari pricing_baseline
                $module = Module::find($moduleId);
                if ($module && $module->pricing_baseline) {
                    $this->selectedModules[$index]['unit_price'] = $module->pricing_baseline;
                }
            } else {
                // Modul di-clear - reset harga ke 0
                $this->selectedModules[$index]['unit_price'] = 0;
            }
            
            // Auto-calculate risk level berdasarkan modul yang dipilih
            $this->calculateProjectRiskLevel();
        }
    }

    /**
     * Calculate project risk level based on selected modules.
     * Logic: Ambil risiko tertinggi dari semua modul yang dipilih.
     * Jika ada minimal 1 modul tinggi → Project = Tinggi
     * Jika tidak ada tinggi tapi ada sedang → Project = Sedang
     * Jika semua rendah → Project = Rendah
     */
    private function calculateProjectRiskLevel()
    {
        $hasHigh = false;
        $hasMedium = false;
        
        foreach ($this->selectedModules as $selectedModule) {
            if (!empty($selectedModule['module_id'])) {
                $module = Module::find($selectedModule['module_id']);
                if ($module) {
                    if ($module->risk_level->value === 'high') {
                        $hasHigh = true;
                        break; // Langsung break karena sudah ketemu tinggi
                    } elseif ($module->risk_level->value === 'medium') {
                        $hasMedium = true;
                    }
                }
            }
        }
        
        // Set risk level berdasarkan prioritas
        if ($hasHigh) {
            $this->risk_level = RiskLevel::High->value;
        } elseif ($hasMedium) {
            $this->risk_level = RiskLevel::Medium->value;
        } else {
            $this->risk_level = RiskLevel::Low->value;
        }
    }

    public function save(ProjectService $service)
    {
        $this->validate();

        try {
            $data = [
                'code' => $this->code,
                'name' => $this->name,
                'scope' => $this->scope,
                'method' => $this->method,
                'duration' => $this->duration,
                'deliverable' => $this->deliverable,
                'risk_level' => $this->risk_level,
                'notes' => $this->notes,
            ];

            $modules = array_filter($this->selectedModules, fn($m) => !empty($m['module_id']));
            $resources = collect($this->selectedResources ?? [])
                ->filter(fn($r) => !empty($r['user_id']))
                ->pluck('user_id')
                ->unique()
                ->toArray();
            $equipments = array_filter($this->selectedEquipments ?? [], fn($e) => !empty($e['name']));
            $accommodations = array_filter($this->selectedAccommodations ?? [], fn($a) => !empty($a['description']));

            if ($this->editMode) {
                $project = Project::findOrFail($this->projectId);
                $this->authorize('update', $project);
                $service->update($project, $data, $modules, $resources, $equipments, $accommodations);
                $message = 'Project berhasil diupdate!';
            } else {
                $this->authorize('create', Project::class);
                $service->create($data, $modules, $resources, $equipments, $accommodations);
                $message = 'Project berhasil dibuat!';
            }

            $this->notifySuccess($message);
            $this->closeModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function submit($id, ProjectService $service)
    {
        try {
            $project = Project::findOrFail($id);
            $this->authorize('submit', $project);

            $service->submit($project);
            
            $freshProject = $project->fresh();
            $message = $freshProject->requiresCoEControl() 
                ? 'Project berhasil disubmit dan masuk ke CoE Review!'
                : 'Project berhasil disubmit dan otomatis diapprove!';
            
            $this->notifySuccess($message);
            
            // Close modal dan refresh data hanya jika berhasil
            $this->closeModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat submit project ini.');
            // Modal tetap terbuka saat error
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
            // Modal tetap terbuka saat error
        }
    }

    public function approve($id, ProjectService $service)
    {
        try {
            $project = Project::findOrFail($id);
            $this->authorize('approve', $project);

            $service->approve($project, auth()->id());
            $this->notifySuccess('Project berhasil diapprove!');
            
            // Close modal dan refresh data hanya jika berhasil
            $this->closeModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk approve project.');
            // Modal tetap terbuka saat error
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
            // Modal tetap terbuka saat error
        }
    }

    public function confirmReject($id)
    {
        $this->rejectingProjectId = $id;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function reject(ProjectService $service)
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10|max:500',
        ], [
            'rejectionReason.required' => 'Alasan penolakan harus diisi.',
            'rejectionReason.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejectionReason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        try {
            $project = Project::findOrFail($this->rejectingProjectId);
            $this->authorize('approve', $project); // Same permission as approve

            $service->reject($project, $this->rejectionReason);
            $this->notifySuccess('Project berhasil ditolak!');
            
            // Close modal dan refresh data
            $this->showRejectModal = false;
            $this->closeModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk reject project.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmStop($id)
    {
        $this->stoppingProjectId = $id;
        $this->stopReason = '';
        $this->showStopModal = true;
    }

    public function stop(ProjectService $service)
    {
        $this->validate([
            'stopReason' => 'required|string|min:10|max:500',
        ], [
            'stopReason.required' => 'Alasan stop harus diisi.',
            'stopReason.min' => 'Alasan stop minimal 10 karakter.',
            'stopReason.max' => 'Alasan stop maksimal 500 karakter.',
        ]);

        try {
            $project = Project::findOrFail($this->stoppingProjectId);
            $this->authorize('approve', $project); // Same permission as approve

            $service->stop($project, $this->stopReason);
            $this->notifySuccess('Project berhasil di-stop! Project ini menjadi data mati dan tidak dapat diedit lagi.');
            
            // Close modal dan refresh data
            $this->showStopModal = false;
            $this->closeModal();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk stop project.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $project = Project::findOrFail($id);
        $this->deletingProjectId = $project->id;
        $this->deletingProjectName = $project->name;
        $this->showDeleteModal = true;
    }

    public function delete(ProjectService $service)
    {
        try {
            $project = Project::findOrFail($this->deletingProjectId);
            $this->authorize('delete', $project);

            $service->delete($project);
            $this->notifySuccess('Project berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak dapat menghapus project ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->viewMode = false;
        $this->currentProject = null;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset([
            'projectId',
            'code',
            'name',
            'scope',
            'method',
            'duration',
            'deliverable',
            'risk_level',
            'notes',
            'selectedModules',
            'availableModules',
            'selectedResources',
            'availableUsers',
            'selectedEquipments',
            'selectedAccommodations',
        ]);

        $this->risk_level = RiskLevel::Low->value;
    }

    public function exportExcel()
    {
        $this->authorize('exportExcel', Project::class);

        return (new ProjectsExport($this->search, $this->statusFilter, $this->riskFilter))
            ->download('project-pengadaan-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $this->authorize('exportPdf', Project::class);

        $projects = Project::with(['creator', 'approver'])
            ->withCount('modules')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%")
                      ->orWhere('scope', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== null && $this->statusFilter !== '', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->riskFilter !== null && $this->riskFilter !== '', function ($q) {
                $q->where('risk_level', $this->riskFilter);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('exports.projects-pdf', ['projects' => $projects]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'project-pengadaan-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(ProjectService $service)
    {
        return view('livewire.pages.project-management', [
            'projects' => $service->getFiltered(
                $this->search,
                $this->statusFilter,
                $this->riskFilter
            ),
            'riskLevels' => RiskLevel::cases(),
            'statuses' => [
                'draft' => 'Draft',
                'coe_review' => 'CoE Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'stopped' => 'Stopped',
            ],
        ]);
    }
}
