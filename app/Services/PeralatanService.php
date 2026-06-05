<?php

namespace App\Services;

use App\Jobs\ProcessPeralatanEvidence;
use App\Models\Peralatan;
use App\Models\PeralatanEvidence;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PeralatanService
{
    public function getFiltered(
        ?string $search = null,
        ?bool $activeOnly = false,
        ?string $calibrationStatus = null,
        ?string $condition = null,
        ?string $ownershipStatus = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Peralatan::query()
            ->when($activeOnly, fn ($q) => $q->active())
            ->search($search)
            ->byCalibrationStatus($calibrationStatus)
            ->byCondition($condition)
            ->byOwnershipStatus($ownershipStatus)
            ->withCount('evidences')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): Peralatan
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = strtoupper($data['code']);
            $peralatan = Peralatan::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'location' => $data['location'] ?? null,
                'calibration_status' => $data['calibration_status'] ?? 'not_required',
                'calibration_expired_date' => $data['calibration_expired_date'] ?? null,
                'condition' => $data['condition'] ?? 'suitable',
                'ownership_status' => $data['ownership_status'] ?? 'owned',
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['evidences']) && is_array($data['evidences'])) {
                foreach ($data['evidences'] as $evidenceData) {
                    if (empty($evidenceData['name'])) {
                        continue;
                    }

                    $evidenceCreateData = [
                        'name' => $evidenceData['name'],
                        'file_status' => 'pending',
                    ];

                    if (isset($evidenceData['temp_file_path'])) {
                        $evidenceCreateData['file_status'] = 'processing';
                    } elseif (isset($evidenceData['file_path'])) {
                        $evidenceCreateData['file_path'] = $evidenceData['file_path'];
                        $evidenceCreateData['file_name'] = $evidenceData['file_name'];
                        $evidenceCreateData['file_size'] = $evidenceData['file_size'];
                        $evidenceCreateData['file_status'] = 'completed';
                    }

                    $evidence = $peralatan->evidences()->create($evidenceCreateData);

                    if (isset($evidenceData['temp_file_path'])) {
                        ProcessPeralatanEvidence::dispatch(
                            $evidence->id,
                            $evidenceData['temp_file_path'],
                            $evidenceData['file_name']
                        );
                    }
                }
            }

            return $peralatan;
        });
    }

    public function update(Peralatan $peralatan, array $data): Peralatan
    {
        DB::transaction(function () use ($peralatan, $data) {
            $data['code'] = strtoupper($data['code']);
            $peralatan->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'] ?? $peralatan->description,
                'location' => $data['location'] ?? $peralatan->location,
                'calibration_status' => $data['calibration_status'] ?? $peralatan->calibration_status,
                'calibration_expired_date' => $data['calibration_expired_date'] ?? $peralatan->calibration_expired_date,
                'condition' => $data['condition'] ?? $peralatan->condition,
                'ownership_status' => $data['ownership_status'] ?? $peralatan->ownership_status,
                'is_active' => $data['is_active'] ?? $peralatan->is_active,
            ]);

            if (isset($data['evidences']) && is_array($data['evidences'])) {
                // Get existing evidence IDs
                $existingEvidenceIds = $peralatan->evidences()->pluck('id')->toArray();
                $newEvidenceIds = collect($data['evidences'])->pluck('id')->filter()->toArray();

                // Delete evidences that are no longer in the new list
                $toDelete = array_diff($existingEvidenceIds, $newEvidenceIds);
                foreach ($toDelete as $evidenceId) {
                    $evidence = $peralatan->evidences()->find($evidenceId);
                    if ($evidence) {
                        // Delete file if exists
                        if ($evidence->file_path && Storage::disk('local')->exists($evidence->file_path)) {
                            Storage::disk('local')->delete($evidence->file_path);
                        }
                        $evidence->delete();
                    }
                }

                // Update or create new evidences
                foreach ($data['evidences'] as $evidenceData) {
                    if (empty($evidenceData['name'])) {
                        continue;
                    }

                    $evidenceUpdateData = [
                        'name' => $evidenceData['name'],
                        'file_status' => 'pending',
                    ];

                    if (isset($evidenceData['temp_file_path'])) {
                        $evidenceUpdateData['file_status'] = 'processing';
                    } elseif (isset($evidenceData['file_path'])) {
                        $evidenceUpdateData['file_path'] = $evidenceData['file_path'];
                        $evidenceUpdateData['file_name'] = $evidenceData['file_name'];
                        $evidenceUpdateData['file_size'] = $evidenceData['file_size'];
                        $evidenceUpdateData['file_status'] = 'completed';
                    }

                    // Check if evidence already exists
                    if (isset($evidenceData['id']) && in_array($evidenceData['id'], $existingEvidenceIds)) {
                        $evidence = $peralatan->evidences()->find($evidenceData['id']);
                        if ($evidence) {
                            $evidence->update($evidenceUpdateData);

                            if (isset($evidenceData['temp_file_path'])) {
                                ProcessPeralatanEvidence::dispatch(
                                    $evidence->id,
                                    $evidenceData['temp_file_path'],
                                    $evidenceData['file_name']
                                );
                            }
                        }
                    } else {
                        $evidence = $peralatan->evidences()->create($evidenceUpdateData);

                        if (isset($evidenceData['temp_file_path'])) {
                            ProcessPeralatanEvidence::dispatch(
                                $evidence->id,
                                $evidenceData['temp_file_path'],
                                $evidenceData['file_name']
                            );
                        }
                    }
                }
            }
        });

        return $peralatan->fresh();
    }

    public function delete(Peralatan $peralatan): bool
    {
        return DB::transaction(function () use ($peralatan) {
            // Delete all evidence files
            foreach ($peralatan->evidences as $evidence) {
                if (Storage::disk('local')->exists($evidence->file_path)) {
                    Storage::disk('local')->delete($evidence->file_path);
                }
            }

            $peralatan->evidences()->delete();
            return $peralatan->delete();
        });
    }

    public function toggleStatus(Peralatan $peralatan): Peralatan
    {
        DB::transaction(function () use ($peralatan) {
            $peralatan->update(['is_active' => !$peralatan->is_active]);
        });

        return $peralatan->fresh();
    }

    public function getActivePeralatan()
    {
        return Peralatan::active()
            ->orderBy('name')
            ->get();
    }
}
