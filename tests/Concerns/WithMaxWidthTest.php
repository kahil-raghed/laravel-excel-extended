<?php

namespace Maatwebsite\Excel\Tests\Concerns;


use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use LaravelExcelExtended\Concerns\WithMaxWidth;
use LaravelExcelExtended\Tests\TestCase;


class WithMaxWidthTest extends TestCase
{
    public function test_can_set_max_size_per_column()
    {
        $export = new class implements FromArray, WithMaxWidth, ShouldAutoSize
        {
            use Exportable;

            public function maxWidth(): float|array
            {
                return [
                    'A' => 50,
                    'B' => 60
                ];
            }

            public function array(): array
            {
                return [
                    [
                        implode('', array_fill(0, 100, 'A')),
                        implode('', array_fill(0, 100, 'B')),
                        implode('', array_fill(0, 100, 'C')),
                    ],
                ];
            }
        };

        $export->store('with-max-width.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../temp/with-max-width.xlsx', 'Xlsx');

        $this->assertEquals(50, $spreadsheet->getActiveSheet()->getColumnDimension('A')->getWidth());
        $this->assertEquals(60, $spreadsheet->getActiveSheet()->getColumnDimension('B')->getWidth());
        $this->assertNotEquals(50, $spreadsheet->getActiveSheet()->getColumnDimension('C')->getWidth());
    }


    public function test_can_set_max_for_all_columns()
    {
        $export = new class implements FromArray, WithMaxWidth, ShouldAutoSize
        {
            use Exportable;

            public function maxWidth(): float|array
            {
                return 50;
            }

            public function array(): array
            {
                return [
                    [
                        implode('', array_fill(0, 100, 'A')),
                        implode('', array_fill(0, 100, 'B')),
                        "CC",
                    ],
                ];
            }
        };

        $export->store('with-max-width.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../temp/with-max-width.xlsx', 'Xlsx');

        $this->assertEquals(50, $spreadsheet->getActiveSheet()->getColumnDimension('A')->getWidth());
        $this->assertEquals(50, $spreadsheet->getActiveSheet()->getColumnDimension('B')->getWidth());
        $this->assertNotEquals(50, $spreadsheet->getActiveSheet()->getColumnDimension('C')->getWidth());
    }
}