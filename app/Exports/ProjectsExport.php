<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?string $search;
    protected ?string $statusFilter;
    protected ?string $riskFilter;

    public function __construct(?string $search = null, ?string $statusFilter = null, ?string $riskFilter = null)
    {
        $this->search = $search;
        $this->statusFilter = $statusFilter;
        $this->riskFilter = $riskFilter;
    }

    public function query()
    {
        $query = Project::with(['creator', 'approver'])->withCount('modules');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
                  ->orWhere('scope', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== null && $this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->riskFilter !== null && $this->riskFilter !== '') {
            $query->where('risk_level', $this->riskFilter);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Project',
            'Scope',
            'Durasi',
            'Risk Level',
            'CoE Control',
            'Status',
            'Jumlah Modul',
            'Total Estimasi',
            'Dibuat Oleh',
            'Disetujui Oleh',
            'Tanggal Submit',
            'Tanggal Approve',
            'Tanggal Dibuat',
        ];
    }

    public function map($project): array
    {
        static $no = 0;
        $no++;

        $statuses = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'coe_review' => 'CoE Review',
            'approved' => 'Approved',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return [
            $no,
            $project->code,
            $project->name,
            $project->scope ?? '-',
            $project->duration ?? '-',
            $project->risk_level->label(),
            $project->coe_control_level->label(),
            $statuses[$project->status] ?? $project->status,
            $project->modules_count,
            $project->total_estimate ? 'Rp ' . number_format($project->total_estimate, 0, ',', '.') : '-',
            $project->creator?->name ?? '-',
            $project->approver?->name ?? '-',
            $project->submitted_at?->format('d/m/Y H:i') ?? '-',
            $project->approved_at?->format('d/m/Y H:i') ?? '-',
            $project->created_at->format('d/m/Y H:i'),
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
