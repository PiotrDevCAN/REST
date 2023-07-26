<?php
namespace rest;

use itdq\DbRecord;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class rateCardRecord extends DbRecord
{
    use recordTrait;

    static public $columnHeadings = array(
        "RFS Id",
        "RFS Created Date",
        "Resource Request",
        "Resource Name",
        "Resource Type",
        "PS Band",
        "Overrides PS Band",
        "Bespoke Rate (Yes/No)"
    );

    static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (rateCardRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }
}