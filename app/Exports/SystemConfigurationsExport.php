<?php

namespace App\Exports;

use App\Models\SystemConfiguration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SystemConfigurationsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?string $search;
    protected ?string $isActive;

    public function __construct(?string $search = null, ?string $isActive = null)
    {
        $this->search = $search;
        $this->isActive = $isActive;
    }

    public function query()
    {
        $query = SystemConfiguration::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('key', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('value', 'like', "%{$this->search}%");
            });
        }

        if ($this->isActive !== null && $this->isActive !== '') {
            $query->where('is_active', $this->isActive === '1');
        }

        return $query->orderBy('category')->orderBy('key');
    }

    public function headings(): array
    {
        return [
            'No',
            'Key',
            'Kategori',
            'Value',
            'Tipe Data',
            'Deskripsi',
            'Dapat Diedit',
            'Status',
        ];
    }

    public function map($config): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $config->key,
            $config->category->label(),
            $config->value,
            $config->data_type->label(),
            $config->description ?? '-',
            $config->is_editable ? 'Ya' : 'Tidak',
            $config->is_active ? 'Aktif' : 'Nonaktif',
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
