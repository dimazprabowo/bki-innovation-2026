<?php

namespace Database\Seeders;

use App\Models\Personel;
use App\Models\Competency;
use Illuminate\Database\Seeder;

class PersonelSeeder extends Seeder
{
    public function run(): void
    {
        $personels = [
            [
                'code' => 'P001',
                'name' => 'Ahmad Wijaya',
                'is_active' => true,
                'competencies' => [
                    ['competency_id' => 1, 'certificate_file' => 'surveyor_cert_001.pdf', 'issuer' => 'BKI Indonesia', 'expired_date' => '2026-12-31'],
                    ['competency_id' => 3, 'certificate_file' => 'ndt_cert_001.pdf', 'issuer' => 'ASNT', 'expired_date' => '2027-06-30'],
                ],
            ],
            [
                'code' => 'P002',
                'name' => 'Budi Santoso',
                'is_active' => true,
                'competencies' => [
                    ['competency_id' => 2, 'certificate_file' => 'welding_cert_002.pdf', 'issuer' => 'IIW', 'expired_date' => '2026-08-15'],
                ],
            ],
            [
                'code' => 'P003',
                'name' => 'Citra Dewi',
                'is_active' => true,
                'competencies' => [
                    ['competency_id' => 4, 'certificate_file' => 'auditor_cert_003.pdf', 'issuer' => 'ISO', 'expired_date' => '2027-03-20'],
                    ['competency_id' => 5, 'certificate_file' => 'naval_cert_003.pdf', 'issuer' => 'BKI Indonesia', 'expired_date' => '2027-12-31'],
                ],
            ],
            [
                'code' => 'P004',
                'name' => 'Dedi Kurniawan',
                'is_active' => true,
                'competencies' => [
                    ['competency_id' => 6, 'certificate_file' => 'structural_cert_004.pdf', 'issuer' => 'BKI Indonesia', 'expired_date' => '2026-10-01'],
                    ['competency_id' => 7, 'certificate_file' => 'diving_cert_004.pdf', 'issuer' => 'ADCI', 'expired_date' => '2027-01-15'],
                ],
            ],
            [
                'code' => 'P005',
                'name' => 'Eka Pratama',
                'is_active' => true,
                'competencies' => [
                    ['competency_id' => 8, 'certificate_file' => 'mechanical_cert_005.pdf', 'issuer' => 'BKI Indonesia', 'expired_date' => '2026-11-30'],
                ],
            ],
        ];

        foreach ($personels as $personelData) {
            $competencies = $personelData['competencies'];
            unset($personelData['competencies']);

            $personel = Personel::create($personelData);

            foreach ($competencies as $competencyData) {
                $personel->competencies()->attach($competencyData['competency_id'], [
                    'certificate_file_path' => 'personel-certificates/' . $competencyData['certificate_file'],
                    'certificate_file_name' => $competencyData['certificate_file'],
                    'certificate_file_size' => 0,
                    'certificate_file_status' => 'completed',
                    'issuer' => $competencyData['issuer'],
                    'expired_date' => $competencyData['expired_date'],
                ]);
            }
        }
    }
}
