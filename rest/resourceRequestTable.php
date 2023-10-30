<?php
namespace rest;

use DateInterval;
use Exception;
use itdq\DateClass;
use itdq\DbTable;
use rest\traits\resourceRequestTableTrait;
use rest\traits\tableTrait;

class resourceRequestTable extends DbTable
{
    use tableTrait, resourceRequestTableTrait;

    protected $rfsMaxEndDate;

    // $days, $months, $years - values to add
    static function addTime(\DateTime $date_time, $days, $months, $years){
        if ($days) {
            $xDays = new \DateInterval('P'.$days.'D');
            $date_time->add($xDays);
        }

        // Preserve day number
        if ($months or $years) {
            $old_day = $date_time->format('d');
        }

        if ($months) {
            $xMonths = new \DateInterval('P'.$months.'M');
            $date_time->add($xMonths);
        }

        if ($years) {
            $xYears = new \DateInterval('P'.$years.'Y');
            $date_time->add($xYears);
        }
        
        // Patch for adding months or years    
        if ($months or $years) {
            $new_day = $date_time->format("d");

            // The day is changed - set the last day of the previous month
            if ($old_day != $new_day) {
                $subxDays = new \DateInterval('P'.$new_day.'D');
                $date_time->sub($subxDays);
            }
        }
        // You can chage returned format here
        // return $date_time->format('Y-m-d');
        return $date_time;
    }

    static function getThisMonthsClaimCutoff($dateObj){
        $thisMonthsClaimCutoff = DateClass::claimMonth($dateObj);
        $thisMonthsClaimCutoff = self::addTime($thisMonthsClaimCutoff, 1, 0, 0);
    
        return $thisMonthsClaimCutoff;
    }

    static function archivedInLast12MthsPredicate($tableAbbrv = null) {
        $predicate = "(";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE >= DATEADD(month, -12, CURRENT_TIMESTAMP)";
        $predicate.= " AND ";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE <= CURRENT_TIMESTAMP"; 
        $predicate.= ")";
        return $predicate;
    }

    static function archivedSince2022Predicate($tableAbbrv = null) {
        $predicate = "(";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE >= '2022-01-01'";
        $predicate.= " AND ";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE <= CURRENT_TIMESTAMP"; 
        $predicate.= ")";
        return $predicate;
    }

    function prepareDatesForQuery(){
        
        // The first month we need to show them is the CLAIM month they are currently in. 
        // So start with today, and get the next Claim Cut off - th
        
        $startMonthObj = new \DateTime();
        $thisMonthObj = new \DateTime();

        $thisYear = $thisMonthObj->format('Y');
        $thisMonth = $thisMonthObj->format('m');
        $thisDay = '01';
        $thisMonthObj->setDate($thisYear, $thisMonth, $thisDay);
        $thisMonthsClaimCutoff = self::getThisMonthsClaimCutoff($thisMonthObj->format('d-m-Y'));

        $startMonthObj >= $thisMonthsClaimCutoff ? $startMonthObj = self::addTime($startMonthObj, 0, 1, 0) : null;
        $startYear  = $startMonthObj->format('Y');
        $startMonth = $startMonthObj->format('m');
        
        $lastMonthObj = clone $startMonthObj;
        $lastMonthObj = self::addTime($lastMonthObj, 0, 6, 0);
        $lastYear = $lastMonthObj->format('Y');
        $lastMonth = $lastMonthObj->format('m');
                 
        $nextMonthObj = clone $startMonthObj;
        $monthLabels = array();
        $monthDetails = array();
        
        for ($i = 0; $i < 6; $i++) {
            $monthLabels[] = $nextMonthObj->format('M_y');
            $monthDetails[$i]['year'] = $nextMonthObj->format('Y');
            $monthDetails[$i]['month'] = $nextMonthObj->format('m');
            $nextMonthObj = self::addTime($nextMonthObj, 0, 1, 0);
        }
        
        $dates = array(            
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        );

        return $dates;
    }

    function prepareListQuery() {

    }

    function returnNotMatchingBUs($predicate= null, $rsOnly = false) {

        $dates = $this->prepareDatesForQuery();
        list(      
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        ) = $dates;

        $sql = $this->prepareListQuery();

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

    function returnNotMatchingBUsForDataTables($predicate= null, $rsOnly = false) {

        $dates = $this->prepareDatesForQuery();
        list(
            'monthLabels' => $monthLabels,
            'monthDetails' => $monthDetails,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'lastYear' => $lastYear,
            'lastMonth' => $lastMonth
        ) = $dates;

        $sql = $this->prepareListQuery();

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