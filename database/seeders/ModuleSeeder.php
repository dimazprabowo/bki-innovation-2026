<?php

namespace Database\Seeders;

use App\Enums\RiskLevel;
use App\Models\Module;
use App\Models\Competency;
use App\Models\Peralatan;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $competencies = Competency::all();
        $peralatans = Peralatan::all();

        $modules = [
            [
                'code' => 'MOD001',
                'name' => 'Inspeksi Kapal Baru',
                'duration' => '14',
                'risk_level' => RiskLevel::Medium->value,
                'pricing_baseline' => 50000000,
                'is_active' => true,
                'work_order_items' => [
                    [
                        'order' => 1,
                        'name' => 'Pemeriksaan Struktur',
                        'description' => 'Inspeksi hull dan superstructure',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Visual Inspection', 'description' => 'Pemeriksaan visual kondisi plat', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Thickness Measurement', 'description' => 'Pengukuran ketebalan plat', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                    [
                        'order' => 2,
                        'name' => 'Pemeriksaan Mesin',
                        'description' => 'Inspeksi sistem propulsi dan auxiliary',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Main Engine', 'description' => 'Inspeksi mesin utama', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Generator', 'description' => 'Inspeksi generator set', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                ],
                'work_order_references' => [
                    ['document_name' => 'Rules for Classification of Ships', 'document_id' => 'BKI-RCS-2024'],
                    ['document_name' => 'IACS UR', 'document_id' => 'IACS-UR-2024'],
                ],
                'personels' => [
                    ['position_name' => 'Surveyor Senior', 'quantity' => 2, 'nature' => 'mandatory', 'competencies' => $competencies->take(2)->pluck('id')->toArray()],
                    ['position_name' => 'Asisten Surveyor', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->take(1)->pluck('id')->toArray()],
                ],
                'tools' => [
                    ['peralatan_id' => $peralatans->where('name', 'Ultrasonic Testing Equipment')->first()?->id, 'requires_calibration' => true, 'quantity' => 2],
                    ['peralatan_id' => $peralatans->where('name', 'Visual Testing Equipment')->first()?->id, 'requires_calibration' => false, 'quantity' => 1],
                ],
                'deliverables' => [
                    ['order' => 1, 'name' => 'Laporan Inspeksi', 'description' => 'Laporan lengkap inspeksi', 'nature' => 'mandatory', 'is_active' => true],
                    ['order' => 2, 'name' => 'Sertifikat Kelayakan', 'description' => 'Sertifikat kelayakan berlayar', 'nature' => 'mandatory', 'is_active' => true],
                ],
            ],
            [
                'code' => 'MOD002',
                'name' => 'Sertifikasi Welding',
                'duration' => '7',
                'risk_level' => RiskLevel::Low->value,
                'pricing_baseline' => 15000000,
                'is_active' => true,
                'work_order_items' => [
                    [
                        'order' => 1,
                        'name' => 'WPS Review',
                        'description' => 'Review Welding Procedure Specification',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [],
                    ],
                    [
                        'order' => 2,
                        'name' => 'Welder Qualification Test',
                        'description' => 'Uji kualifikasi welder',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [],
                    ],
                ],
                'work_order_references' => [
                    ['document_name' => 'AWS D1.1', 'document_id' => 'AWS-D1.1'],
                ],
                'personels' => [
                    ['position_name' => 'Welding Inspector', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->take(1)->pluck('id')->toArray()],
                ],
                'tools' => [
                    ['peralatan_id' => $peralatans->where('name', 'Magnetic Particle Testing Kit')->first()?->id, 'requires_calibration' => true, 'quantity' => 1],
                ],
                'deliverables' => [
                    ['order' => 1, 'name' => 'WPS Certificate', 'description' => 'Sertifikat prosedur welding', 'nature' => 'mandatory', 'is_active' => true],
                    ['order' => 2, 'name' => 'Welder Qualification', 'description' => 'Sertifikat kualifikasi welder', 'nature' => 'mandatory', 'is_active' => true],
                ],
            ],
            [
                'code' => 'MOD003',
                'name' => 'Audit Sistem Manajemen Mutu',
                'duration' => '5',
                'risk_level' => RiskLevel::Low->value,
                'pricing_baseline' => 75000000,
                'is_active' => true,
                'work_order_items' => [
                    [
                        'order' => 1,
                        'name' => 'Document Review',
                        'description' => 'Review dokumen sistem manajemen mutu',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Quality Manual', 'description' => 'Review manual mutu', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Procedures', 'description' => 'Review prosedur operasional', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                    [
                        'order' => 2,
                        'name' => 'Site Audit',
                        'description' => 'Audit lapangan sistem manajemen mutu',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Interview', 'description' => 'Wawancara staf terkait', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Process Observation', 'description' => 'Observasi proses kerja', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                ],
                'work_order_references' => [
                    ['document_name' => 'ISO 9001:2015', 'document_id' => 'ISO-9001-2015'],
                ],
                'personels' => [
                    ['position_name' => 'Lead Auditor', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->where('code', 'KOM004')->pluck('id')->toArray()],
                    ['position_name' => 'Auditor Asisten', 'quantity' => 1, 'nature' => 'optional', 'competencies' => $competencies->where('code', 'KOM004')->pluck('id')->toArray()],
                ],
                'tools' => [
                    ['peralatan_id' => $peralatans->where('name', 'Visual Testing Equipment')->first()?->id, 'requires_calibration' => false, 'quantity' => 1],
                ],
                'deliverables' => [
                    ['order' => 1, 'name' => 'Audit Report', 'description' => 'Laporan audit lengkap', 'nature' => 'mandatory', 'is_active' => true],
                    ['order' => 2, 'name' => 'Corrective Action Plan', 'description' => 'Rencana tindakan korektif', 'nature' => 'mandatory', 'is_active' => true],
                ],
            ],
            [
                'code' => 'MOD004',
                'name' => 'Inspeksi Platform Offshore',
                'duration' => '30',
                'risk_level' => RiskLevel::High->value,
                'pricing_baseline' => 250000000,
                'is_active' => true,
                'work_order_items' => [
                    [
                        'order' => 1,
                        'name' => 'Above Water Inspection',
                        'description' => 'Inspeksi struktur di atas air',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Visual Inspection', 'description' => 'Pemeriksaan visual struktur deck', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Coating Inspection', 'description' => 'Inspeksi kondisi coating', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                    [
                        'order' => 2,
                        'name' => 'Underwater Inspection',
                        'description' => 'Inspeksi struktur bawah air',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'CP Survey', 'description' => 'Survey cathodic protection', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'FMD Inspection', 'description' => 'Flooded member detection', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                ],
                'work_order_references' => [
                    ['document_name' => 'API RP 2SIM', 'document_id' => 'API-RP-2SIM'],
                    ['document_name' => 'ISO 19902', 'document_id' => 'ISO-19902'],
                ],
                'personels' => [
                    ['position_name' => 'Lead Inspector', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->whereIn('code', ['KOM006', 'KOM007'])->pluck('id')->toArray()],
                    ['position_name' => 'Diving Inspector', 'quantity' => 2, 'nature' => 'mandatory', 'competencies' => $competencies->where('code', 'KOM007')->pluck('id')->toArray()],
                    ['position_name' => 'Structural Engineer', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->where('code', 'KOM006')->pluck('id')->toArray()],
                ],
                'tools' => [
                    ['peralatan_id' => $peralatans->where('name', 'Ultrasonic Testing Equipment')->first()?->id, 'requires_calibration' => true, 'quantity' => 2],
                    ['peralatan_id' => $peralatans->where('name', 'Magnetic Particle Testing Kit')->first()?->id, 'requires_calibration' => true, 'quantity' => 1],
                    ['peralatan_id' => $peralatans->where('name', 'Liquid Penetrant Testing Set')->first()?->id, 'requires_calibration' => false, 'quantity' => 2],
                ],
                'deliverables' => [
                    ['order' => 1, 'name' => 'Inspection Report', 'description' => 'Laporan inspeksi lengkap', 'nature' => 'mandatory', 'is_active' => true],
                    ['order' => 2, 'name' => 'Integrity Assessment', 'description' => 'Penilaian integritas struktur', 'nature' => 'mandatory', 'is_active' => true],
                    ['order' => 3, 'name' => 'Recommendation Report', 'description' => 'Rekomendasi tindakan lanjutan', 'nature' => 'optional', 'is_active' => true],
                ],
            ],
            [
                'code' => 'MOD005',
                'name' => 'Inspeksi Mesin Kapal',
                'duration' => '10',
                'risk_level' => RiskLevel::Medium->value,
                'pricing_baseline' => 60000000,
                'is_active' => true,
                'work_order_items' => [
                    [
                        'order' => 1,
                        'name' => 'Main Engine Inspection',
                        'description' => 'Inspeksi mesin utama',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Crankshaft', 'description' => 'Inspeksi crankshaft dan bearing', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Fuel System', 'description' => 'Inspeksi sistem bahan bakar', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                    [
                        'order' => 2,
                        'name' => 'Auxiliary Engine Inspection',
                        'description' => 'Inspeksi mesin bantu dan generator',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Generator Set', 'description' => 'Inspeksi generator set', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Cooling System', 'description' => 'Inspeksi sistem pendingin', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                ],
                'work_order_references' => [
                    ['document_name' => 'Engine Manufacturer Manual', 'document_id' => 'EMM-2024'],
                ],
                'personels' => [
                    ['position_name' => 'Mechanical Inspector', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->where('code', 'KOM008')->pluck('id')->toArray()],
                    ['position_name' => 'Electrical Engineer', 'quantity' => 1, 'nature' => 'optional', 'competencies' => $competencies->where('code', 'KOM009')->pluck('id')->toArray()],
                ],
                'tools' => [
                    ['peralatan_id' => $peralatans->where('name', 'Ultrasonic Testing Equipment')->first()?->id, 'requires_calibration' => true, 'quantity' => 1],
                    ['peralatan_id' => $peralatans->where('name', 'Visual Testing Equipment')->first()?->id, 'requires_calibration' => false, 'quantity' => 1],
                ],
                'deliverables' => [
                    ['order' => 1, 'name' => 'Engine Inspection Report', 'description' => 'Laporan inspeksi mesin', 'nature' => 'mandatory', 'is_active' => true],
                    ['order' => 2, 'name' => 'Maintenance Recommendation', 'description' => 'Rekomendasi maintenance', 'nature' => 'mandatory', 'is_active' => true],
                ],
            ],
            [
                'code' => 'MOD006',
                'name' => 'Konsultasi Desain Kapal',
                'duration' => '60',
                'risk_level' => RiskLevel::High->value,
                'pricing_baseline' => 150000000,
                'is_active' => true,
                'work_order_items' => [
                    [
                        'order' => 1,
                        'name' => 'Design Review',
                        'description' => 'Review desain kapal',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'Hull Form', 'description' => 'Review bentuk lambung', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Stability Calculation', 'description' => 'Review perhitungan stabilitas', 'nature' => 'mandatory', 'is_active' => true],
                        ],
                    ],
                    [
                        'order' => 2,
                        'name' => 'Structural Analysis',
                        'description' => 'Analisis struktur kapal',
                        'nature' => 'mandatory',
                        'is_active' => true,
                        'subitems' => [
                            ['order' => 1, 'name' => 'FEA Analysis', 'description' => 'Finite element analysis', 'nature' => 'mandatory', 'is_active' => true],
                            ['order' => 2, 'name' => 'Fatigue Analysis', 'description' => 'Analisis kelelahan struktur', 'nature' => 'optional', 'is_active' => true],
                        ],
                    ],
                ],
                'work_order_references' => [
                    ['document_name' => 'IMO MODU Code', 'document_id' => 'IMO-MODU'],
                    ['document_name' => 'BKI Rules for Ship Construction', 'document_id' => 'BKI-RSC-2024'],
                ],
                'personels' => [
                    ['position_name' => 'Naval Architect', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->where('code', 'KOM005')->pluck('id')->toArray()],
                    ['position_name' => 'Structural Engineer', 'quantity' => 1, 'nature' => 'mandatory', 'competencies' => $competencies->where('code', 'KOM006')->pluck('id')->toArray()],
                ],
                'tools' => [
                    ['peralatan_id' => $peralatans->where('name', 'Visual Testing Equipment')->first()?->id, 'requires_calibration' => false, 'quantity' => 1],
                ],
                'deliverables' => [
                    ['order' => 1, 'name' => 'Design Approval', 'description' => 'Approval desain kapal', 'nature' => 'mandatory', 'is_active' => true],
                    ['order' => 2, 'name' => 'Technical Specification', 'description' => 'Spesifikasi teknis lengkap', 'nature' => 'mandatory', 'is_active' => true],
                ],
            ],
        ];

        foreach ($modules as $moduleData) {
            $module = Module::create([
                'code' => $moduleData['code'],
                'name' => $moduleData['name'],
                'duration' => $moduleData['duration'],
                'risk_level' => $moduleData['risk_level'],
                'pricing_baseline' => $moduleData['pricing_baseline'],
                'is_active' => $moduleData['is_active'],
            ]);

            // Create work order items with subitems
            if (isset($moduleData['work_order_items'])) {
                foreach ($moduleData['work_order_items'] as $itemData) {
                    $item = $module->workOrderItems()->create([
                        'order' => $itemData['order'],
                        'name' => $itemData['name'],
                        'description' => $itemData['description'],
                        'nature' => $itemData['nature'],
                        'is_active' => $itemData['is_active'],
                    ]);

                    if (isset($itemData['subitems'])) {
                        foreach ($itemData['subitems'] as $subitemData) {
                            $item->subitems()->create([
                                'order' => $subitemData['order'],
                                'name' => $subitemData['name'],
                                'description' => $subitemData['description'],
                                'nature' => $subitemData['nature'],
                                'is_active' => $subitemData['is_active'],
                            ]);
                        }
                    }
                }
            }

            // Create work order references
            if (isset($moduleData['work_order_references'])) {
                foreach ($moduleData['work_order_references'] as $refData) {
                    $module->workOrderReferences()->create($refData);
                }
            }

            // Create personels with competencies
            if (isset($moduleData['personels'])) {
                foreach ($moduleData['personels'] as $personelData) {
                    $personel = $module->personels()->create([
                        'position_name' => $personelData['position_name'],
                        'quantity' => $personelData['quantity'],
                        'nature' => $personelData['nature'],
                    ]);
                    $personel->competencies()->sync($personelData['competencies']);
                }
            }

            // Create tools
            if (isset($moduleData['tools'])) {
                foreach ($moduleData['tools'] as $toolData) {
                    $module->tools()->create($toolData);
                }
            }

            // Create deliverables
            if (isset($moduleData['deliverables'])) {
                foreach ($moduleData['deliverables'] as $delData) {
                    $module->deliverables()->create($delData);
                }
            }
        }
    }
}
