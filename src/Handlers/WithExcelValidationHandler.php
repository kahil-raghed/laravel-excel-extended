<?php

namespace LaravelExcelExtended\Handlers;

use LaravelExcelExtended\Concerns\WithDropdown;
use LaravelExcelExtended\Concerns\WithExcelValidation;
use LaravelExcelExtended\Constants\ValidationTypes;
use LaravelExcelExtended\Helpers\Excel;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class WithExcelValidationHandler
{
    public function __invoke(WithExcelValidation $export, Sheet $sheet)
    {
        $rules = $export->excelValidation();

        foreach ($rules as $range => $config) {
            switch ($config['type']) {
                case ValidationTypes::LIST:
                case ValidationTypes::INTEGER;
                    $dataValidationType = $config['type'] === ValidationTypes::INTEGER ? DataValidation::TYPE_WHOLE : DataValidation::TYPE_DECIMAL;
                    Excel::applyListValidation(
                        sheet: $sheet,
                        cells: $range,
                        values: $config['values'] ?? [],
                        allowBlank: $config['allowBlank'] ?? true,
                        showErrorMessage: $config['showErrorMessage'] ?? true,
                        showInputMessage: $config['showInputMessage'] ?? true,
                        showDropDown: $config['showDropDown'] ?? true,
                        errorTitle: $config['errorTitle'] ?? 'Input error',
                        error: $config['error'] ?? 'Value is not in list.',
                        promptTitle: $config['promptTitle'] ?? 'Pick from list',
                        prompt: $config['prompt'] ?? 'Please pick a value from the drop-down list.',
                        strategy: $config['strategy'] ?? Excel::LIST_STRATEGY_SIMPLE,
                    );
                case ValidationTypes::NUMBER:
                    Excel::applyNumberValidation(
                        sheet: $sheet,
                        cells: $range,
                        allowBlank: $config['allowBlank'] ?? true,
                        showErrorMessage: $config['showErrorMessage'] ?? true,
                        showInputMessage: $config['showInputMessage'] ?? true,
                        errorTitle: $config['errorTitle'] ?? 'Input error',
                        error: $config['error'] ?? 'Value is not a number.',
                    );
                    break;
            }
        }
    }
}
