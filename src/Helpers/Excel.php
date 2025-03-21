<?php

namespace LaravelExcelExtended\Helpers;

use Exception;
use Maatwebsite\Excel\Sheet;
use \PhpOffice\PhpSpreadsheet\Style\Conditional as ConditionalStyle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard as FormattingWizard;

class Excel
{

    const LIST_STRATEGY_SIMPLE = 'simple';
    const LIST_STRATEGY_HIDDEN_COLUMN = 'hidden-column';

    private function __construct()
    {
    }


    static function applyDateFormat(
        Sheet $sheet,
        string $cells,
    ) {
        $sheet->getDelegate()->getCell($cells)->setDataType(DataType::TYPE_ISO_DATE);
    }

    static function applyColorFormat(
        Sheet $sheet,
        string $cells,
        array $colors,
    ) {
        $conditions = [];
        foreach ($colors as $key => $value) {
            $conditional = new ConditionalStyle();
            $conditional->setConditionType(ConditionalStyle::CONDITION_CELLIS);
            $conditional->setOperatorType(ConditionalStyle::OPERATOR_EQUAL);
            $conditional->setConditions("\"$key\"");
            $conditional->getStyle()->getFont()->setColor(new Color($value));
            $conditions[] = $conditional;
        }

        $sheet->getDelegate()->getStyle($cells)->setConditionalStyles($conditions);
    }

    static function setMaxWidth(Sheet $sheet, int $maxWidth)
    {
        $sheet->getDelegate()->calculateColumnWidths();

        foreach ($sheet->getDelegate()->getColumnDimensions() as $col) {
            if ($col->getWidth() > $maxWidth) {
                $col->setAutoSize(false);
                $col->setWidth($maxWidth);
            }
        }
    }

    static function applyListValidation(
        Sheet $sheet,
        string $cells,
        iterable $values,
        bool $allowBlank = true,
        bool $showErrorMessage = true,
        bool $showInputMessage = true,
        bool $showDropDown = true,
        string $errorTitle = 'Input error',
        string $error = 'Value is not in list.',
        string $promptTitle = 'Pick from list',
        string $prompt = 'Please pick a value from the drop-down list.',
        string $strategy = null,

    ) {
        if ($strategy === null) {
            $strategy = static::LIST_STRATEGY_SIMPLE;
        }
        $sheet = $sheet->getDelegate();
        
        switch ($strategy) {
            case static::LIST_STRATEGY_HIDDEN_COLUMN:
                $columnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn()) + 1;
                $column = Coordinate::stringFromColumnIndex($columnIndex);

                $sheet->getColumnDimensionByColumn($columnIndex)->setVisible(false);

                for ($i = 0; $i < sizeof($values); $i++) {
                    $cell = $sheet->getCell([$columnIndex, $i + 1]);
                    $cell->setValue($values[$i]);
                }
                $formula = Coordinate::absoluteCoordinate($column . 1) . ":" . Coordinate::absoluteCoordinate($column . sizeof($values));
                break;
            case static::LIST_STRATEGY_SIMPLE;
                $formula = '"' . implode(',', $values) . '"';
                break;
            default:
                throw new Exception("Unknown list strategy: $strategy");
                break;
        }
        if ($strategy == static::LIST_STRATEGY_SIMPLE) {
        } elseif ($strategy == static::LIST_STRATEGY_HIDDEN_COLUMN) {
        }



        $validation = $sheet->getDataValidation($cells);
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank($allowBlank);
        $validation->setShowInputMessage($showInputMessage);
        $validation->setShowErrorMessage($showErrorMessage);
        $validation->setShowDropDown($showDropDown);
        $validation->setErrorTitle($errorTitle);
        $validation->setError($error);
        $validation->setPromptTitle($promptTitle);
        $validation->setPrompt($prompt);
        $validation->setFormula1($formula);
    }
}
