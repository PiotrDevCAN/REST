<?php
namespace rest;

use itdq\DbRecord;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class RRBespokeRateRecord extends DbRecord
{
    use recordTrait;

    static public $columnHeadings = array(
        "RFS Id",
        "Resource Request",
        "Resource Name",
        "Resource Type",
        "PS Band"
    );

    static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (RRBespokeRateRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }
}