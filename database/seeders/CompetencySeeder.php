<?php

namespace Database\Seeders;

use App\Models\Competency;
use Illuminate\Database\Seeder;

class CompetencySeeder extends Seeder
{
    public function run(): void
    {
        $competencies = [
            [
                'code' => 'KOM001',
                'name' => 'Surveyor Kapal',
                'level' => 3,
                'description' => 'Kompetensi melakukan inspeksi kapal baru dan kapal yang sudah beroperasi sesuai standar internasional',
                'is_active' => true,
            ],
            [
                'code' => 'KOM002',
                'name' => 'Welding Inspector',
                'level' => 2,
                'description' => 'Kompetensi inspeksi welding dan sertifikasi prosedur welding untuk konstruksi maritim',
                'is_active' => true,
            ],
            [
                'code' => 'KOM003',
                'name' => 'NDT Technician',
                'level' => 3,
                'description' => 'Kompetensi Non-Destructive Testing termasuk ultrasonic, radiographic, dan magnetic particle inspection',
                'is_active' => true,
            ],
            [
                'code' => 'KOM004',
                'name' => 'Lead Auditor',
                'level' => 3,
                'description' => 'Kompetensi memimpin audit sistem manajemen mutu ISO 9001 dan standar lainnya',
                'is_active' => true,
            ],
            [
                'code' => 'KOM005',
                'name' => 'Naval Architect',
                'level' => 3,
                'description' => 'Kompetensi desain kapal dan perhitungan struktur sesuai regulasi internasional',
                'is_active' => true,
            ],
            [
                'code' => 'KOM006',
                'name' => 'Structural Engineer',
                'level' => 3,
                'description' => 'Kompetensi analisis dan desain struktur kapal dan platform lepas pantai',
                'is_active' => true,
            ],
            [
                'code' => 'KOM007',
                'name' => 'Diving Inspector',
                'level' => 3,
                'description' => 'Kompetensi inspeksi underwater untuk struktur dan fasilitas lepas pantai',
                'is_active' => true,
            ],
            [
                'code' => 'KOM008',
                'name' => 'Mechanical Inspector',
                'level' => 2,
                'description' => 'Kompetensi inspeksi peralatan mekanikal termasuk alat angkat dan mesin kapal',
                'is_active' => true,
            ],
            [
                'code' => 'KOM009',
                'name' => 'Electrical Engineer',
                'level' => 3,
                'description' => 'Kompetensi sistem kelistrikan kapal dan instalasi platform lepas pantai',
                'is_active' => true,
            ],
            [
                'code' => 'KOM010',
                'name' => 'Safety Officer',
                'level' => 2,
                'description' => 'Kompetensi manajemen K3 dan keselamatan kerja di lingkungan galangan dan lepas pantai',
                'is_active' => true,
            ],
        ];

        foreach ($competencies as $competency) {
            Competency::create($competency);
        }
    }
}
