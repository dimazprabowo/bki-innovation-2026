<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ModuleService
{
    public function getFiltered(
        ?string $search = null,
        ?string $riskLevel = null,
        ?bool $activeOnly = true,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Module::query()
            ->when($activeOnly, fn ($q) => $q->active())
            ->search($search)
            ->byRiskLevel($riskLevel)
            ->withCount('projects')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): Module
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = strtoupper($data['code']);
            return Module::create($data);
        });
    }

    public function update(Module $module, array $data): Module
    {
        DB::transaction(function () use ($module, $data) {
            $data['code'] = strtoupper($data['code']);
            $module->update($data);
        });

        return $module->fresh();
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

    public function getActiveModules()
    {
        return Module::active()
            ->orderBy('name')
            ->get();
    }
}
