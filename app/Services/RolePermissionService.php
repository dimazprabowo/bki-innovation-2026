<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionService
{
    public function getAllRolesWithPermissions(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function getRolePermissions(int $roleId): array
    {
        $role = Role::with('permissions')->find($roleId);

        return $role ? $role->permissions->pluck('name')->toArray() : [];
    }

    public function createRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function updateRole(Role $role, string $name, array $permissions = []): Role
    {
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
    }

    public function togglePermission(Role $role, string $permission): string
    {
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);

            return 'revoked';
        }

        $role->givePermissionTo($permission);

        return 'granted';
    }

    public function roleHasUsers(Role $role): bool
    {
        return $role->users()->count() > 0;
    }

    /**
     * Build permission groups dynamically from database.
     * Maps permissions to groups based on naming convention.
     * Any unmatched permissions go to 'Lainnya' group.
     *
     * Returns: ['Group Name' => [['name' => 'permission_key', 'label' => 'Human Label'], ...]]
     */
    public function buildPermissionGroups(): array
    {
        $groupMapping = [
            'Dashboard' => [
                ['name' => 'dashboard_view', 'label' => 'Lihat Dashboard'],
                ['name' => 'manage_own_company', 'label' => 'Kelola Perusahaan Sendiri'],
            ],
            'Perusahaan' => [
                ['name' => 'companies_view',         'label' => 'Lihat Perusahaan'],
                ['name' => 'companies_create',       'label' => 'Tambah Perusahaan'],
                ['name' => 'companies_update',       'label' => 'Edit Perusahaan'],
                ['name' => 'companies_delete',       'label' => 'Hapus Perusahaan'],
                ['name' => 'companies_export_excel', 'label' => 'Export Excel Perusahaan'],
                ['name' => 'companies_export_pdf',   'label' => 'Export PDF Perusahaan'],
            ],
            'Konfigurasi System' => [
                ['name' => 'configuration_view',         'label' => 'Lihat Konfigurasi'],
                ['name' => 'configuration_update',       'label' => 'Edit Konfigurasi'],
                ['name' => 'configuration_export_excel', 'label' => 'Export Excel Konfigurasi'],
                ['name' => 'configuration_export_pdf',   'label' => 'Export PDF Konfigurasi'],
            ],
            'Manajemen User' => [
                ['name' => 'users_view',         'label' => 'Lihat User'],
                ['name' => 'users_create',       'label' => 'Tambah User'],
                ['name' => 'users_update',       'label' => 'Edit User'],
                ['name' => 'users_delete',       'label' => 'Hapus User'],
                ['name' => 'users_export_excel', 'label' => 'Export Excel User'],
                ['name' => 'users_export_pdf',   'label' => 'Export PDF User'],
            ],
            'Roles & Permissions' => [
                ['name' => 'roles_view',         'label' => 'Lihat Roles'],
                ['name' => 'roles_create',       'label' => 'Tambah Role'],
                ['name' => 'roles_update',       'label' => 'Edit Role'],
                ['name' => 'roles_delete',       'label' => 'Hapus Role'],
                ['name' => 'roles_export_excel', 'label' => 'Export Excel Roles'],
                ['name' => 'roles_export_pdf',   'label' => 'Export PDF Roles'],
            ],
            'Notifikasi' => [
                ['name' => 'notifications_view', 'label' => 'Lihat Notifikasi'],
                ['name' => 'notifications_send', 'label' => 'Kirim Notifikasi'],
            ],
            'Chat' => [
                ['name' => 'chat_view',   'label' => 'Lihat Chat'],
                ['name' => 'chat_create', 'label' => 'Buat Chat'],
                ['name' => 'chat_delete', 'label' => 'Hapus Chat'],
            ],
            'Modul Pengadaan' => [
                ['name' => 'modules_view',         'label' => 'Lihat Modul'],
                ['name' => 'modules_create',       'label' => 'Tambah Modul'],
                ['name' => 'modules_update',       'label' => 'Edit Modul'],
                ['name' => 'modules_delete',       'label' => 'Hapus Modul'],
                ['name' => 'modules_export_excel', 'label' => 'Export Excel Modul'],
                ['name' => 'modules_export_pdf',   'label' => 'Export PDF Modul'],
            ],
            'Project Pengadaan' => [
                ['name' => 'projects_view',         'label' => 'Lihat Project'],
                ['name' => 'projects_create',       'label' => 'Buat Project'],
                ['name' => 'projects_update',       'label' => 'Edit Project'],
                ['name' => 'projects_delete',       'label' => 'Hapus Project'],
                ['name' => 'projects_approve',      'label' => 'Approve Project'],
                ['name' => 'projects_export_excel', 'label' => 'Export Excel Project'],
                ['name' => 'projects_export_pdf',   'label' => 'Export PDF Project'],
            ],
        ];

        $allPermissions = Permission::pluck('name')->toArray();
        $mapped = collect($groupMapping)->flatten(1)->pluck('name')->toArray();
        $unmapped = array_diff($allPermissions, $mapped);

        $groups = $groupMapping;

        if (!empty($unmapped)) {
            $groups['Lainnya'] = array_values(array_map(
                fn($p) => ['name' => $p, 'label' => ucwords(str_replace('_', ' ', $p))],
                $unmapped
            ));
        }

        return $groups;
    }
}
