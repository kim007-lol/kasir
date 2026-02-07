<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $transactions;
    protected $date;

    public function __construct($transactions, $date)
    {
        $this->transactions = $transactions;
        $this->date = $date;
    }

    public function view(): View
    {
        return view('reports.excel', [
            'transactions' => $this->transactions,
            'date' => $this->date
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
