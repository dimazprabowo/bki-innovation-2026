<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\CoEControlLevel;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use App\Models\Module;
use App\Models\ModuleTool;
use App\Models\Peralatan;
use App\Models\Personel;
use App\Models\Project;
use App\Models\ProjectPersonel;
use App\Models\ProjectPeralatan;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@app.com')->first();

        if (!$admin) {
            $this->command->warn('Admin user not found. Skipping ProjectSeeder.');
            return;
        }

        $modules = Module::all();

        if ($modules->isEmpty()) {
            $this->command->warn('No modules found. Please run ModuleSeeder first.');
            return;
        }

        $personels = Personel::active()->get();

        $mod1 = $modules->where('code', 'MOD001')->first();
        $mod2 = $modules->where('code', 'MOD002')->first();
        $mod3 = $modules->where('code', 'MOD003')->first();
        $mod4 = $modules->where('code', 'MOD004')->first();
        $mod5 = $modules->where('code', 'MOD005')->first();
        $mod6 = $modules->where('code', 'MOD006')->first();

        // ========== PROJECT 1: Active, Approved ==========
        $project1 = Project::create([
            'code' => 'PRJ2026001',
            'name' => 'Inspeksi Kapal Tanker MT. Nusantara Jaya',
            'description' => 'Inspeksi komprehensif kapal tanker 50,000 DWT untuk renewal sertifikat',
            'priority' => ProjectPriority::High->value,
            'risk_level' => RiskLevel::Medium->value,
            'coe_control_level' => CoEControlLevel::Standard->value,
            'status' => ProjectStatus::Active->value,
            'approval_status' => ApprovalStatus::Approved->value,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(28),
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
            'submitted_at' => now()->subDays(10),
            'approved_at' => now()->subDays(5),
            'notes' => 'Project prioritas untuk klien existing',
        ]);

        if ($mod1) {
            $project1->modules()->attach($mod1->id, [
                'quantity' => 1,
                'unit_price' => 50000000,
                'subtotal' => 50000000,
                'notes' => 'Inspeksi utama kapal',
            ]);
        }

        if ($mod2) {
            $project1->modules()->attach($mod2->id, [
                'quantity' => 2,
                'unit_price' => 15000000,
                'subtotal' => 30000000,
                'notes' => 'Sertifikasi welding untuk repair works',
            ]);
        }

        if ($mod5) {
            $project1->modules()->attach($mod5->id, [
                'quantity' => 1,
                'unit_price' => 60000000,
                'subtotal' => 60000000,
                'notes' => 'Inspeksi mesin utama dan bantu',
            ]);
        }

        $project1->additionalCosts()->createMany([
            ['name' => 'Transportasi', 'amount' => 5000000, 'notes' => 'Mobilisasi demobilisasi'],
            ['name' => 'Akomodasi', 'amount' => 3500000, 'notes' => 'Tim inspeksi 3 hari'],
        ]);

        // Assign personels to project1
        $this->assignPersonels($project1, $mod1, $personels, [0, 1]);
        $this->assignPersonels($project1, $mod2, $personels, [1]);
        $this->assignPersonels($project1, $mod5, $personels, [4]);

        // Assign peralatans to project1
        $this->assignPeralatans($project1, $mod1);
        $this->assignPeralatans($project1, $mod2);
        $this->assignPeralatans($project1, $mod5);

        // ========== PROJECT 2: Draft, CoE Review ==========
        $project2 = Project::create([
            'code' => 'PRJ2026002',
            'name' => 'Inspeksi Platform Offshore Natuna Alpha',
            'description' => 'Inspeksi struktur platform lepas pantai termasuk underwater inspection dan integrity assessment',
            'priority' => ProjectPriority::Critical->value,
            'risk_level' => RiskLevel::High->value,
            'coe_control_level' => CoEControlLevel::Full->value,
            'status' => ProjectStatus::Draft->value,
            'approval_status' => ApprovalStatus::CoEReview->value,
            'start_date' => now()->addDays(14),
            'end_date' => now()->addMonths(4),
            'created_by' => $admin->id,
            'submitted_at' => now()->subDays(3),
            'notes' => 'High-risk project memerlukan full CoE oversight',
        ]);

        if ($mod4) {
            $project2->modules()->attach($mod4->id, [
                'quantity' => 1,
                'unit_price' => 250000000,
                'subtotal' => 250000000,
                'notes' => 'Inspeksi platform utama',
            ]);
        }

        if ($mod1) {
            $project2->modules()->attach($mod1->id, [
                'quantity' => 1,
                'unit_price' => 50000000,
                'subtotal' => 50000000,
                'notes' => 'Inspeksi struktur kapal support',
            ]);
        }

        $project2->additionalCosts()->create([
            'name' => 'Logistik Offshore',
            'amount' => 75000000,
            'notes' => 'Vessel charter dan helikopter',
        ]);

        $project2->additionalCosts()->create([
            'name' => 'Akomodasi Offshore',
            'amount' => 25000000,
            'notes' => 'Akomodasi tim inspeksi di platform',
        ]);

        // Assign personels to project2
        $this->assignPersonels($project2, $mod4, $personels, [3, 0]);
        $this->assignPersonels($project2, $mod1, $personels, [0]);

        // Assign peralatans to project2
        $this->assignPeralatans($project2, $mod4);
        $this->assignPeralatans($project2, $mod1);

        // ========== PROJECT 3: Draft, Not yet submitted ==========
        $project3 = Project::create([
            'code' => 'PRJ2026003',
            'name' => 'Konsultasi Desain Kapal Ro-Ro 5000 GT',
            'description' => 'Review dan approval desain kapal Ro-Ro baru untuk rute domestik',
            'priority' => ProjectPriority::Medium->value,
            'risk_level' => RiskLevel::High->value,
            'coe_control_level' => CoEControlLevel::Enhanced->value,
            'status' => ProjectStatus::Draft->value,
            'approval_status' => ApprovalStatus::None->value,
            'start_date' => now()->addMonths(1),
            'end_date' => now()->addMonths(3)->addWeeks(2),
            'created_by' => $admin->id,
            'notes' => 'Masih dalam tahap persiapan dokumen',
        ]);

        if ($mod6) {
            $project3->modules()->attach($mod6->id, [
                'quantity' => 1,
                'unit_price' => 150000000,
                'subtotal' => 150000000,
                'notes' => 'Konsultasi desain utama',
            ]);
        }

        if ($mod3) {
            $project3->modules()->attach($mod3->id, [
                'quantity' => 1,
                'unit_price' => 75000000,
                'subtotal' => 75000000,
                'notes' => 'Audit sistem manajemen galangan',
            ]);
        }

        $project3->additionalCosts()->create([
            'name' => 'Software Licensing',
            'amount' => 15000000,
            'notes' => 'Lisensi software analisis struktur',
        ]);

        // Assign personels to project3
        $this->assignPersonels($project3, $mod6, $personels, [2, 3]);
        $this->assignPersonels($project3, $mod3, $personels, [2]);

        // Assign peralatans to project3
        $this->assignPeralatans($project3, $mod6);
        $this->assignPeralatans($project3, $mod3);
    }

    private function assignPersonels($project, $module, $personels, array $indices): void
    {
        if (!$module) {
            return;
        }

        $modulePersonels = $module->personels()->get();

        foreach ($indices as $i => $personelIndex) {
            $personel = $personels->get($personelIndex);
            $modulePersonel = $modulePersonels->get($i);

            if (!$personel || !$modulePersonel) {
                continue;
            }

            ProjectPersonel::create([
                'project_id' => $project->id,
                'module_id' => $module->id,
                'module_personel_id' => $modulePersonel->id,
                'personel_id' => $personel->id,
            ]);
        }
    }

    private function assignPeralatans($project, $module): void
    {
        if (!$module) {
            return;
        }

        $tools = $module->tools()->get();

        foreach ($tools as $tool) {
            if (!$tool->peralatan_id) {
                continue;
            }

            $peralatan = Peralatan::find($tool->peralatan_id);

            if (!$peralatan || !$peralatan->is_active) {
                continue;
            }

            ProjectPeralatan::create([
                'project_id' => $project->id,
                'module_id' => $module->id,
                'module_tool_id' => $tool->id,
                'peralatan_id' => $peralatan->id,
            ]);
        }
    }
}
