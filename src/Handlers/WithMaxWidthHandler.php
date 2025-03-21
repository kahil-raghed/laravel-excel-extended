<?php

namespace LaravelExcelExtended\Handlers;

use LaravelExcelExtended\Concerns\WithMaxWidth;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;

class WithMaxWidthHandler {

    public function __invoke(WithMaxWidth $export, Sheet $sheet)
    {   
        $maxWidth = $export->maxWidth();
        $sheet = $sheet->getDelegate();
        $sheet->calculateColumnWidths();
        if (is_float($maxWidth)) {
            $columns = $sheet->getColumnDimensions();
            foreach ($columns as $col) {
                $this->applyMaxWidth($col, $maxWidth);
            }
        } else if (is_array($maxWidth)) {
            foreach ($maxWidth as $colId => $colMaxWidth) {
                $col = $sheet->getColumnDimension($colId);
                $this->applyMaxWidth($col, $colMaxWidth);
            }
        }

    }

    function applyMaxWidth(ColumnDimension $col, float $maxWidth){
        if ($col->getAutoSize() && $col->getWidth() > $maxWidth) {
            $col->setAutoSize(false);
            $col->setWidth($maxWidth);
        }

    }
}