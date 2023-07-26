<?php
namespace rest\traits;

use rest\rfsRecord;

trait rfsRecordTrait
{
    static function htmlHeaderRow(){
        $headerRow = "<tr>";
        $headerRow .= rfsRecord::htmlHeaderCellsStatic();

        $headerRow .= "</tr>";
        return $headerRow;
    }

    function htmlHeaderCells(){
        $headerCells = "";
        foreach (rfsRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }

	static function htmlHeaderCellsStatic(){
        $headerCells = "";
        foreach (rfsRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        return $headerCells;
    }
}