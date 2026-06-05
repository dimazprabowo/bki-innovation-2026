<?php

namespace App\Policies;

use App\Models\Competency;
use App\Models\User;

class CompetencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('competencies_view');
    }

    public function view(User $user, Competency $competency): bool
    {
        return $user->can('competencies_view');
    }

    public function create(User $user): bool
    {
        return $user->can('competencies_create');
    }

    public function update(User $user, Competency $competency): bool
    {
        return $user->can('competencies_update');
    }

    public function delete(User $user, Competency $competency): bool
    {
        return $user->can('competencies_delete');
    }

    public function toggleStatus(User $user, Competency $competency): bool
    {
        return $user->can('competencies_update');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('competencies_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('competencies_export_pdf');
    }
}
