<?php

namespace App\Services;

use App\Enums\ModuleReviewStatus;
use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ModuleService
{
    public function getFiltered(
        ?string $search = null,
        ?string $riskLevel = null,
        ?string $reviewStatus = null,
        ?bool $activeOnly = true,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Module::query()
            ->when($activeOnly, fn ($q) => $q->active())
            ->search($search)
            ->byRiskLevel($riskLevel)
            ->byReviewStatus($reviewStatus)
            ->withCount('projects')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): Module
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = strtoupper($data['code']);
            $module = Module::create($data);

            // Handle work order items with subitems
            if (isset($data['work_order_items'])) {
                foreach ($data['work_order_items'] as $itemData) {
                    $item = $module->workOrderItems()->create([
                        'order' => $itemData['order'] ?? 0,
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'nature' => $itemData['nature'] ?? 'mandatory',
                        'is_active' => $itemData['is_active'] ?? true,
                    ]);

                    // Handle subitems
                    if (isset($itemData['subitems'])) {
                        foreach ($itemData['subitems'] as $subitemData) {
                            $item->subitems()->create([
                                'order' => $subitemData['order'] ?? 0,
                                'name' => $subitemData['name'],
                                'description' => $subitemData['description'] ?? null,
                                'nature' => $subitemData['nature'] ?? $itemData['nature'] ?? 'mandatory',
                                'is_active' => $subitemData['is_active'] ?? true,
                            ]);
                        }
                    }
                }
            }

            // Handle work order references
            if (isset($data['work_order_references'])) {
                foreach ($data['work_order_references'] as $refData) {
                    $module->workOrderReferences()->create([
                        'document_name' => $refData['document_name'],
                        'document_id' => $refData['document_id'] ?? null,
                        'file_path' => $refData['file_path'] ?? null,
                    ]);
                }
            }

            // Handle personels with competencies
            if (isset($data['personels'])) {
                foreach ($data['personels'] as $personelData) {
                    $personel = $module->personels()->create([
                        'position_name' => $personelData['position_name'],
                        'quantity' => $personelData['quantity'] ?? 1,
                        'nature' => $personelData['nature'] ?? 'mandatory',
                    ]);

                    // Handle competencies
                    if (isset($personelData['competencies'])) {
                        $personel->competencies()->sync($personelData['competencies']);
                    }
                }
            }

            // Handle tools
            if (isset($data['tools'])) {
                foreach ($data['tools'] as $toolData) {
                    $module->tools()->create([
                        'peralatan_id' => $toolData['peralatan_id'] ?? null,
                        'requires_calibration' => $toolData['requires_calibration'] ?? false,
                        'quantity' => $toolData['quantity'] ?? 1,
                    ]);
                }
            }

            // Handle deliverables
            if (isset($data['deliverables'])) {
                foreach ($data['deliverables'] as $delData) {
                    $module->deliverables()->create([
                        'order' => $delData['order'] ?? 0,
                        'name' => $delData['name'],
                        'description' => $delData['description'] ?? null,
                        'nature' => $delData['nature'] ?? 'mandatory',
                        'is_active' => $delData['is_active'] ?? true,
                    ]);
                }
            }

            return $module->load(['workOrderItems.subitems', 'workOrderReferences', 'personels.competencies', 'tools', 'deliverables']);
        });
    }

    public function update(Module $module, array $data): Module
    {
        DB::transaction(function () use ($module, $data) {
            $data['code'] = strtoupper($data['code']);

            // If module was rejected, reset review status to pending on update
            if ($module->isRejected()) {
                $data['review_status'] = ModuleReviewStatus::Pending->value;
                $data['rejection_reason'] = null;
            }

            $module->update($data);

            // Handle work order items with subitems
            if (isset($data['work_order_items'])) {
                $module->workOrderItems()->delete();
                foreach ($data['work_order_items'] as $itemData) {
                    $item = $module->workOrderItems()->create([
                        'order' => $itemData['order'] ?? 0,
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'nature' => $itemData['nature'] ?? 'mandatory',
                        'is_active' => $itemData['is_active'] ?? true,
                    ]);

                    if (isset($itemData['subitems'])) {
                        foreach ($itemData['subitems'] as $subitemData) {
                            $item->subitems()->create([
                                'order' => $subitemData['order'] ?? 0,
                                'name' => $subitemData['name'],
                                'description' => $subitemData['description'] ?? null,
                                'nature' => $subitemData['nature'] ?? $itemData['nature'] ?? 'mandatory',
                                'is_active' => $subitemData['is_active'] ?? true,
                            ]);
                        }
                    }
                }
            }

            // Handle work order references with file processing
            if (isset($data['work_order_references']) && is_array($data['work_order_references'])) {
                // Get existing reference IDs
                $existingReferenceIds = $module->workOrderReferences()->pluck('id')->toArray();
                $newReferenceIds = collect($data['work_order_references'])->pluck('id')->filter()->toArray();

                // Delete references that are no longer in the new list
                $toDelete = array_diff($existingReferenceIds, $newReferenceIds);
                foreach ($toDelete as $referenceId) {
                    $reference = $module->workOrderReferences()->find($referenceId);
                    if ($reference) {
                        // Delete file if exists
                        if ($reference->file_path && Storage::disk(file_disk())->exists($reference->file_path)) {
                            Storage::disk(file_disk())->delete($reference->file_path);
                        }
                        $reference->delete();
                    }
                }

                // Update or create new references
                foreach ($data['work_order_references'] as $refData) {
                    if (empty($refData['document_name'])) {
                        continue;
                    }

                    $refUpdateData = [
                        'document_name' => $refData['document_name'],
                        'document_id' => $refData['document_id'] ?? null,
                        'file_status' => 'pending',
                    ];

                    if (isset($refData['temp_file_path'])) {
                        $refUpdateData['file_status'] = 'processing';
                    } elseif (isset($refData['file_path'])) {
                        $refUpdateData['file_path'] = $refData['file_path'];
                        $refUpdateData['file_name'] = $refData['file_name'];
                        $refUpdateData['file_size'] = $refData['file_size'];
                        $refUpdateData['file_status'] = 'completed';
                    }

                    // Check if reference already exists
                    if (isset($refData['id']) && in_array($refData['id'], $existingReferenceIds)) {
                        $reference = $module->workOrderReferences()->find($refData['id']);
                        if ($reference) {
                            $reference->update($refUpdateData);

                            if (isset($refData['temp_file_path'])) {
                                \App\Jobs\ProcessWorkOrderReference::dispatch(
                                    $reference->id,
                                    $refData['temp_file_path'],
                                    $refData['file_name']
                                );
                            }
                        }
                    } else {
                        $reference = $module->workOrderReferences()->create($refUpdateData);

                        if (isset($refData['temp_file_path'])) {
                            \App\Jobs\ProcessWorkOrderReference::dispatch(
                                $reference->id,
                                $refData['temp_file_path'],
                                $refData['file_name']
                            );
                        }
                    }
                }
            }

            // Handle personels with competencies
            if (isset($data['personels'])) {
                $module->personels()->delete();
                foreach ($data['personels'] as $personelData) {
                    $personel = $module->personels()->create([
                        'position_name' => $personelData['position_name'],
                        'quantity' => $personelData['quantity'] ?? 1,
                        'nature' => $personelData['nature'] ?? 'mandatory',
                    ]);

                    if (isset($personelData['competencies'])) {
                        $personel->competencies()->sync($personelData['competencies']);
                    }
                }
            }

            // Handle tools
            if (isset($data['tools'])) {
                $module->tools()->delete();
                foreach ($data['tools'] as $toolData) {
                    $module->tools()->create([
                        'peralatan_id' => $toolData['peralatan_id'] ?? null,
                        'requires_calibration' => $toolData['requires_calibration'] ?? false,
                        'quantity' => $toolData['quantity'] ?? 1,
                    ]);
                }
            }

            // Handle deliverables
            if (isset($data['deliverables'])) {
                $module->deliverables()->delete();
                foreach ($data['deliverables'] as $delData) {
                    $module->deliverables()->create([
                        'order' => $delData['order'] ?? 0,
                        'name' => $delData['name'],
                        'description' => $delData['description'] ?? null,
                        'nature' => $delData['nature'] ?? 'mandatory',
                        'is_active' => $delData['is_active'] ?? true,
                    ]);
                }
            }
        });

        return $module->fresh()->load(['workOrderItems.subitems', 'workOrderReferences', 'personels.competencies', 'tools', 'deliverables']);
    }

    public function delete(Module $module): bool
    {
        return DB::transaction(function () use ($module) {
            if ($module->projects()->exists()) {
                throw new \Exception('Module tidak dapat dihapus karena masih digunakan dalam project.');
            }
            return $module->delete();
        });
    }

    public function toggleStatus(Module $module): Module
    {
        DB::transaction(function () use ($module) {
            $module->update(['is_active' => !$module->is_active]);
        });

        return $module->fresh();
    }

    public function approveReview(Module $module, int $reviewerId, ?string $note = null): Module
    {
        return DB::transaction(function () use ($module, $reviewerId, $note) {
            $module->update([
                'review_status' => ModuleReviewStatus::Approved->value,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
                'rejection_reason' => null,
                'approval_note' => $note,
            ]);

            return $module->fresh();
        });
    }

    public function rejectReview(Module $module, int $reviewerId, string $reason): Module
    {
        return DB::transaction(function () use ($module, $reviewerId, $reason) {
            $module->update([
                'review_status' => ModuleReviewStatus::Rejected->value,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            return $module->fresh();
        });
    }

    public function getActiveModules()
    {
        return Module::active()
            ->orderBy('name')
            ->get();
    }
}
