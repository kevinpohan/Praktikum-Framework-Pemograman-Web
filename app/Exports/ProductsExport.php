<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductsExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    public function collection()
    {
        return Product::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Unit',
            'Category',
            'Description',
            'Stock',
            'Supplier',
            'Barang Masuk',
            'Barang Keluar',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Sisakan 4 baris header
                $sheet->insertNewRowBefore(1, 4);

                // BARIS 1: Kevin Production 
                $sheet->setCellValue('A1', 'Kevin Production');

                $a1 = $sheet->getStyle('A1');
                $a1->getFont()->setBold(true)->setSize(12);
                $a1->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // BARIS 1: PT KEVIN MAKMUR 
                $sheet->setCellValue('B1', 'PT. KEVIN MAKMUR');
                $sheet->mergeCells('B1:H1');

                $c1 = $sheet->getStyle('B1');
                $c1->getFont()->setBold(true)->setSize(13);
                $c1->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // BARIS 2 & 3: Judul tengah
                $sheet->setCellValue('B2', 'Rekap Stock Produk Gudang');
                $sheet->mergeCells('B2:H2');

                $sheet->setCellValue('B3', 'Periode November 2025');
                $sheet->mergeCells('B3:H3');

                foreach (['B2', 'B3'] as $cell) {
                    $s = $sheet->getStyle($cell);
                    $s->getFont()->setBold(true)->setSize(13);
                    $s->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // AUTO WIDTH KOLOM
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // HEADER TABEL (BARIS 5) — BIRU
                $sheet->getStyle('A5:I5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '1F4E78'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // BORDER SEMUA SEL
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A5:I' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // KOLOM ID = RATA KIRI
                $sheet->getStyle('A6:A' . $lastRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // FOOTER TTD
                $footerStart = $lastRow + 3;
                $footerText = [
                    $footerStart => 'Diketahui oleh,',
                    $footerStart + 4 => 'Kepala Logistik,',
                    $footerStart + 6 => '_____________________',
                ];

                foreach ($footerText as $row => $text) {
                    $sheet->setCellValue('A' . $row, $text);
                    $st = $sheet->getStyle('A' . $row);
                    $st->getFont()->setBold(true);
                    $st->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
            },
        ];
    }
}
