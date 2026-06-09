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
                'teams' => [
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
                'teams' => [
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

            // Create teams with competencies
            if (isset($moduleData['teams'])) {
                foreach ($moduleData['teams'] as $teamData) {
                    $team = $module->teams()->create([
                        'position_name' => $teamData['position_name'],
                        'quantity' => $teamData['quantity'],
                        'nature' => $teamData['nature'],
                    ]);
                    $team->competencies()->sync($teamData['competencies']);
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
