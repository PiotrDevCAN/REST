<?php
namespace rest\traits;

use rest\allTables;
use rest\rfsNoMatchBUsRecord;

trait rfsNoMatchBUsTrait
{
    use tableTrait, prepareDatesForQueryTrait;
    
    static function buildHTMLTable($tableId = 'noMatchBU'){

        $headerCells = rfsNoMatchBUsRecord::htmlHeaderCellsStatic();
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead><tr><?=$headerCells ;?></tr></thead>
        <tbody></tbody>
        <tfoot><tr><?=$headerCells ;?></tr></tfoot>
        </table>
        <?php
    }

    function returnForAPI($predicate= null, $rsOnly = false) {

        $dates = self::prepareDatesForQuery();
        list(      
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        ) = $dates;

        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE, RESOURCE_NAME ";

        foreach ($monthLabels as $label) {
            $sql.=",  $label ";
        }

        $sql.= " ) AS ( ";
        $sql.=" SELECT RESOURCE_REFERENCE, RESOURCE_NAME ";

        foreach ($monthLabels as $label) {
            $sql.=", SUM($label) AS $label ";
        }

        $sql.=" FROM ( ";
        $sql.=" SELECT RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME ";
        
        foreach ($monthDetails as $key => $detail) {
            $sql.=", CASE WHEN (CLAIM_YEAR = " . $detail['year'] . " AND CLAIM_MONTH = " . $detail['month'] . ") THEN SUM(CAST(HOURS as decimal(6,2))) ELSE null END AS " . $monthLabels[$key];
        }
        $sql.=" FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.=" LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RH ";
        $sql.=" ON RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND ( ";
        $sql.="        ( CLAIM_YEAR = $startYear AND ( CLAIM_MONTH >= $startMonth AND CLAIM_MONTH <= " .  ($startMonth+6) . " )) ";
        $sql.="      OR " ;
        $sql.="        ( CLAIM_YEAR = $lastYear  AND ( CLAIM_MONTH <= $lastMonth  AND CLAIM_MONTH >= " . ($lastMonth-6) . "  )) ";
        $sql.="    ) ";
        $sql.="      GROUP BY RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, CLAIM_YEAR, CLAIM_MONTH ";
        $sql.="      ) AS data ";
        $sql.="      GROUP BY RESOURCE_REFERENCE, RESOURCE_NAME ";
        // $sql.="      ORDER BY 1,2 ";
        $sql.=" ) ";
        $sql.=" " ;
        
        $sql.= " SELECT RFS.RFS_ID AS RFS_ID,";
        $sql.= " RR.RESOURCE_REFERENCE AS RR,";
        $sql.= " RR.RESOURCE_NAME AS RESOURCE_NAME,";
        $sql.= " RFS.BUSINESS_UNIT AS RFS_BUSINESS_UNIT,";
        $sql.= " AR.TRIBE_NAME_MAPPED AS INDIVIDUAL_BUSINESS_UNIT,";

        $sql .= " (SELECT STRING_AGG(D.ENTRY, '</br>') FROM " . $GLOBALS['Db2Schema'] . ".DIARY AS D ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_DIARY. " AS RD ";
        $sql .= " ON D.DIARY_REFERENCE = RD.DIARY_REFERENCE ";
        $sql .= " WHERE RD.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE) AS DIARY, CLAIM.* ";

        // RFS table
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        
        // RR table
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= " ON RR.RFS = RFS.RFS_ID ";

        // Active Resources table
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " as AR ";
        $sql.= " ON LOWER(RR.RESOURCE_NAME) = LOWER(AR.NOTES_ID)";

        $sql.= " , CLAIM";

        $sql.= empty($predicate) ? null : " WHERE 1=1 " . 
            " AND " . $predicate .
            " AND RFS.RFS_ID is not null" .
            " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE" .
            " AND (RR.RESOURCE_NAME is not null AND RR.RESOURCE_NAME != '') " .
            " AND LOWER(RFS.BUSINESS_UNIT) != LOWER(AR.TRIBE_NAME_MAPPED)" .            
            " AND (AR.TRIBE_NAME_MAPPED is not null AND AR.TRIBE_NAME_MAPPED != '') ";
            
        $sql.= " ORDER BY RFS.RFS_ID, RR.RESOURCE_REFERENCE";
        
        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = array();

        switch (true) {
            case $rsOnly:
                return $resultSet;
                break;
            case $resultSet:
                while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                    $allData[]  = array_map('trim', $row);
                }
                return array('data'=>$allData, 'sql'=>$sql);            
            default:
                return false;
                break;
        }
    }

    function returnAsArray($predicate= null, $rsOnly = false) {

        $dates = self::prepareDatesForQuery();
        list(
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        ) = $dates;

        $sql = "";
        $sql.=" WITH ";
        $sql.= " CLAIM(RESOURCE_REFERENCE, RESOURCE_NAME ";

        $monthNumber = 0;
        foreach ($monthLabels as $label) {
            $dataTableColName = "MONTH_" . substr("00" . ++$monthNumber,-2);
            $sql.=",  $dataTableColName ";
        }

        $sql.= " ) AS ( ";
        $sql.=" SELECT RESOURCE_REFERENCE, RESOURCE_NAME ";

        foreach ($monthLabels as $label) {
            $sql.=", SUM($label) AS $label ";
        }

        $sql.=" FROM ( ";
        $sql.=" SELECT RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME ";
        
        foreach ($monthDetails as $key => $detail) {
            $sql.=", CASE WHEN (CLAIM_YEAR = " . $detail['year'] . " AND CLAIM_MONTH = " . $detail['month'] . ") THEN SUM(CAST(HOURS as decimal(6,2))) ELSE null END AS " . $monthLabels[$key];
        }
        $sql.=" FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.=" LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RH ";
        $sql.=" ON RR.RESOURCE_REFERENCE = RH.RESOURCE_REFERENCE ";
        $sql.=" WHERE 1=1 " ;
        $sql.=" AND ( ";
        $sql.="        ( CLAIM_YEAR = $startYear AND ( CLAIM_MONTH >= $startMonth AND CLAIM_MONTH <= " .  ($startMonth+6) . " )) ";
        $sql.="      OR " ;
        $sql.="        ( CLAIM_YEAR = $lastYear  AND ( CLAIM_MONTH <= $lastMonth  AND CLAIM_MONTH >= " . ($lastMonth-6) . "  )) ";
        $sql.="    ) ";
        $sql.="      GROUP BY RR.RESOURCE_REFERENCE, RR.RESOURCE_NAME, CLAIM_YEAR, CLAIM_MONTH ";
        $sql.="      ) AS data ";
        $sql.="      GROUP BY RESOURCE_REFERENCE, RESOURCE_NAME ";
        // $sql.="      ORDER BY 1,2 ";
        $sql.=" ) ";
        $sql.=" " ;
        
        $sql.= " SELECT RFS.RFS_ID AS RFS_ID,";
        $sql.= " RR.RESOURCE_REFERENCE AS RR,";
        $sql.= " RR.RESOURCE_NAME AS RESOURCE_NAME,";
        $sql.= " RFS.BUSINESS_UNIT AS RFS_BUSINESS_UNIT,";
        $sql.= " AR.TRIBE_NAME_MAPPED AS INDIVIDUAL_BUSINESS_UNIT,";

        $sql .= " (SELECT STRING_AGG(D.ENTRY, '</br>') FROM " . $GLOBALS['Db2Schema'] . ".DIARY AS D ";
        $sql .= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_DIARY. " AS RD ";
        $sql .= " ON D.DIARY_REFERENCE = RD.DIARY_REFERENCE ";
        $sql .= " WHERE RD.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE) AS DIARY, CLAIM.* ";

        // RFS table
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        
        // RR table
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= " ON RR.RFS = RFS.RFS_ID ";

        // Active Resources table
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " as AR ";
        $sql.= " ON LOWER(RR.RESOURCE_NAME) = LOWER(AR.NOTES_ID)";

        $sql.= " , CLAIM";

        $sql.= empty($predicate) ? null : " WHERE 1=1 " . 
            " AND " . $predicate .
            " AND RFS.RFS_ID is not null" .
            " AND RR.RESOURCE_REFERENCE = CLAIM.RESOURCE_REFERENCE" .
            " AND (RR.RESOURCE_NAME is not null AND RR.RESOURCE_NAME != '') " .
            " AND LOWER(RFS.BUSINESS_UNIT) != LOWER(AR.TRIBE_NAME_MAPPED)" .
            " AND (AR.TRIBE_NAME_MAPPED is not null AND AR.TRIBE_NAME_MAPPED != '') ";
            
        $sql.= " ORDER BY RFS.RFS_ID, RR.RESOURCE_REFERENCE";

        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = array();

        switch (true) {
            case $rsOnly:
                return $resultSet;
                break;
            case $resultSet:
                $allData = array();
                while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                    $allData[]  = array_map('trim', $row);
                }
                return array('data'=>$allData, 'sql'=>$sql);            
            default:
                return false;
                break;
        }
    }
}