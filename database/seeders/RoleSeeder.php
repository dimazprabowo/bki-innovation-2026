<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Super Admin - All permissions (bypass via Gate::before in AuthServiceProvider)
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Admin - Full Access (explicit permissions)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // 3. Admin Data - Input all master data (companies, competencies, personels, modules, peralatan)
        $adminData = Role::firstOrCreate(['name' => 'admin data']);
        $adminData->syncPermissions([
            'dashboard_view',
            // Companies
            'companies_view', 'companies_create', 'companies_update', 'companies_delete',
            'companies_export_excel', 'companies_export_pdf',
            // Competencies
            'competencies_view', 'competencies_create', 'competencies_update', 'competencies_delete',
            'competencies_export_excel', 'competencies_export_pdf',
            // Personels
            'personels_view', 'personels_create', 'personels_update', 'personels_delete',
            'personels_export_excel', 'personels_export_pdf',
            // Modules
            'modules_view', 'modules_show', 'modules_create', 'modules_update', 'modules_delete',
            'modules_export_excel', 'modules_export_pdf',
            // Peralatan
            'peralatan_view', 'peralatan_show', 'peralatan_create', 'peralatan_update', 'peralatan_delete',
            'peralatan_export_excel', 'peralatan_export_pdf',
        ]);

        // 4. Approver - Approve/review peralatan, modules, project CoE
        $approver = Role::firstOrCreate(['name' => 'approver']);
        $approver->syncPermissions([
            'dashboard_view',
            // Peralatan review
            'peralatan_view', 'peralatan_show', 'peralatan_review',
            // Modules review
            'modules_view', 'modules_show', 'modules_review',
            // Project CoE approval
            'projects_view', 'projects_show', 'projects_approve',
        ]);

        // 5. Admin Project - Create and manage projects
        $adminProject = Role::firstOrCreate(['name' => 'admin project']);
        $adminProject->syncPermissions([
            'dashboard_view',
            // Projects
            'projects_view', 'projects_show', 'projects_create', 'projects_update', 'projects_delete',
            'projects_work_order', 'projects_deliverables',
            'projects_export_excel', 'projects_export_pdf',
            // Master Data (view only for reference)
            'modules_view', 'modules_show',
            'peralatan_view', 'peralatan_show',
            'personels_view',
            'competencies_view',
        ]);

        // 6. User - No default permissions
        // Admin must explicitly grant permissions via UI
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([]);
    }
}
