<?php

namespace LaravelExcelExtended\Handlers;

use Maatwebsite\Excel\Events\AfterSheet;
use LaravelExcelExtended\Concerns\WithDropdowns;
use LaravelExcelExtended\Helpers\Excel;

class WithDropdownsHandler
{
    public function __invoke(WithDropdowns $export, AfterSheet $ev)
    {
        $dropdowns = $export->dropdowns();

        foreach ($dropdowns as $range => $dropdown) {
            Excel::applyListValidation(
                sheet: $ev->getSheet(),
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
