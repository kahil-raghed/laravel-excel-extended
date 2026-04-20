<?php

namespace Maatwebsite\Excel\Tests\Concerns;

use LaravelExcelExtended\Concerns\WithDropdown;
use LaravelExcelExtended\Concerns\WithExcelValidation;
use LaravelExcelExtended\Constants\ValidationTypes;
use LaravelExcelExtended\Helpers\Excel;
use LaravelExcelExtended\Tests\TestCase;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class WithExcelValidationTest extends TestCase
{
    public function test_can_set_validation_data_validation()
    {
        $export = new class implements FromArray, WithExcelValidation
        {
            use Exportable;

            public function array(): array
            {
                return [
                    ['A', 'X', '12'],
                    ['B', 'Y', '12'],
                    ['C', 'Z', '12']
                ];
            }

            public function excelValidation(): array
            {
                return [
                    "A1:A3" => [
                        'type' => ValidationTypes::LIST,
                        'values' => ['A', 'B', 'C'],
                    ],
                    "B1:B3" => [
                        'type' => ValidationTypes::LIST,
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
                    ],
                    "C1:C3" => [
                        'type' => ValidationTypes::NUMBER,
                        'allowBlank' => false,
                        'showErrorMessage' => false,
                        'showInputMessage' => false,
                        'errorTitle' => 'Test error title',
                        'error' => 'Test error',
                        'promptTitle' => 'Test prompt title',
                        'prompt' => 'Test prompt',
                    ],
                ];
            }
        };

        $export->store('with-dropdown.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../temp/with-dropdown.xlsx', 'Xlsx');
        $sheet = $spreadsheet->getActiveSheet();

        $validationSimple = $sheet->getDataValidation("A1");
        $this->assertEquals('"A,B,C"', $validationSimple->getFormula1());

        $validationFull = $sheet->getDataValidation("B1");
        $this->assertEquals('$D$1:$D$3', $validationFull->getFormula1());
        $this->assertFalse($validationFull->getAllowBlank());
        $this->assertFalse($validationFull->getShowErrorMessage());
        $this->assertFalse($validationFull->getShowInputMessage());
        $this->assertFalse($validationFull->getShowDropDown());
        $this->assertEquals('Test error title', $validationFull->getErrorTitle());
        $this->assertEquals('Test error', $validationFull->getError());
        $this->assertEquals('Test prompt title', $validationFull->getPromptTitle());
        $this->assertEquals('Test prompt', $validationFull->getPrompt());

        $validationNumber = $sheet->getDataValidation("C1");
        $this->assertEquals(DataValidation::TYPE_DECIMAL, $validationNumber->getType());
        $this->assertFalse($validationNumber->getAllowBlank());
        $this->assertFalse($validationNumber->getShowErrorMessage());
        $this->assertFalse($validationNumber->getShowInputMessage());
        $this->assertEquals('Test error title', $validationNumber->getErrorTitle());
        $this->assertEquals('Test error', $validationNumber->getError());
    }
}
