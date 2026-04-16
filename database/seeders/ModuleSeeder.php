<?php

namespace Database\Seeders;

use App\Enums\CoEControlLevel;
use App\Enums\RiskLevel;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'code' => 'MOD001',
                'name' => 'Inspeksi Kapal Baru',
                'scope' => 'Inspeksi menyeluruh untuk kapal baru meliputi struktur, mesin, dan sistem keselamatan',
                'method' => 'Survey fisik dan dokumentasi',
                'resource' => '2 Surveyor Senior, 1 Asisten Surveyor',
                'duration' => '2 minggu',
                'deliverable' => 'Laporan inspeksi lengkap dan sertifikat kelayakan',
                'risk_level' => RiskLevel::Medium->value,
                'pricing_baseline' => 50000000,
                'coe_control_level' => CoEControlLevel::Standard->value,
                'is_active' => true,
            ],
            [
                'code' => 'MOD002',
                'name' => 'Sertifikasi Welding',
                'scope' => 'Sertifikasi prosedur dan operator welding untuk konstruksi maritim',
                'method' => 'Uji kualifikasi dan dokumentasi prosedur',
                'resource' => '1 Welding Inspector, 1 NDT Technician',
                'duration' => '1 minggu',
                'deliverable' => 'Sertifikat welding procedure dan welder qualification',
                'risk_level' => RiskLevel::Low->value,
                'pricing_baseline' => 15000000,
                'coe_control_level' => CoEControlLevel::None->value,
                'is_active' => true,
            ],
            [
                'code' => 'MOD003',
                'name' => 'Audit Sistem Manajemen Mutu',
                'scope' => 'Audit ISO 9001 untuk galangan kapal dan industri maritim',
                'method' => 'Audit dokumen dan implementasi sistem',
                'resource' => '2 Lead Auditor, 1 Technical Expert',
                'duration' => '1 bulan',
                'deliverable' => 'Laporan audit dan rekomendasi perbaikan',
                'risk_level' => RiskLevel::Medium->value,
                'pricing_baseline' => 75000000,
                'coe_control_level' => CoEControlLevel::Standard->value,
                'is_active' => true,
            ],
            [
                'code' => 'MOD004',
                'name' => 'Inspeksi Platform Lepas Pantai',
                'scope' => 'Inspeksi struktur dan fasilitas platform minyak & gas lepas pantai',
                'method' => 'Underwater inspection, NDT, dan structural assessment',
                'resource' => '3 Surveyor Senior, 2 Diving Inspector, 1 NDT Level III',
                'duration' => '3 bulan',
                'deliverable' => 'Comprehensive inspection report dan integrity assessment',
                'risk_level' => RiskLevel::High->value,
                'pricing_baseline' => 250000000,
                'coe_control_level' => CoEControlLevel::Full->value,
                'is_active' => true,
                'notes' => 'Memerlukan koordinasi dengan operator platform dan persetujuan CoE',
            ],
            [
                'code' => 'MOD005',
                'name' => 'Sertifikasi Alat Angkat',
                'scope' => 'Inspeksi dan sertifikasi crane, hoist, dan lifting equipment',
                'method' => 'Load test dan visual inspection',
                'resource' => '1 Mechanical Inspector, 1 Technician',
                'duration' => '3 hari',
                'deliverable' => 'Sertifikat kelayakan alat angkat',
                'risk_level' => RiskLevel::Low->value,
                'pricing_baseline' => 8000000,
                'coe_control_level' => CoEControlLevel::None->value,
                'is_active' => true,
            ],
            [
                'code' => 'MOD006',
                'name' => 'Konsultasi Desain Kapal',
                'scope' => 'Review dan konsultasi desain kapal baru sesuai regulasi internasional',
                'method' => 'Technical review dan calculation verification',
                'resource' => '2 Naval Architect, 1 Structural Engineer',
                'duration' => '2 bulan',
                'deliverable' => 'Design approval dan technical report',
                'risk_level' => RiskLevel::High->value,
                'pricing_baseline' => 150000000,
                'coe_control_level' => CoEControlLevel::Enhanced->value,
                'is_active' => true,
                'notes' => 'Proyek kompleks memerlukan review CoE untuk approval',
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
