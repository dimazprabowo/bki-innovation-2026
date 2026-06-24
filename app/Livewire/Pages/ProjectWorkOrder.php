<?php

namespace App\Livewire\Pages;

use App\Livewire\Traits\HasNotification;
use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectWorkOrderChecklist;
use App\Models\WorkOrderItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ProjectWorkOrder extends Component
{
    use AuthorizesRequests, HasNotification;

    public Project $project;

    public array $checklist = [];

    public function mount(Project $project): void
    {
        $this->authorize('manageWorkOrder', $project);
        $this->project = $project->load([
            'modules.workOrderItems.subitems',
            'modules.workOrderReferences',
            'workOrderChecklists',
        ]);

        $this->buildChecklist();
    }

    protected function buildChecklist(): void
    {
        $existing = $this->project->workOrderChecklists->keyBy(function ($c) {
            return $c->work_order_item_id . '-' . ($c->work_order_subitem_id ?? '0');
        });

        $this->checklist = [];

        foreach ($this->project->modules as $module) {
            foreach ($module->workOrderItems as $item) {
                if ($item->subitems->isNotEmpty()) {
                    foreach ($item->subitems as $subitem) {
                        $key = $item->id . '-' . $subitem->id;
                        $record = $existing->get($item->id . '-' . $subitem->id);
                        $this->checklist[$key] = [
                            'module_id' => $module->id,
                            'work_order_item_id' => $item->id,
                            'work_order_subitem_id' => $subitem->id,
                            'is_checked' => $record?->is_checked ?? false,
                            'notes' => $record?->notes ?? '',
                        ];
                    }
                } else {
                    $key = $item->id . '-0';
                    $record = $existing->get($item->id . '-0');
                    $this->checklist[$key] = [
                        'module_id' => $module->id,
                        'work_order_item_id' => $item->id,
                        'work_order_subitem_id' => null,
                        'is_checked' => $record?->is_checked ?? false,
                        'notes' => $record?->notes ?? '',
                    ];
                }
            }
        }
    }

    public function toggleCheck(string $key): void
    {
        if (!isset($this->checklist[$key])) {
            return;
        }

        $this->checklist[$key]['is_checked'] = !$this->checklist[$key]['is_checked'];
        $this->saveChecklistItem($key);
    }

    public function updatedChecklist($value, string $key): void
    {
        $topKey = explode('.', $key)[0];

        try {
            $this->saveChecklistItem($topKey);
            $this->cascadeUncheck();

            $isChecked = $this->checklist[$topKey]['is_checked'] ?? false;
            if ($isChecked) {
                $this->notifySuccess('Item checklist berhasil disimpan.');
            } else {
                $this->notifyInfo('Item checklist berhasil di-uncheck.');
            }
        } catch (\Exception $e) {
            $this->notifyError('Gagal menyimpan checklist. Silakan coba lagi.');
        }
    }

    protected function cascadeUncheck(): void
    {
        $changed = false;

        foreach ($this->project->modules as $module) {
            $items = $module->workOrderItems
                ->filter(fn ($item) => $item->is_active)
                ->sortByDesc(fn ($item) => $item->nature === 'mandatory')
                ->sortBy(fn ($item) => $item->order)
                ->values();

            $hasUncheckedMandatory = false;

            foreach ($items as $item) {
                $activeSubitems = $item->subitems
                    ->filter(fn ($s) => $s->is_active)
                    ->sortByDesc(fn ($s) => $s->nature === 'mandatory')
                    ->sortBy(fn ($s) => $s->order)
                    ->values();

                if ($activeSubitems->isNotEmpty()) {
                    $hasUncheckedMandatorySub = $hasUncheckedMandatory;

                    foreach ($activeSubitems as $subitem) {
                        $key = $item->id . '-' . $subitem->id;
                        $isChecked = $this->checklist[$key]['is_checked'] ?? false;

                        if (!$isChecked && $subitem->nature === 'mandatory') {
                            $hasUncheckedMandatorySub = true;
                        }

                        if ($hasUncheckedMandatorySub && $isChecked) {
                            $this->checklist[$key]['is_checked'] = false;
                            $this->saveChecklistItem($key);
                            $changed = true;
                        }
                    }

                    $allSubitemsChecked = !$hasUncheckedMandatorySub
                        && $activeSubitems->every(fn ($s) => ($this->checklist[$item->id . '-' . $s->id]['is_checked'] ?? false));

                    if (!$allSubitemsChecked && $item->nature === 'mandatory') {
                        $hasUncheckedMandatory = true;
                    }
                } else {
                    $key = $item->id . '-0';
                    $isChecked = $this->checklist[$key]['is_checked'] ?? false;

                    if (!$isChecked && $item->nature === 'mandatory') {
                        $hasUncheckedMandatory = true;
                    }

                    if ($hasUncheckedMandatory && $isChecked) {
                        $this->checklist[$key]['is_checked'] = false;
                        $this->saveChecklistItem($key);
                        $changed = true;
                    }
                }
            }
        }
    }

    protected function saveChecklistItem(string $key): void
    {
        $data = $this->checklist[$key];

        $record = ProjectWorkOrderChecklist::where('project_id', $this->project->id)
            ->where('work_order_item_id', $data['work_order_item_id'])
            ->where('work_order_subitem_id', $data['work_order_subitem_id'])
            ->first();

        if ($data['is_checked']) {
            if ($record) {
                $record->update([
                    'is_checked' => true,
                    'checked_by' => auth()->id(),
                    'checked_at' => now(),
                    'notes' => $data['notes'] ?? null,
                ]);
            } else {
                ProjectWorkOrderChecklist::create([
                    'project_id' => $this->project->id,
                    'module_id' => $data['module_id'],
                    'work_order_item_id' => $data['work_order_item_id'],
                    'work_order_subitem_id' => $data['work_order_subitem_id'],
                    'is_checked' => true,
                    'checked_by' => auth()->id(),
                    'checked_at' => now(),
                    'notes' => $data['notes'] ?? null,
                ]);
            }
        } else {
            if ($record) {
                $record->update([
                    'is_checked' => false,
                    'checked_by' => null,
                    'checked_at' => null,
                    'notes' => $data['notes'] ?? null,
                ]);
            }
        }
    }

    #[\Livewire\Attributes\Computed]
    public function moduleGroups(): array
    {
        $groups = [];
        $moduleIndex = 0;

        foreach ($this->project->modules as $module) {
            $moduleIndex++;

            $items = $module->workOrderItems
                ->filter(fn ($item) => $item->is_active)
                ->sortByDesc(fn ($item) => $item->nature === 'mandatory')
                ->sortBy(fn ($item) => $item->order)
                ->values();

            if ($items->isEmpty()) {
                continue;
            }

            $totalChecks = 0;
            $checkedCount = 0;
            $itemNumber = 0;
            $structuredItems = [];
            $hasUncheckedMandatoryBefore = false;

            foreach ($items as $item) {
                $itemNumber++;
                $activeSubitems = $item->subitems
                    ->filter(fn ($s) => $s->is_active)
                    ->sortByDesc(fn ($s) => $s->nature === 'mandatory')
                    ->sortBy(fn ($s) => $s->order)
                    ->values();

                $itemLocked = $hasUncheckedMandatoryBefore;

                if ($activeSubitems->isNotEmpty()) {
                    $subNumber = 0;
                    $allSubitemsChecked = true;
                    $structuredSubitems = [];
                    $hasUncheckedMandatorySubBefore = false;

                    foreach ($activeSubitems as $subitem) {
                        $subNumber++;
                        $totalChecks++;
                        $key = $item->id . '-' . $subitem->id;
                        $subChecked = $this->checklist[$key]['is_checked'] ?? false;
                        $subLocked = $hasUncheckedMandatorySubBefore || $itemLocked;

                        if ($subChecked) {
                            $checkedCount++;
                        } else {
                            $allSubitemsChecked = false;
                            if ($subitem->nature === 'mandatory') {
                                $hasUncheckedMandatorySubBefore = true;
                            }
                        }

                        $structuredSubitems[] = [
                            'subitem' => $subitem,
                            'number' => "{$moduleIndex}.{$itemNumber}." . chr(96 + $subNumber),
                            'key' => $key,
                            'locked' => $subLocked,
                        ];
                    }

                    if (!$allSubitemsChecked && $item->nature === 'mandatory') {
                        $hasUncheckedMandatoryBefore = true;
                    }

                    $structuredItems[] = [
                        'item' => $item,
                        'number' => "{$moduleIndex}.{$itemNumber}",
                        'has_subitems' => true,
                        'all_subitems_checked' => $allSubitemsChecked,
                        'locked' => $itemLocked,
                        'subitems' => $structuredSubitems,
                    ];
                } else {
                    $totalChecks++;
                    $key = $item->id . '-0';
                    $isChecked = $this->checklist[$key]['is_checked'] ?? false;
                    if ($isChecked) {
                        $checkedCount++;
                    } else {
                        if ($item->nature === 'mandatory') {
                            $hasUncheckedMandatoryBefore = true;
                        }
                    }

                    $structuredItems[] = [
                        'item' => $item,
                        'number' => "{$moduleIndex}.{$itemNumber}",
                        'has_subitems' => false,
                        'all_subitems_checked' => $isChecked,
                        'locked' => $itemLocked,
                        'subitems' => [],
                        'key' => $key,
                    ];
                }
            }

            $groups[] = [
                'module' => $module,
                'module_number' => $moduleIndex,
                'items' => $structuredItems,
                'total' => $totalChecks,
                'checked' => $checkedCount,
                'progress' => $totalChecks > 0 ? round(($checkedCount / $totalChecks) * 100) : 0,
            ];
        }

        return $groups;
    }

    public function goBack()
    {
        return $this->redirect(route('projects.index'), navigate: true);
    }

    public function notifyLocked()
    {
        $this->notifyWarning('Selesaikan item wajib sebelumnya terlebih dahulu sebelum mengakses item ini.');
    }

    public function render()
    {
        return view('livewire.pages.project-work-order');
    }
}
