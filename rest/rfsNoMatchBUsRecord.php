<?php
namespace rest;

use itdq\DbRecord;
use rest\traits\prepareDatesForQueryTrait;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class rfsNoMatchBUsRecord extends DbRecord
{
    use recordTrait, prepareDatesForQueryTrait;

    static public $columnHeadings = array(
        "RFS ID",
        "Resource Ref",
        "Resource Name",
        "RFS Business Unit",
        "Individual Business Unit",
        "Diary"
    );

    static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (rfsNoMatchBUsRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        $dates = self::prepareDatesForQuery();
        list(      
            'monthLabels' => $monthLabels
        ) = $dates;
        foreach ($monthLabels as $label) {
            $headerCells .= "<th>" . $label . "</th>";
        }
        return $headerCells;
    }
}