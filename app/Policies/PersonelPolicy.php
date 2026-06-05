<?php

namespace App\Policies;

use App\Models\Personel;
use App\Models\User;

class PersonelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('personels_view');
    }

    public function view(User $user, Personel $personel): bool
    {
        return $user->can('personels_view');
    }

    public function create(User $user): bool
    {
        return $user->can('personels_create');
    }

    public function update(User $user, Personel $personel): bool
    {
        return $user->can('personels_update');
    }

    public function delete(User $user, Personel $personel): bool
    {
        return $user->can('personels_delete');
    }

    public function toggleStatus(User $user, Personel $personel): bool
    {
        return $user->can('personels_update');
    }

    public function exportExcel(User $user): bool
    {
        return $user->can('personels_export_excel');
    }

    public function exportPdf(User $user): bool
    {
        return $user->can('personels_export_pdf');
    }
}
