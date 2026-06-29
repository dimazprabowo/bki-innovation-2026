<?php

namespace App\Exports;

use App\Models\Peralatan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeralatanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?string $search;
    protected ?string $calibrationStatus;
    protected ?string $condition;
    protected ?string $ownershipStatus;
    protected ?string $reviewStatusFilter;

    public function __construct(
        ?string $search = null,
        ?string $calibrationStatus = null,
        ?string $condition = null,
        ?string $ownershipStatus = null,
        ?string $reviewStatusFilter = null
    ) {
        $this->search = $search;
        $this->calibrationStatus = $calibrationStatus;
        $this->condition = $condition;
        $this->ownershipStatus = $ownershipStatus;
        $this->reviewStatusFilter = $reviewStatusFilter;
    }

    public function query()
    {
        $query = Peralatan::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%");
            });
        }

        if ($this->calibrationStatus) {
            $query->where('calibration_status', $this->calibrationStatus);
        }

        if ($this->condition) {
            $query->where('condition', $this->condition);
        }

        if ($this->ownershipStatus) {
            $query->where('ownership_status', $this->ownershipStatus);
        }

        if ($this->reviewStatusFilter !== null && $this->reviewStatusFilter !== '') {
            $query->where('review_status', $this->reviewStatusFilter);
        }

        return $query->where('is_active', true)->with('evidences')->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Alat',
            'Deskripsi',
            'Lokasi',
            'Status Kalibrasi',
            'Tanggal Expired Kalibrasi',
            'Kondisi',
            'Status Kepemilikan',
            'Jumlah Evidence',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function map($peralatan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $peralatan->code,
            $peralatan->name,
            $peralatan->description ?? '-',
            $peralatan->location ?? '-',
            $peralatan->calibration_status->label(),
            $peralatan->calibration_expired_date ? $peralatan->calibration_expired_date->format('d/m/Y') : '-',
            $peralatan->condition->label(),
            $peralatan->ownership_status->label(),
            $peralatan->evidences->count(),
            $peralatan->is_active ? 'Aktif' : 'Non-Aktif',
            $peralatan->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'],
                ],
            ],
        ];
    }
}
