<?php

namespace App\Services;

use App\Jobs\ProcessPersonelCertificate;
use App\Models\Personel;
use App\Models\Competency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonelService
{
    public function getFiltered(
        ?string $search = null,
        ?string $isActive = null,
        ?int $competencyId = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Personel::query()
            ->when($isActive !== null && $isActive !== '', fn ($q) => $q->where('is_active', $isActive === '1'))
            ->search($search)
            ->when($competencyId, function ($query) use ($competencyId) {
                $query->whereHas('competencies', function ($q) use ($competencyId) {
                    $q->where('competencies.id', $competencyId);
                });
            })
            ->withCount('competencies')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): Personel
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = strtoupper($data['code']);
            $personel = Personel::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['competencies']) && is_array($data['competencies'])) {
                foreach ($data['competencies'] as $competencyData) {
                    if (!empty($competencyData['competency_id'])) {
                        $pivotData = [
                            'issuer' => $competencyData['issuer'] ?? null,
                            'issue_date' => $competencyData['issue_date'] ?? null,
                            'has_no_expiry' => $competencyData['has_no_expiry'] ?? false,
                            'expired_date' => $competencyData['expired_date'] ?? null,
                            'certificate_file_status' => 'pending',
                        ];

                        if (isset($competencyData['temp_file_path'])) {
                            $pivotData['certificate_file_status'] = 'processing';
                        } elseif (isset($competencyData['certificate_file_path'])) {
                            $pivotData['certificate_file_path'] = $competencyData['certificate_file_path'];
                            $pivotData['certificate_file_name'] = $competencyData['certificate_file_name'];
                            $pivotData['certificate_file_status'] = 'completed';
                        }

                        $personel->competencies()->attach($competencyData['competency_id'], $pivotData);

                        if (isset($competencyData['temp_file_path'])) {
                            ProcessPersonelCertificate::dispatch(
                                $personel->id,
                                $competencyData['competency_id'],
                                $competencyData['temp_file_path'],
                                $competencyData['file_name']
                            );
                        }
                    }
                }
            }

            return $personel;
        });
    }

    public function update(Personel $personel, array $data): Personel
    {
        DB::transaction(function () use ($personel, $data) {
            $data['code'] = strtoupper($data['code']);
            $personel->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'is_active' => $data['is_active'] ?? $personel->is_active,
            ]);

            if (isset($data['competencies']) && is_array($data['competencies'])) {
                // Get existing competency IDs
                $existingCompetencyIds = $personel->competencies()->pluck('competency_id')->toArray();
                $newCompetencyIds = collect($data['competencies'])->pluck('competency_id')->filter()->toArray();

                // Detach competencies that are no longer in the new list
                $toDetach = array_diff($existingCompetencyIds, $newCompetencyIds);
                foreach ($toDetach as $competencyId) {
                    $pivot = DB::table('personel_competency')
                        ->where('personel_id', $personel->id)
                        ->where('competency_id', $competencyId)
                        ->first();

                    if ($pivot) {
                        // Delete file if exists
                        if ($pivot->certificate_file_path && Storage::disk(file_disk())->exists($pivot->certificate_file_path)) {
                            Storage::disk(file_disk())->delete($pivot->certificate_file_path);
                        }
                        // Delete pivot record
                        DB::table('personel_competency')
                            ->where('personel_id', $personel->id)
                            ->where('competency_id', $competencyId)
                            ->delete();
                    }
                }

                // Update or attach new competencies
                foreach ($data['competencies'] as $competencyData) {
                    if (empty($competencyData['competency_id'])) {
                        continue;
                    }

                    $pivotData = [
                        'issuer' => $competencyData['issuer'] ?? null,
                        'issue_date' => $competencyData['issue_date'] ?? null,
                        'has_no_expiry' => $competencyData['has_no_expiry'] ?? false,
                        'expired_date' => $competencyData['expired_date'] ?? null,
                        'certificate_file_status' => 'pending',
                    ];

                    if (isset($competencyData['temp_file_path'])) {
                        $pivotData['certificate_file_status'] = 'processing';
                    } elseif (isset($competencyData['certificate_file_path'])) {
                        $pivotData['certificate_file_path'] = $competencyData['certificate_file_path'];
                        $pivotData['certificate_file_name'] = $competencyData['certificate_file_name'];
                        $pivotData['certificate_file_status'] = 'completed';
                    }

                    // Check if competency already exists
                    $existingPivot = DB::table('personel_competency')
                        ->where('personel_id', $personel->id)
                        ->where('competency_id', $competencyData['competency_id'])
                        ->first();

                    if ($existingPivot) {
                        // Update existing pivot
                        DB::table('personel_competency')
                            ->where('personel_id', $personel->id)
                            ->where('competency_id', $competencyData['competency_id'])
                            ->update($pivotData);

                        // Dispatch job if new file uploaded
                        if (isset($competencyData['temp_file_path'])) {
                            ProcessPersonelCertificate::dispatch(
                                $personel->id,
                                $competencyData['competency_id'],
                                $competencyData['temp_file_path'],
                                $competencyData['file_name']
                            );
                        }
                    } else {
                        // Attach new competency
                        $personel->competencies()->attach($competencyData['competency_id'], $pivotData);

                        if (isset($competencyData['temp_file_path'])) {
                            ProcessPersonelCertificate::dispatch(
                                $personel->id,
                                $competencyData['competency_id'],
                                $competencyData['temp_file_path'],
                                $competencyData['file_name']
                            );
                        }
                    }
                }
            }
        });

        return $personel->fresh();
    }

    public function delete(Personel $personel): bool
    {
        return DB::transaction(function () use ($personel) {
            // Delete all competency files
            $competencies = DB::table('personel_competency')
                ->where('personel_id', $personel->id)
                ->get();

            foreach ($competencies as $competency) {
                if ($competency->certificate_file_path && Storage::disk(file_disk())->exists($competency->certificate_file_path)) {
                    Storage::disk(file_disk())->delete($competency->certificate_file_path);
                }
            }

            $personel->competencies()->detach();
            return $personel->delete();
        });
    }

    public function toggleStatus(Personel $personel): Personel
    {
        DB::transaction(function () use ($personel) {
            $personel->update(['is_active' => !$personel->is_active]);
        });

        return $personel->fresh();
    }

    public function getActivePersonels()
    {
        return Personel::active()
            ->orderBy('name')
            ->get();
    }

    public function getActiveCompetencies()
    {
        return Competency::active()
            ->orderBy('name')
            ->get();
    }
}
