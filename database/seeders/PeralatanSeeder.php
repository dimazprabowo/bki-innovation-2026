<?php

namespace Database\Seeders;

use App\Models\Peralatan;
use App\Models\PeralatanEvidence;
use Illuminate\Database\Seeder;

class PeralatanSeeder extends Seeder
{
    public function run(): void
    {
        $peralatanData = [
            [
                'code' => 'EQ001',
                'name' => 'Ultrasonic Testing Equipment',
                'description' => 'Alat untuk deteksi cacat internal pada material menggunakan gelombang ultrasonik',
                'location' => 'Workshop A',
                'calibration_status' => 'calibrated',
                'calibration_expired_date' => '2026-12-31',
                'condition' => 'suitable',
                'ownership_status' => 'owned',
                'is_active' => true,
                'evidences' => [
                    ['name' => 'Sertifikat Kalibrasi', 'file_name' => 'kalibrasi_eq001.pdf'],
                    ['name' => 'Manual Book', 'file_name' => 'manual_eq001.pdf'],
                ],
            ],
            [
                'code' => 'EQ002',
                'name' => 'Magnetic Particle Testing Kit',
                'description' => 'Set alat untuk inspeksi magnetic particle testing',
                'location' => 'Workshop B',
                'calibration_status' => 'calibrated',
                'calibration_expired_date' => '2027-06-30',
                'condition' => 'suitable',
                'ownership_status' => 'owned',
                'is_active' => true,
                'evidences' => [
                    ['name' => 'Sertifikat Kalibrasi', 'file_name' => 'kalibrasi_eq002.pdf'],
                ],
            ],
            [
                'code' => 'EQ003',
                'name' => 'Liquid Penetrant Testing Set',
                'description' => 'Set alat untuk liquid penetrant testing',
                'location' => 'Workshop A',
                'calibration_status' => 'pending',
                'calibration_expired_date' => null,
                'condition' => 'suitable',
                'ownership_status' => 'rented',
                'is_active' => true,
                'evidences' => [
                    ['name' => 'Kontrak Sewa', 'file_name' => 'sewa_eq003.pdf'],
                ],
            ],
            [
                'code' => 'EQ004',
                'name' => 'Radiographic Testing Equipment',
                'description' => 'Alat radiografi untuk inspeksi internal material',
                'location' => 'Workshop C',
                'calibration_status' => 'expired',
                'calibration_expired_date' => '2024-12-31',
                'condition' => 'not_suitable',
                'ownership_status' => 'owned',
                'is_active' => false,
                'evidences' => [
                    ['name' => 'Sertifikat Kalibrasi Expired', 'file_name' => 'kalibrasi_eq004.pdf'],
                ],
            ],
            [
                'code' => 'EQ005',
                'name' => 'Visual Testing Equipment',
                'description' => 'Alat untuk visual inspection termasuk borescope dan lampu',
                'location' => 'Workshop B',
                'calibration_status' => 'not_required',
                'calibration_expired_date' => null,
                'condition' => 'suitable',
                'ownership_status' => 'owned',
                'is_active' => true,
                'evidences' => [
                    ['name' => 'Spesifikasi Alat', 'file_name' => 'spesifikasi_eq005.pdf'],
                ],
            ],
        ];

        foreach ($peralatanData as $data) {
            $evidences = $data['evidences'];
            unset($data['evidences']);

            $peralatan = Peralatan::create($data);

            foreach ($evidences as $evidenceData) {
                $peralatan->evidences()->create([
                    'name' => $evidenceData['name'],
                    'file_path' => 'peralatan-evidence/' . $evidenceData['file_name'],
                    'file_name' => $evidenceData['file_name'],
                    'file_size' => 0,
                ]);
            }
        }
    }
}
