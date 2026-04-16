<?php

namespace App\Policies;

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
        return $user->can('projects_view');
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

        return $project->created_by === $user->id && $project->status === 'draft';
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->can('projects_delete')) {
            return true;
        }

        return $project->created_by === $user->id && $project->status === 'draft';
    }

    public function submit(User $user, Project $project): bool
    {
        return $project->created_by === $user->id && $project->status === 'draft';
    }

    public function approve(User $user, Project $project): bool
    {
        return $user->can('projects_approve');
    }

    public function reject(User $user, Project $project): bool
    {
        return $user->can('projects_approve');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('projects_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('projects_export_pdf');
    }
}
