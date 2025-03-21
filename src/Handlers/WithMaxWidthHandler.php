<?php

namespace LaravelExcelExtended\Handlers;

use LaravelExcelExtended\Concerns\WithMaxWidth;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;

class WithMaxWidthHandler {

    public function __invoke(WithMaxWidth $export, AfterSheet $ev)
    {   
        $maxWidth = $export->maxWidth();
        if (is_float($maxWidth)) {
            $columns = $ev->sheet->getDelegate()->getColumnDimensions();
            foreach ($columns as $col) {
                $this->applyMaxWidth($col, $maxWidth);
            }
        } else if (is_array($maxWidth)) {
            foreach ($maxWidth as $colId => $colMaxWidth) {
                $col = $ev->sheet->getDelegate()->getColumnDimension($colId);
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