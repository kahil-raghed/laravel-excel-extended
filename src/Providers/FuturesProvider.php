<?php

namespace LaravelExcelExtended\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use LaravelExcelExtended\Concerns\WithDropdown;
use LaravelExcelExtended\Concerns\WithMaxWidth;
use LaravelExcelExtended\Handlers\WithDropdownHandler;
use LaravelExcelExtended\Handlers\WithMaxWidthHandler;

class FuturesProvider extends ServiceProvider
{
    public function register()
    {
        Excel::extend(WithMaxWidth::class, new WithMaxWidthHandler(), AfterSheet::class);
        Excel::extend(WithDropdown::class, new WithDropdownHandler(), AfterSheet::class);
    }
}
