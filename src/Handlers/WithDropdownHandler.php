<?php

namespace LaravelExcelExtended\Handlers;

use LaravelExcelExtended\Concerns\WithDropdown;
use LaravelExcelExtended\Helpers\Excel;
use Maatwebsite\Excel\Sheet;

class WithDropdownHandler
{
    public function __invoke(WithDropdown $export, Sheet $sheet)
    {
        $dropdowns = $export->dropdown();

        foreach ($dropdowns as $range => $dropdown) {
            Excel::applyListValidation(
                sheet: $sheet,
                cells: $range,
                values: $dropdown['values'] ?? [],
                allowBlank: $dropdown['allowBlank'] ?? true,
                showErrorMessage: $dropdown['showErrorMessage'] ?? true,
                showInputMessage: $dropdown['showInputMessage'] ?? true,
                showDropDown: $dropdown['showDropDown'] ?? true,
                errorTitle: $dropdown['errorTitle'] ?? 'Input error',
                error: $dropdown['error'] ?? 'Value is not in list.',
                promptTitle: $dropdown['promptTitle'] ?? 'Pick from list',
                prompt: $dropdown['prompt'] ?? 'Please pick a value from the drop-down list.',
                strategy: $dropdown['strategy'] ?? Excel::LIST_STRATEGY_SIMPLE,
            );
        }
    }
}
