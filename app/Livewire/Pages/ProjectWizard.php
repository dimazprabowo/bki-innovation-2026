<?php

namespace App\Livewire\Pages;

use App\Enums\ApprovalStatus;
use App\Enums\CalibrationStatus;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use App\Models\Module;
use App\Models\ModulePersonel;
use App\Models\ModuleTool;
use App\Models\Personel;
use App\Models\Peralatan;
use App\Models\Project;
use App\Livewire\Traits\HasNotification;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProjectWizard extends Component
{
    use AuthorizesRequests, HasNotification;

    public ?Project $project = null;
    #[Url(as: 'step', except: 1)]
    public int $currentStep = 1;
    public int $totalSteps = 5;

    public $code = '';
    public $name = '';
    public $description = '';
    public $priority = 'medium';
    public $risk_level = 'low';
    public $start_date = '';
    public $end_date = '';

    public array $selectedModules = [];

    public array $personelAssignments = [];

    public array $peralatanAssignments = [];

    public array $additionalCosts = [];

    public $showDeleteModuleModal = false;
    public $deletingModuleIndex = null;
    public $showDeleteCostModal = false;
    public $deletingCostIndex = null;
    public $showDeletePeralatanModal = false;
    public $deletingPeralatanIndex = null;
    public $showSubmitModal = false;


    public function mount(?Project $project = null): void
    {
        if ($project && $project->exists) {
            $this->authorize('update', $project);
            $this->project = $project;
            $this->fillFromProject($project);
            $this->recalculateRiskLevel();
        } else {
            $this->currentStep = 1;
        }

        $this->additionalCosts = $this->additionalCosts ?: [['name' => '', 'amount' => '', 'notes' => '']];

        if ($this->currentStep === 3) {
            $this->generatePersonelAssignments();
        }
        if ($this->currentStep === 4) {
            $this->generatePeralatanAssignments();
        }
    }

    protected function fillFromProject(Project $project): void
    {
        $this->code = $project->code;
        $this->name = $project->name;
        $this->description = $project->description ?? '';
        $this->priority = $project->priority?->value ?? 'medium';
        $this->risk_level = $project->risk_level?->value ?? 'low';
        $this->start_date = $project->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $project->end_date?->format('Y-m-d') ?? '';

        foreach ($project->modules as $module) {
            $this->selectedModules[] = [
                'module_id' => $module->id,
                'quantity' => $module->pivot->quantity,
                'unit_price' => $module->pivot->unit_price,
                'notes' => $module->pivot->notes ?? '',
            ];
        }

        foreach ($project->projectPersonels as $pp) {
            $personel = ModulePersonel::find($pp->module_personel_id);
            $slot = 1;
            if ($personel) {
                $existingForPersonel = collect($this->personelAssignments)
                    ->filter(fn ($a) => $a['module_personel_id'] === $pp->module_personel_id)
                    ->count();
                $slot = $existingForPersonel + 1;
            }
            $this->personelAssignments[] = [
                'module_id' => $pp->module_id,
                'module_personel_id' => $pp->module_personel_id,
                'personel_id' => $pp->personel_id,
                'slot' => $slot,
            ];
        }

        foreach ($project->projectPeralatans as $pp) {
            $this->peralatanAssignments[] = [
                'module_id' => $pp->module_id,
                'module_tool_id' => $pp->module_tool_id,
                'peralatan_id' => $pp->peralatan_id,
                'slot' => $pp->slot ?? 1,
            ];
        }

        $costs = $project->additionalCosts->toArray();
        if (count($costs)) {
            $this->additionalCosts = collect($costs)->map(fn ($c) => [
                'name' => $c['name'],
                'amount' => $c['amount'],
                'notes' => $c['notes'] ?? '',
            ])->toArray();
        }
    }

    #[Computed]
    public function modules()
    {
        return Module::active()
            ->with(['personels.competencies', 'tools.peralatan', 'deliverables', 'workOrderItems.subitems', 'workOrderReferences'])
            ->orderBy('name')
            ->get();
    }

    public function selectedModuleIds(): array
    {
        return collect($this->selectedModules)->pluck('module_id')->filter()->values()->toArray();
    }

    #[Computed]
    public function modulePersonels()
    {
        $ids = $this->selectedModuleIds();
        if (empty($ids)) {
            return collect();
        }

        return ModulePersonel::with(['competencies', 'module'])
            ->whereIn('module_id', $ids)
            ->get();
    }

    #[Computed]
    public function availablePersonels()
    {
        return Personel::active()
            ->with('competencies')
            ->orderBy('name')
            ->get();
    }

    public function personelsForSlot($modulePersonelId): array
    {
        $personel = $this->modulePersonels->firstWhere('id', $modulePersonelId);
        $matchedClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        $unmatchedClass = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';

        if (!$personel || $personel->competencies->isEmpty()) {
            return $this->availablePersonels->map(fn ($p) => [
                'id' => $p->id,
                'label' => $p->name . ' (' . $p->code . ')',
                'badges' => $p->competencies->map(fn ($c) => [
                    'text' => $c->name,
                    'badgeClass' => $unmatchedClass,
                ])->values()->toArray(),
            ])->toArray();
        }

        $requiredCompetencyIds = $personel->competencies->pluck('id');

        return $this->availablePersonels
            ->filter(fn ($p) => $p->competencies->pluck('id')->intersect($requiredCompetencyIds)->isNotEmpty())
            ->map(fn ($p) => [
                'id' => $p->id,
                'label' => $p->name . ' (' . $p->code . ')',
                'badges' => $p->competencies->map(fn ($c) => [
                    'text' => $c->name,
                    'badgeClass' => $requiredCompetencyIds->contains($c->id) ? $matchedClass : $unmatchedClass,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
    }

    public function positionCompetencyBadges($modulePersonelId): array
    {
        $personel = $this->modulePersonels->firstWhere('id', $modulePersonelId);

        if (!$personel || $personel->competencies->isEmpty()) {
            return [];
        }

        $assignedPersonelIds = collect($this->personelAssignments)
            ->filter(fn ($a) => $a['module_personel_id'] == $modulePersonelId && !empty($a['personel_id']))
            ->pluck('personel_id')
            ->unique();

        $assignedCompetencyIds = $this->availablePersonels
            ->whereIn('id', $assignedPersonelIds)
            ->flatMap(fn ($p) => $p->competencies->pluck('id'))
            ->unique();

        $fulfilledClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        $unfulfilledClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';

        return $personel->competencies->map(fn ($c) => [
            'text' => $c->name,
            'badgeClass' => $assignedCompetencyIds->contains($c->id) ? $fulfilledClass : $unfulfilledClass,
        ])->values()->toArray();
    }

    public function generatePersonelAssignments(): void
    {
        $existing = collect($this->personelAssignments)->keyBy(fn ($a) => $a['module_personel_id'] . '-' . $a['slot']);
        $newAssignments = [];

        foreach ($this->modulePersonels as $personel) {
            for ($slot = 1; $slot <= $personel->quantity; $slot++) {
                $key = $personel->id . '-' . $slot;
                if ($existing->has($key)) {
                    $existingItem = $existing->get($key);
                    $newAssignments[] = [
                        'module_id' => $personel->module_id,
                        'module_personel_id' => $personel->id,
                        'personel_id' => $existingItem['personel_id'] ?? '',
                        'slot' => $slot,
                    ];
                } else {
                    $newAssignments[] = [
                        'module_id' => $personel->module_id,
                        'module_personel_id' => $personel->id,
                        'personel_id' => '',
                        'slot' => $slot,
                    ];
                }
            }
        }

        $this->personelAssignments = $newAssignments;
    }

    #[Computed]
    public function personelAssignmentsGrouped()
    {
        return collect($this->personelAssignments)
            ->groupBy('module_personel_id')
            ->map(fn ($items, $personelId) => [
                'personel' => $this->modulePersonels->firstWhere('id', $personelId),
                'slots' => $items->values()->toArray(),
            ])
            ->filter(fn ($group) => $group['personel'] !== null)
            ->toArray();
    }

    #[Computed]
    public function personelAssignmentsByModule()
    {
        $selectedIds = $this->selectedModuleIds();
        if (empty($selectedIds)) {
            return [];
        }

        $modules = Module::whereIn('id', $selectedIds)->orderBy('name')->get()->keyBy('id');

        return collect($this->personelAssignments)
            ->groupBy('module_id')
            ->map(function ($moduleItems, $moduleId) use ($modules) {
                $module = $modules->get($moduleId);
                if (!$module) {
                    return null;
                }

                $positions = $moduleItems
                    ->groupBy('module_personel_id')
                    ->map(function ($items, $personelId) {
                        $personel = $this->modulePersonels->firstWhere('id', $personelId);
                        if (!$personel) {
                            return null;
                        }

                        return [
                            'personel' => $personel,
                            'slots' => $items->values()->toArray(),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();

                return [
                    'module' => $module,
                    'positions' => $positions,
                ];
            })
            ->filter()
            ->sortBy(fn ($group) => $group['module']->name)
            ->values()
            ->toArray();
    }

    #[Computed]
    public function moduleTools()
    {
        $ids = $this->selectedModuleIds();
        if (empty($ids)) {
            return collect();
        }

        return ModuleTool::with(['peralatan', 'module'])
            ->whereIn('module_id', $ids)
            ->get();
    }

    #[Computed]
    public function availablePeralatans()
    {
        return Peralatan::active()
            ->orderBy('name')
            ->get();
    }

    public function peralatansForTool($moduleToolId): array
    {
        $tool = $this->moduleTools->firstWhere('id', $moduleToolId);
        if (!$tool || !$tool->peralatan_id) {
            return $this->availablePeralatans->map(function ($p) {
                $parts = [];
                if ($p->location) $parts[] = $p->location;
                $calLabel = $p->calibration_status?->label() ?? '';
                if ($calLabel) $parts[] = $calLabel;
                if ($p->calibration_expired_date) {
                    $parts[] = $p->calibration_expired_date->format('d/m/Y');
                }
                return [
                    'id' => $p->id,
                    'label' => $p->name . ' (' . $p->code . ')',
                    'sublabel' => implode(' · ', $parts),
                ];
            })->toArray();
        }

        $requiredPeralatanId = $tool->peralatan_id;

        return $this->availablePeralatans
            ->filter(function ($p) use ($requiredPeralatanId, $tool) {
                if ($p->id !== $requiredPeralatanId) {
                    return false;
                }

                if ($tool->requires_calibration) {
                    return $p->calibration_status === CalibrationStatus::Calibrated
                        && !$p->calibration_status_expired;
                }

                return true;
            })
            ->map(function ($p) {
                $parts = [];
                if ($p->location) $parts[] = $p->location;
                $calLabel = $p->calibration_status?->label() ?? '';
                if ($calLabel) $parts[] = $calLabel;
                if ($p->calibration_expired_date) {
                    $expStatus = $p->calibration_status_expired ? ' (Expired)' : '';
                    $parts[] = $p->calibration_expired_date->format('d/m/Y') . $expStatus;
                }
                return [
                    'id' => $p->id,
                    'label' => $p->name . ' (' . $p->code . ')',
                    'sublabel' => implode(' · ', $parts),
                ];
            })
            ->values()
            ->toArray();
    }

    public function generatePeralatanAssignments(): void
    {
        $existing = collect($this->peralatanAssignments)->keyBy(fn ($a) => $a['module_tool_id'] . '-' . $a['slot']);
        $newAssignments = [];

        foreach ($this->moduleTools as $tool) {
            for ($slot = 1; $slot <= $tool->quantity; $slot++) {
                $key = $tool->id . '-' . $slot;
                $existingItem = $existing->get($key);
                $newAssignments[] = [
                    'module_id' => $tool->module_id,
                    'module_tool_id' => $tool->id,
                    'peralatan_id' => $existingItem['peralatan_id'] ?? '',
                    'slot' => $slot,
                ];
            }
        }

        $this->peralatanAssignments = $newAssignments;
    }

    #[Computed]
    public function peralatanAssignmentsGrouped()
    {
        return collect($this->peralatanAssignments)
            ->groupBy('module_tool_id')
            ->map(fn ($items, $toolId) => [
                'tool' => $this->moduleTools->firstWhere('id', $toolId),
                'slots' => $items->values()->toArray(),
            ])
            ->filter(fn ($group) => $group['tool'] !== null)
            ->toArray();
    }

    #[Computed]
    public function peralatanAssignmentsByModule()
    {
        $selectedIds = $this->selectedModuleIds();
        if (empty($selectedIds)) {
            return [];
        }

        $modules = Module::whereIn('id', $selectedIds)->orderBy('name')->get()->keyBy('id');

        return collect($this->peralatanAssignments)
            ->groupBy('module_id')
            ->map(function ($moduleItems, $moduleId) use ($modules) {
                $module = $modules->get($moduleId);
                if (!$module) {
                    return null;
                }

                $tools = $moduleItems
                    ->groupBy('module_tool_id')
                    ->map(function ($items, $toolId) {
                        $tool = $this->moduleTools->firstWhere('id', $toolId);
                        if (!$tool) {
                            return null;
                        }

                        return [
                            'tool' => $tool,
                            'slots' => $items->values()->toArray(),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();

                return [
                    'module' => $module,
                    'tools' => $tools,
                ];
            })
            ->filter()
            ->sortBy(fn ($group) => $group['module']->name)
            ->values()
            ->toArray();
    }

    public function getBaseCostProperty(): float
    {
        $total = 0;
        foreach ($this->selectedModules as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $total += $qty * $price;
        }
        return (float) $total;
    }

    public function getAdditionalCostTotalProperty(): float
    {
        return (float) collect($this->additionalCosts)->sum(fn ($c) => (float) ($c['amount'] ?? 0));
    }

    public function getTotalCostProperty(): float
    {
        return $this->base_cost + $this->additional_cost_total;
    }

    public function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'code' => ['required', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($this->project?->id)],
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:' . implode(',', ProjectPriority::values()),
                'risk_level' => 'required|in:' . implode(',', RiskLevel::values()),
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ],
            2 => [
                'selectedModules' => 'required|array|min:1',
                'selectedModules.*.module_id' => 'required|exists:modules,id',
                'selectedModules.*.quantity' => 'required|integer|min:1',
                'selectedModules.*.unit_price' => 'required|numeric|min:0',
            ],
            3 => [
                'personelAssignments' => 'nullable|array',
            ],
            4 => [
                'peralatanAssignments' => 'nullable|array',
            ],
            5 => [
                'additionalCosts.*.name' => 'nullable|string|max:255',
                'additionalCosts.*.amount' => 'nullable|numeric|min:0',
                'additionalCosts.*.notes' => 'nullable|string',
            ],
            default => [],
        };
    }

    protected function rules(): array
    {
        return $this->rulesForStep($this->currentStep);
    }

    public function isStepComplete(int $step): bool
    {
        $rules = $this->rulesForStep($step);
        if (empty($rules)) {
            return true;
        }

        $validator = \Validator::make($this->all(), $rules);
        return !$validator->fails();
    }

    public function canGoToStep(int $step): bool
    {
        if ($step <= 1) {
            return true;
        }

        for ($i = 1; $i < $step; $i++) {
            if (!$this->isStepComplete($i)) {
                return false;
            }
        }

        return true;
    }

    protected function ensureDraftProject(): Project
    {
        if ($this->project && $this->project->exists) {
            return $this->project;
        }

        $service = app(ProjectService::class);
        $this->project = $service->createDraft([
            'code' => $this->code ?: 'TMP-' . time(),
            'name' => $this->name ?: 'Draft Project',
            'description' => $this->description,
            'priority' => $this->priority,
            'risk_level' => $this->risk_level,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
        ]);

        return $this->project;
    }

    public function autosaveStep1(): void
    {
        $step1Rules = [
            'code' => ['required', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($this->project?->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:' . implode(',', ProjectPriority::values()),
            'risk_level' => 'required|in:' . implode(',', RiskLevel::values()),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
        $this->withValidator(function ($validator) {
            if ($validator->fails()) {
                $errors = $validator->errors();
                $allErrors = $errors->all();
                $message = count($allErrors) > 1
                    ? 'Terdapat ' . count($allErrors) . ' kesalahan validasi'
                    : $allErrors[0];
                $this->dispatch('notify', type: 'error', message: $message);
            }
        })->validate($step1Rules);
        $project = $this->ensureDraftProject();

        app(ProjectService::class)->autosave($project, [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'risk_level' => $this->risk_level,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
        ]);
    }

    public function autosaveStep2(): void
    {
        $project = $this->ensureDraftProject();

        $this->recalculateRiskLevel();

        $modules = collect($this->selectedModules)
            ->filter(fn ($item) => !empty($item['module_id']))
            ->map(fn ($item) => [
                'module_id' => $item['module_id'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'notes' => $item['notes'] ?? null,
            ])
            ->toArray();

        app(ProjectService::class)->syncModules($project, $modules);

        app(ProjectService::class)->autosave($project, [
            'risk_level' => $this->risk_level,
        ]);
    }

    public function autosaveStep3(): void
    {
        $project = $this->ensureDraftProject();

        $personels = collect($this->personelAssignments)
            ->filter(fn ($a) => !empty($a['personel_id']))
            ->unique(fn ($a) => $a['module_personel_id'] . '-' . $a['personel_id'])
            ->values()
            ->toArray();

        app(ProjectService::class)->syncPersonels($project, $personels);
    }

    public function autosaveStep4(): void
    {
        $project = $this->ensureDraftProject();

        $peralatans = collect($this->peralatanAssignments)
            ->filter(fn ($a) => !empty($a['peralatan_id']))
            ->values()
            ->toArray();

        app(ProjectService::class)->syncPeralatans($project, $peralatans);
    }

    public function autosaveStep5(): void
    {
        $project = $this->ensureDraftProject();

        app(ProjectService::class)->syncAdditionalCosts($project, $this->additionalCosts);
    }

    public function nextStep(): void
    {
        $this->withValidator(function ($validator) {
            if ($validator->fails()) {
                $errors = $validator->errors();
                $allErrors = $errors->all();
                $message = count($allErrors) > 1
                    ? 'Terdapat ' . count($allErrors) . ' kesalahan validasi'
                    : $allErrors[0];
                $this->dispatch('notify', type: 'error', message: $message);
            }
        })->validate($this->rules());

        if ($this->project && $this->project->exists) {
            match ($this->currentStep) {
                1 => $this->autosaveStep1(),
                2 => $this->autosaveStep2(),
                3 => $this->autosaveStep3(),
                4 => $this->autosaveStep4(),
                5 => $this->autosaveStep5(),
                default => null,
            };
            $this->notifySuccess('Data langkah ' . $this->currentStep . ' berhasil disimpan.');
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            if ($this->currentStep === 3) {
                $this->generatePersonelAssignments();
            }
            if ($this->currentStep === 4) {
                $this->generatePeralatanAssignments();
            }
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps && $this->canGoToStep($step)) {
            if ($step !== $this->currentStep) {
                if ($this->project && $this->project->exists) {
                    match ($this->currentStep) {
                        1 => $this->autosaveStep1(),
                        2 => $this->autosaveStep2(),
                        3 => $this->autosaveStep3(),
                        4 => $this->autosaveStep4(),
                        5 => $this->autosaveStep5(),
                        default => null,
                    };
                    $this->notifySuccess('Data langkah ' . $this->currentStep . ' berhasil disimpan.');
                }

                $this->currentStep = $step;
                if ($step === 3) {
                    $this->generatePersonelAssignments();
                }
                if ($step === 4) {
                    $this->generatePeralatanAssignments();
                }
            }
        }
    }

    public function addModule(): void
    {
        $this->selectedModules[] = [
            'module_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'notes' => '',
        ];
    }

    public function updatedSelectedModules(): void
    {
        $this->recalculateRiskLevel();
    }

    protected function recalculateRiskLevel(): void
    {
        $moduleIds = $this->selectedModuleIds();
        if (empty($moduleIds)) {
            return;
        }

        $maxRisk = Module::whereIn('id', $moduleIds)
            ->pluck('risk_level')
            ->sortByDesc(function ($level) {
                return match ($level) {
                    RiskLevel::High => 3,
                    RiskLevel::Medium => 2,
                    RiskLevel::Low => 1,
                    default => 0,
                };
            })
            ->first();

        if ($maxRisk) {
            $this->risk_level = $maxRisk->value;
        }
    }

    public function removeModule(int $index): void
    {
        $this->deletingModuleIndex = $index;
        $this->showDeleteModuleModal = true;
    }

    public function confirmDeleteModule(): void
    {
        if ($this->deletingModuleIndex !== null) {
            unset($this->selectedModules[$this->deletingModuleIndex]);
            $this->selectedModules = array_values($this->selectedModules);
            $this->deletingModuleIndex = null;
            $this->showDeleteModuleModal = false;
        }
    }

    public function onModuleSelected(int $index, int $moduleId): void
    {
        $module = Module::find($moduleId);
        if ($module) {
            $this->selectedModules[$index]['unit_price'] = $module->pricing_baseline;
        }
    }

    public function addAdditionalCost(): void
    {
        $this->additionalCosts[] = ['name' => '', 'amount' => '', 'notes' => ''];
    }

    public function removeAdditionalCost(int $index): void
    {
        $this->deletingCostIndex = $index;
        $this->showDeleteCostModal = true;
    }

    public function confirmDeleteAdditionalCost(): void
    {
        if ($this->deletingCostIndex !== null) {
            unset($this->additionalCosts[$this->deletingCostIndex]);
            $this->additionalCosts = array_values($this->additionalCosts);
            $this->deletingCostIndex = null;
            $this->showDeleteCostModal = false;
        }
    }

    public function confirmSubmit(): void
    {
        $this->showSubmitModal = true;
    }

    public function submitProject(): void
    {
        $this->showSubmitModal = false;

        $this->withValidator(function ($validator) {
            if ($validator->fails()) {
                $errors = $validator->errors();
                $allErrors = $errors->all();
                $message = count($allErrors) > 1
                    ? 'Terdapat ' . count($allErrors) . ' kesalahan validasi'
                    : $allErrors[0];
                $this->dispatch('notify', type: 'error', message: $message);
            }
        })->validate($this->rules());

        try {
            $project = $this->ensureDraftProject();

            $this->autosaveStep1();
            $this->autosaveStep2();
            $this->autosaveStep3();
            $this->autosaveStep4();
            $this->autosaveStep5();

            $this->authorize('submit', $project);

            app(ProjectService::class)->submit($project);

            $this->notifySuccess('Project berhasil diajukan.');

            $this->redirect(route('projects.index'), navigate: true);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function saveDraft(): void
    {
        try {
            match ($this->currentStep) {
                1 => $this->autosaveStep1(),
                2 => $this->autosaveStep2(),
                3 => $this->autosaveStep3(),
                4 => $this->autosaveStep4(),
                5 => $this->autosaveStep5(),
                default => null,
            };

            $this->notifySuccess('Draft berhasil disimpan.');

            $this->js("window.history.pushState({}, '', '" . route('projects.edit', $this->project) . "?step=" . $this->currentStep . "')");
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function goBack()
    {
        return $this->redirect(route('projects.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.project-wizard');
    }
}
