<?php

namespace App\Exports;

use App\Models\Competency;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompetenciesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?string $search;
    protected ?string $levelFilter;
    protected ?string $isActive;

    public function __construct(?string $search = null, ?string $levelFilter = null, ?string $isActive = null)
    {
        $this->search = $search;
        $this->levelFilter = $levelFilter;
        $this->isActive = $isActive;
    }

    public function query()
    {
        $query = Competency::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->levelFilter !== null && $this->levelFilter !== '') {
            $query->where('level', $this->levelFilter);
        }

        if ($this->isActive !== null && $this->isActive !== '') {
            $query->where('is_active', $this->isActive === '1');
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Kompetensi',
            'Level',
            'Deskripsi',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function map($competency): array
    {
        static $no = 0;
        $no++;

        $levelLabel = match($competency->level) {
            1 => 'Level 1',
            2 => 'Level 2',
            3 => 'Level 3',
            default => '-',
        };

        return [
            $no,
            $competency->code,
            $competency->name,
            $levelLabel,
            $competency->description ?? '-',
            $competency->is_active ? 'Aktif' : 'Non-Aktif',
            $competency->created_at->format('d/m/Y H:i'),
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
