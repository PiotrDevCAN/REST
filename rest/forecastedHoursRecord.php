<?php
namespace rest;

use itdq\DbRecord;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class forecastedHoursRecord extends DbRecord
{
    use recordTrait;

    static public $columnHeadings = array(
        "Resource Name",
    );

    static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (forecastedHoursRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }
}