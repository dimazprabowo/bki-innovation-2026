<?php

namespace App\Policies;

use App\Models\Peralatan;
use App\Models\User;

class PeralatanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('peralatan_view');
    }

    public function view(User $user, Peralatan $peralatan): bool
    {
        return $user->can('peralatan_view');
    }

    public function create(User $user): bool
    {
        return $user->can('peralatan_create');
    }

    public function update(User $user, Peralatan $peralatan): bool
    {
        return $user->can('peralatan_update');
    }

    public function delete(User $user, Peralatan $peralatan): bool
    {
        return $user->can('peralatan_delete');
    }

    public function toggleStatus(User $user, Peralatan $peralatan): bool
    {
        return $user->can('peralatan_update');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('peralatan_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('peralatan_export_pdf');
    }
}
