<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/6
 * Time: 上午1:20
 */

namespace Cly\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class NoScienceValueBinder extends DefaultValueBinder
{
    public function bindValue(Cell $cell, $value)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        } elseif (is_object($value)) {
            // Handle any objects that might be injected
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (!($value instanceof RichText)) {
                $value = (string)$value;
            }
        }

        if (is_string($value) && strlen($value) > 10) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
        } else {
            // Set value explicit
            $cell->setValueExplicit($value, self::dataTypeForValue($value));
        }

        // Done!
        return true;
    }
}