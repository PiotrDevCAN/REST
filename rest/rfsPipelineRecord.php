<?php
namespace rest;

use itdq\DbRecord;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class rfsPipelineRecord extends DbRecord
{
    use recordTrait;

    static public $columnHeadings = array(
        "Resource Name",
        "RFS ID",
        "Title",
        "Resource Req.",
        "From",
        "To",
        "Value Stream",
        "Link to PGMP"
    );

    static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (rfsPipelineRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }
}