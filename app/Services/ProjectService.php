<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\CoEControlLevel;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use App\Models\Project;
use App\Models\ProjectPersonel;
use App\Models\ProjectPeralatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function getFiltered(
        ?string $search = null,
        ?string $status = null,
        ?string $approvalStatus = null,
        ?string $riskLevel = null,
        ?string $priority = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Project::query()
            ->with(['creator', 'approver', 'modules', 'personels'])
            ->search($search)
            ->byStatus($status)
            ->byApprovalStatus($approvalStatus)
            ->byRiskLevel($riskLevel)
            ->when($priority, fn ($q) => $q->where('priority', $priority))
            ->withCount('modules')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function createDraft(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = strtoupper($data['code']);
            $data['created_by'] = auth()->id();
            $data['status'] = ProjectStatus::Draft->value;
            $data['approval_status'] = ApprovalStatus::None->value;

            if (isset($data['risk_level'])) {
                $data['coe_control_level'] = $this->determineCoEControlLevel($data['risk_level']);
            }

            return Project::create($data);
        });
    }

    public function autosave(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            if (isset($data['code'])) {
                $data['code'] = strtoupper($data['code']);
            }

            if (isset($data['risk_level'])) {
                $data['coe_control_level'] = $this->determineCoEControlLevel($data['risk_level']);
            }

            $project->update($data);

            return $project->fresh();
        });
    }

    public function syncModules(Project $project, array $modules): Project
    {
        return DB::transaction(function () use ($project, $modules) {
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

            return $project->fresh(['modules']);
        });
    }

    public function syncPersonels(Project $project, array $personels): Project
    {
        return DB::transaction(function () use ($project, $personels) {
            $project->projectPersonels()->delete();

            foreach ($personels as $personelData) {
                if (!empty($personelData['personel_id'])) {
                    ProjectPersonel::create([
                        'project_id' => $project->id,
                        'module_id' => $personelData['module_id'] ?? null,
                        'module_personel_id' => $personelData['module_personel_id'] ?? null,
                        'personel_id' => $personelData['personel_id'],
                    ]);
                }
            }

            return $project->fresh(['personels']);
        });
    }

    public function syncAdditionalCosts(Project $project, array $costs): Project
    {
        return DB::transaction(function () use ($project, $costs) {
            $project->additionalCosts()->delete();

            foreach ($costs as $costData) {
                if (!empty($costData['name'])) {
                    $project->additionalCosts()->create([
                        'name' => $costData['name'],
                        'amount' => $costData['amount'] ?? 0,
                        'notes' => $costData['notes'] ?? null,
                    ]);
                }
            }

            return $project->fresh(['additionalCosts']);
        });
    }

    public function syncPeralatans(Project $project, array $peralatans): Project
    {
        return DB::transaction(function () use ($project, $peralatans) {
            $project->projectPeralatans()->delete();

            foreach ($peralatans as $data) {
                if (!empty($data['peralatan_id'])) {
                    ProjectPeralatan::create([
                        'project_id' => $project->id,
                        'module_id' => $data['module_id'] ?? null,
                        'module_tool_id' => $data['module_tool_id'] ?? null,
                        'peralatan_id' => $data['peralatan_id'],
                    ]);
                }
            }

            return $project->fresh(['projectPeralatans']);
        });
    }

    public function delete(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            $project->modules()->detach();
            $project->projectPersonels()->delete();
            $project->projectPeralatans()->delete();
            $project->additionalCosts()->delete();
            return $project->delete();
        });
    }

    public function submit(Project $project): Project
    {
        return DB::transaction(function () use ($project) {
            if ($project->requiresCoEControl()) {
                $project->update([
                    'approval_status' => ApprovalStatus::CoEReview->value,
                    'submitted_at' => now(),
                    'rejection_reason' => null,
                ]);
            } else {
                $project->update([
                    'status' => ProjectStatus::Active->value,
                    'approval_status' => ApprovalStatus::Approved->value,
                    'submitted_at' => now(),
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);
            }

            return $project->fresh();
        });
    }

    public function approve(Project $project, int $approverId): Project
    {
        return DB::transaction(function () use ($project, $approverId) {
            $project->update([
                'status' => ProjectStatus::Active->value,
                'approval_status' => ApprovalStatus::Approved->value,
                'approved_by' => $approverId,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            return $project->fresh();
        });
    }

    public function reject(Project $project, string $reason): Project
    {
        return DB::transaction(function () use ($project, $reason) {
            $project->update([
                'approval_status' => ApprovalStatus::Rejected->value,
                'rejection_reason' => $reason,
            ]);

            return $project->fresh();
        });
    }

    public function close(Project $project, string $reason): Project
    {
        return DB::transaction(function () use ($project, $reason) {
            $project->update([
                'status' => ProjectStatus::Closed->value,
                'rejection_reason' => $reason,
            ]);

            return $project->fresh();
        });
    }

    public function updateStatus(Project $project, ProjectStatus $status): Project
    {
        return DB::transaction(function () use ($project, $status) {
            $data = ['status' => $status->value];

            if ($status === ProjectStatus::Completed) {
                $data['actual_end_date'] = now();
            }

            $project->update($data);

            return $project->fresh();
        });
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
