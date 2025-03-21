# Larave Excel extended

Added additional futures to `maatwebsite/excel` package

## installation

```
composer require kahil-raghed/laravel-excel-extended
```

The `LaravelExcelExtended\Providers\FuturesProvider` is auto-discovered and registered by default.

If you want to register it yourself, add the ServiceProvider in config/app.php:

```php
'providers' => [
    /*
     * Package Service Providers...
     */
    LaravelExcelExtended\Providers\FuturesProvider::class,
]

```

## Futures

### Max width for auto size columns:

You can set maximum width to prevent columns from scalling too much.


Set for all columns:

```php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use LaravelExcelExtended\Concerns\WithMaxidth;


class ProductsExport implements ShouldAutoSize, WithMaxWidth {
    public function maxWidth(){
        return 50;
    }
}
```

Or set for spacific columns:
```php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use LaravelExcelExtended\Concerns\WithMaxidth;


class ProductsExport implements ShouldAutoSize, WithMaxWidth {
    public function maxWidth(){
        return [
            'A' => 50,
            'H' => 100
        ];
    }
}
```

### Dropdown lists

You can create interactive drop down lists using `WithDropdowns` concern.



```php
namespace App\Exports;

use LaravelExcelExtended\Concerns\WithDropdowns;


class ProductsExport implements ShouldAutoSize, WithMaxWidth {
    public function dropdowns(){
        [
            'B2:B100' => [ // product size
                'values' => [
                    'XS',
                    'S',
                    'M',
                    'L'
                ]
            ],
        ]
    }
}
```
But this approach has limitations refer [phpspreadsheet docs](https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/#setting-data-validation-on-a-cell)

>It is important to remember that any string participating in an Excel formula is allowed to be maximum 255 characters (not bytes). This sets a limit on how many items you can have in the string "Item A,Item B,Item C". Therefore it is normally a better idea to type the item values directly in some cell range, say A1:A3, and instead use, say, ```$validation->setFormula1('\'Sheet title\'!$A$1:$A$3')```. Another benefit is that the item values themselves can contain the comma , character itself.

For long lists or list containing values with commas it is better to set stratergy to `Excel::LIST_STRATEGY_HIDDEN_COLUMN`.


it stores allowed values in a hidden column after the data.

```php
namespace App\Exports;

use Models\Category;
use LaravelExcelExtended\Concerns\WithDropdowns;
use LaravelExcelExtended\Helpers\Excel;


class ProductsExport implements WithDropdowns {
    public function dropdowns(){
        $categories = Category::all(['id', 'name']);
        [
            'B2:B100' => [
                'values' => $category->map(fn ($c) => $c->name),
                'strategy' => Excel::LIST_STRATEGY_HIDDEN_COLUMN
            ],
        ]
    }
}
```