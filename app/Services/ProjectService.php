<?php

namespace App\Services;

use App\Enums\CoEControlLevel;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function getFiltered(
        ?string $search = null,
        ?string $status = null,
        ?string $riskLevel = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Project::query()
            ->with(['creator', 'modules'])
            ->search($search)
            ->byStatus($status)
            ->byRiskLevel($riskLevel)
            ->withCount('modules')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data, array $modules = []): Project
    {
        return DB::transaction(function () use ($data, $modules) {
            $data['code'] = strtoupper($data['code']);
            $data['created_by'] = auth()->id();
            
            $data['coe_control_level'] = $this->determineCoEControlLevel($data['risk_level']);
            
            $project = Project::create($data);

            if (!empty($modules)) {
                $this->syncModules($project, $modules);
            }

            return $project->load('modules');
        });
    }

    public function update(Project $project, array $data, ?array $modules = null): Project
    {
        return DB::transaction(function () use ($project, $data, $modules) {
            $data['code'] = strtoupper($data['code']);
            
            if (isset($data['risk_level'])) {
                $data['coe_control_level'] = $this->determineCoEControlLevel($data['risk_level']);
            }
            
            $project->update($data);

            if ($modules !== null) {
                $this->syncModules($project, $modules);
            }

            return $project->fresh(['modules']);
        });
    }

    public function delete(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            $project->modules()->detach();
            return $project->delete();
        });
    }

    public function submit(Project $project): Project
    {
        return DB::transaction(function () use ($project) {
            // Risiko Tinggi → CoE Review
            // Risiko Rendah/Sedang → Auto-Approve
            if ($project->requiresCoEControl()) {
                $project->update([
                    'status' => ProjectStatus::CoEReview->value,
                    'submitted_at' => now(),
                ]);
            } else {
                $project->update([
                    'status' => ProjectStatus::Approved->value,
                    'submitted_at' => now(),
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            }

            return $project->fresh();
        });
    }

    public function approve(Project $project, int $approverId): Project
    {
        return DB::transaction(function () use ($project, $approverId) {
            $project->update([
                'status' => ProjectStatus::Approved->value,
                'approved_by' => $approverId,
                'approved_at' => now(),
                'rejection_reason' => null, // Clear rejection reason jika ada
            ]);

            return $project->fresh();
        });
    }

    public function reject(Project $project, string $reason): Project
    {
        return DB::transaction(function () use ($project, $reason) {
            $project->update([
                'status' => ProjectStatus::Rejected->value,
                'rejection_reason' => $reason,
            ]);

            return $project->fresh();
        });
    }

    public function stop(Project $project, string $reason): Project
    {
        return DB::transaction(function () use ($project, $reason) {
            $project->update([
                'status' => ProjectStatus::Stopped->value,
                'rejection_reason' => $reason,
            ]);

            return $project->fresh();
        });
    }

    protected function syncModules(Project $project, array $modules): void
    {
        $syncData = [];
        
        foreach ($modules as $moduleData) {
            $moduleId = $moduleData['module_id'];
            $quantity = $moduleData['quantity'] ?? 1;
            $unitPrice = $moduleData['unit_price'] ?? 0;
            
            $syncData[$moduleId] = [
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
                'notes' => $moduleData['notes'] ?? null,
            ];
        }

        $project->modules()->sync($syncData);
    }

    protected function determineCoEControlLevel(string $riskLevel): string
    {
        return match ($riskLevel) {
            RiskLevel::High->value => CoEControlLevel::Full->value,
            RiskLevel::Medium->value => CoEControlLevel::Standard->value,
            default => CoEControlLevel::None->value,
        };
    }
}
