<?php

namespace App\Services;

use App\Models\Competency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompetencyService
{
    public function getFiltered(
        ?string $search = null,
        ?string $level = null,
        ?string $isActive = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Competency::query()
            ->when($isActive !== null && $isActive !== '', fn ($q) => $q->where('is_active', $isActive === '1'))
            ->search($search)
            ->byLevel($level)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): Competency
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = strtoupper($data['code']);
            return Competency::create($data);
        });
    }

    public function update(Competency $competency, array $data): Competency
    {
        DB::transaction(function () use ($competency, $data) {
            $data['code'] = strtoupper($data['code']);
            $competency->update($data);
        });

        return $competency->fresh();
    }

    public function delete(Competency $competency): bool
    {
        return DB::transaction(function () use ($competency) {
            return $competency->delete();
        });
    }

    public function toggleStatus(Competency $competency): Competency
    {
        DB::transaction(function () use ($competency) {
            $competency->update(['is_active' => !$competency->is_active]);
        });

        return $competency->fresh();
    }

    public function getActiveCompetencies()
    {
        return Competency::active()
            ->orderBy('name')
            ->get();
    }

    public function getLevelOptions(): array
    {
        return [
            ['value' => '', 'label' => 'Semua Level'],
            ['value' => '1', 'label' => 'Level 1'],
            ['value' => '2', 'label' => 'Level 2'],
            ['value' => '3', 'label' => 'Level 3'],
        ];
    }
}
