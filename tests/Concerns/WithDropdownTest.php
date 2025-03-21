<?php

namespace Maatwebsite\Excel\Tests\Concerns;

use LaravelExcelExtended\Concerns\WithDropdown;
use LaravelExcelExtended\Helpers\Excel;
use LaravelExcelExtended\Tests\TestCase;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class WithDropdownTest extends TestCase
{
    public function test_can_set_dropdown_data_validation()
    {
        $export = new class implements FromArray, WithDropdown
        {
            use Exportable;

            public function array(): array
            {
                return [
                    ['A', 'X'],
                    ['B', 'Y'],
                    ['C', 'Z']
                ];
            }

            public function dropdown(): array
            {
                return [
                    "A1:A3" => [
                        'values' => ['A', 'B', 'C'],
                    ],
                    "B1:B3" => [
                        'values' => ['X', 'Y', 'Z'],
                        'strategy' => Excel::LIST_STRATEGY_HIDDEN_COLUMN,
                        'allowBlank' => false,
                        'showErrorMessage' => false,
                        'showInputMessage' => false,
                        'showDropDown' => false,
                        'errorTitle' => 'Test error title',
                        'error' => 'Test error',
                        'promptTitle' => 'Test prompt title',
                        'prompt' => 'Test prompt',
                    ]
                ];
            }
        };

        $export->store('with-dropdown.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../temp/with-dropdown.xlsx', 'Xlsx');
        $sheet = $spreadsheet->getActiveSheet();

        $validationSimple = $sheet->getDataValidation("A1");
        $this->assertEquals('"A,B,C"', $validationSimple->getFormula1());

        $validationFull = $sheet->getDataValidation("B1");
        $this->assertEquals('$C$1:$C$3', $validationFull->getFormula1());
        $this->assertFalse($validationFull->getAllowBlank());
        $this->assertFalse($validationFull->getShowErrorMessage());
        $this->assertFalse($validationFull->getShowInputMessage());
        $this->assertFalse($validationFull->getShowDropDown());
        $this->assertEquals('Test error title', $validationFull->getErrorTitle());
        $this->assertEquals('Test error', $validationFull->getError());
        $this->assertEquals('Test prompt title', $validationFull->getPromptTitle());
        $this->assertEquals('Test prompt', $validationFull->getPrompt());
        
        $this->assertEquals('X', $sheet->getCell('C1')->getValue());
        $this->assertEquals('Y', $sheet->getCell('C2')->getValue());
        $this->assertEquals('Z', $sheet->getCell('C3')->getValue());
    }
}
