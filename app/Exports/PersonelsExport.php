<?php

namespace App\Exports;

use App\Models\Personel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersonelsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?string $search;
    protected ?int $competencyId;

    public function __construct(?string $search = null, ?int $competencyId = null)
    {
        $this->search = $search;
        $this->competencyId = $competencyId;
    }

    public function query()
    {
        $query = Personel::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->competencyId) {
            $query->whereHas('competencies', function ($q) {
                $q->where('competencies.id', $this->competencyId);
            });
        }

        return $query->where('is_active', true)->with('competencies')->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Personel',
            'Kompetensi',
            'Jumlah Kompetensi',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function map($personel): array
    {
        static $no = 0;
        $no++;

        $competencyNames = $personel->competencies->map(function ($competency) {
            return $competency->name . ' ' . $competency->level_label;
        })->implode(', ');

        return [
            $no,
            $personel->code,
            $personel->name,
            $competencyNames ?: '-',
            $personel->competencies->count(),
            $personel->is_active ? 'Aktif' : 'Non-Aktif',
            $personel->created_at->format('d/m/Y H:i'),
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
