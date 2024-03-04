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
class rfsNoneActiveRecord extends DbRecord
{
    use recordTrait, prepareDatesForQueryTrait;

    static public $columnHeadings = array(
        "RFS ID",
        "PRN",
        "Project Title",
        "Project Code",
        "Requestor Name",
        "Requestor Email",
        "Value Stream",
        "Business Unit",
        "RFS Creator",
        "RFS Created",
        "Link to PGMP",
        "Resource Ref",
        "Organisation",
        "Service",
        "Description",
        "Start Date",
        "End Date",
        "Total Hours",
        "Resource Name",
        "Request Creator",
        "Request Created",
        "Cloned From",
        "Status",
        "Rate Type",
        "Hours Type",
        "RFS Status",
        "RFS Type"
    );

    static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (rfsNoneActiveRecord::$columnHeadings as $key => $value )
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