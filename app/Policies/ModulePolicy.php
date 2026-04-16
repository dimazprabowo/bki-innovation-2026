<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('modules_view');
    }

    public function view(User $user, Module $module): bool
    {
        return $user->can('modules_view');
    }

    public function create(User $user): bool
    {
        return $user->can('modules_create');
    }

    public function update(User $user, Module $module): bool
    {
        return $user->can('modules_update');
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->can('modules_delete');
    }

    public function toggleStatus(User $user, Module $module): bool
    {
        return $user->can('modules_update');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('modules_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('modules_export_pdf');
    }
}
