<?php

namespace Database\Seeders;

use App\Enums\RiskLevel;
use App\Models\Module;
use App\Models\Project;
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

        $project1 = Project::create([
            'code' => 'PRJ2026001',
            'name' => 'Inspeksi Kapal Tanker MT. Nusantara Jaya',
            'scope' => 'Inspeksi komprehensif kapal tanker 50,000 DWT untuk renewal sertifikat',
            'method' => 'Survey menyeluruh sesuai standar IMO dan class requirements',
            'duration' => '3 minggu',
            'deliverable' => 'Sertifikat class renewal dan laporan kondisi kapal',
            'risk_level' => RiskLevel::Medium->value,
            'coe_control_level' => 'standard',
            'status' => 'approved',
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
            'submitted_at' => now()->subDays(10),
            'approved_at' => now()->subDays(5),
            'notes' => 'Project prioritas untuk klien existing',
        ]);

        $mod1 = $modules->where('code', 'MOD001')->first();
        $mod2 = $modules->where('code', 'MOD002')->first();
        
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

        $project2 = Project::create([
            'code' => 'PRJ2026002',
            'name' => 'Inspeksi Platform Offshore Natuna Alpha',
            'scope' => 'Inspeksi struktur platform lepas pantai termasuk underwater inspection dan integrity assessment',
            'method' => 'ROV inspection, NDT, structural analysis',
            'duration' => '4 bulan',
            'deliverable' => 'Comprehensive integrity report dan rekomendasi maintenance',
            'risk_level' => RiskLevel::High->value,
            'coe_control_level' => 'full',
            'status' => 'coe_review',
            'created_by' => $admin->id,
            'submitted_at' => now()->subDays(3),
            'notes' => 'High-risk project memerlukan full CoE oversight',
        ]);

        $mod4 = $modules->where('code', 'MOD004')->first();
        if ($mod4) {
            $project2->modules()->attach($mod4->id, [
                'quantity' => 1,
                'unit_price' => 250000000,
                'subtotal' => 250000000,
                'notes' => 'Inspeksi platform utama',
            ]);
        }

        $project3 = Project::create([
            'code' => 'PRJ2026003',
            'name' => 'Konsultasi Desain Kapal Ro-Ro 5000 GT',
            'scope' => 'Review dan approval desain kapal Ro-Ro baru untuk rute domestik',
            'method' => 'Technical review, calculation verification, regulatory compliance check',
            'duration' => '2.5 bulan',
            'deliverable' => 'Design approval certificate dan technical recommendations',
            'risk_level' => RiskLevel::High->value,
            'coe_control_level' => 'enhanced',
            'status' => 'draft',
            'created_by' => $admin->id,
            'notes' => 'Masih dalam tahap persiapan dokumen',
        ]);

        $mod6 = $modules->where('code', 'MOD006')->first();
        $mod3 = $modules->where('code', 'MOD003')->first();
        
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

        // $this->command->info('Sample projects created successfully!');
    }
}
