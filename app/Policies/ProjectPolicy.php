<?php

namespace App\Policies;

use App\Enums\ApprovalStatus;
use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('projects_view');
    }

    public function view(User $user, Project $project): bool
    {
        return $user->can('projects_show');
    }

    public function create(User $user): bool
    {
        return $user->can('projects_create');
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->can('projects_update')) {
            return true;
        }

        return $project->created_by === $user->id
            && $project->status === ProjectStatus::Draft;
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->can('projects_delete')) {
            return true;
        }

        return $project->created_by === $user->id
            && $project->status === ProjectStatus::Draft;
    }

    public function submit(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            && $project->status === ProjectStatus::Draft
            && in_array($project->approval_status, [ApprovalStatus::None, ApprovalStatus::Rejected]);
    }

    public function approve(User $user, Project $project): bool
    {
        return $user->can('projects_approve')
            && $project->approval_status === ApprovalStatus::CoEReview;
    }

    public function reject(User $user, Project $project): bool
    {
        return $user->can('projects_approve')
            && $project->approval_status === ApprovalStatus::CoEReview;
    }

    public function close(User $user, Project $project): bool
    {
        return $user->can('projects_approve')
            && in_array($project->status, [ProjectStatus::Active]);
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('projects_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('projects_export_pdf');
    }

    public function manageWorkOrder(User $user, Project $project): bool
    {
        return $user->can('projects_work_order')
            && in_array($project->status, [ProjectStatus::Active]);
    }

    public function manageDeliverables(User $user, Project $project): bool
    {
        return $user->can('projects_deliverables')
            && in_array($project->status, [ProjectStatus::Active]);
    }
}
