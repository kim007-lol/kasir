<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelangganTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['nama', 'no_telepon'];
    }

    public function array(): array
    {
        // Contoh data agar user paham format
        return [
            ['Budi Santoso', '081234567890'],
            ['Siti Aminah', '089876543210'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Bold header + auto-width
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);

        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
