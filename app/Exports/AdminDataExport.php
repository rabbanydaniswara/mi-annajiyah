<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminDataExport extends StringValueBinder implements FromArray, WithColumnWidths, WithCustomValueBinder, WithEvents, WithTitle
{
    public function __construct(
        private readonly string $reportTitle,
        private readonly array $metadata,
        private readonly array $headers,
        private readonly array $rows,
        private readonly array $widths,
        private readonly string $sheetName = 'Data'
    ) {}

    public function array(): array
    {
        $sheetRows = [
            [$this->reportTitle],
        ];

        foreach ($this->metadata as $line) {
            $sheetRows[] = [$line];
        }

        $sheetRows[] = array_fill(0, count($this->headers), '');
        $sheetRows[] = $this->headers;

        foreach ($this->rows as $row) {
            $sheetRows[] = array_map(fn ($value) => $this->formatCell($value), $row);
        }

        return $sheetRows;
    }

    public function title(): string
    {
        $title = mb_substr(str_replace(['\\', '/', '?', '*', '[', ']'], ' ', $this->sheetName), 0, 31);

        return $title ?: 'Data';
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->widths as $index => $width) {
            $widths[Coordinate::stringFromColumnIndex($index + 1)] = $width;
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $columnCount = count($this->headers);
                $lastColumn = Coordinate::stringFromColumnIndex($columnCount);
                $metadataRows = count($this->metadata);
                $headerRow = $metadataRows + 3;
                $lastRow = max($headerRow, $headerRow + count($this->rows));
                $fullRange = "A1:{$lastColumn}{$lastRow}";
                $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";
                $dataRange = 'A'.($headerRow + 1).":{$lastColumn}{$lastRow}";

                for ($row = 1; $row <= $metadataRows + 1; $row++) {
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                }

                $sheet->getStyle($fullRange)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->getStyle($fullRange)->getAlignment()
                    ->setVertical(Alignment::VERTICAL_TOP)
                    ->setWrapText(true);

                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0B3B1E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                if ($metadataRows > 0) {
                    $sheet->getStyle("A2:{$lastColumn}".($metadataRows + 1))->applyFromArray([
                        'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '4B5563']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0B3B1E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                    ],
                ]);

                if (count($this->rows) > 0) {
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                        ],
                    ]);
                }

                $sheet->freezePane('A'.($headerRow + 1));
                $sheet->setAutoFilter("A{$headerRow}:{$lastColumn}{$lastRow}");
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension($headerRow)->setRowHeight(24);
                $sheet->setSelectedCell('A1');
            },
        ];
    }

    private function formatCell(mixed $value): string
    {
        $value = trim((string) ($value ?? '-'));

        return $value === '' ? '-' : $value;
    }
}
