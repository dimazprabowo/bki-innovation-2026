<?php

namespace App\Exports;

use App\Models\Module;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ModulesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?string $search;
    protected ?string $riskFilter;

    public function __construct(?string $search = null, ?string $riskFilter = null)
    {
        $this->search = $search;
        $this->riskFilter = $riskFilter;
    }

    public function query()
    {
        $query = Module::with('deliverables')->withCount('projects');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->riskFilter !== null && $this->riskFilter !== '') {
            $query->where('risk_level', $this->riskFilter);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Modul',
            'Durasi',
            'Deliverable',
            'Risk Level',
            'Pricing Baseline',
            'Jumlah Project',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function map($module): array
    {
        static $no = 0;
        $no++;

        // Get all deliverables names
        $deliverables = $module->deliverables->pluck('name')->join(', ') ?: '-';

        return [
            $no,
            $module->code,
            $module->name,
            $module->duration ?? '-',
            $deliverables,
            $module->risk_level->label(),
            $module->pricing_baseline ? 'Rp ' . number_format($module->pricing_baseline, 0, ',', '.') : '-',
            $module->projects_count,
            $module->is_active ? 'Aktif' : 'Non-Aktif',
            $module->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }
}
