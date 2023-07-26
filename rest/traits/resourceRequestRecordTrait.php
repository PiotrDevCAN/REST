<?php
namespace rest\traits;

use rest\resourceRequestRecord;

trait resourceRequestRecordTrait
{
    static function htmlHeaderRow($startDate=null, $endDate=null){
        $headerRow = "<tr>";
        $headerRow .= resourceRequestRecord::htmlHeaderCellsStatic($startDate,$endDate);
        $headerRow .= "</tr>";

        return $headerRow;
    }

    function htmlHeaderCells($startDate=null){
        $headerCells = "";
        foreach (resourceRequestRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        $headerCells .= "<th>RR</th>";  // allow for the RR field that will come from DB2 from the join

        return $headerCells;
    }

    static function htmlHeaderCellsStatic($startDate=null){
        $headerCells = "";
        foreach (resourceRequestRecord::$columnHeadings as $key => $value )
        {
            $headerCells .= "<th>" . $value . "</th>";
        }
        $headerCells .= "<th>RR</th>";  // allow for the RR field that will come from DB2 from the join

        return $headerCells;
    }
}