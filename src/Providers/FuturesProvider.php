<?php

namespace Raghed\LaravelExcelExtended\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use Raghed\LaravelExcelExtended\Concerns\WithDropdowns;
use Raghed\LaravelExcelExtended\Concerns\WithMaxWidth;
use Raghed\LaravelExcelExtended\Handlers\WithDropdownsHandler;
use Raghed\LaravelExcelExtended\Handlers\WithMaxWidthHandler;

class FuturesProvider extends ServiceProvider
{
    public function register()
    {
        Excel::extend(WithMaxWidth::class, new WithMaxWidthHandler(), AfterSheet::class);
        Excel::extend(WithDropdowns::class, new WithDropdownsHandler(), AfterSheet::class);
    }
}
