<?php
namespace rest\traits;

use itdq\DbTable;
use itdq\DateClass;
use rest\allTables;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\rfsTable;

trait resourceRequestHoursTableTrait
{
    private $preparedGetTotalHrsStatement;
    private $preparedSetHrsStatement;
    private $hoursRemainingByReference;
    
    function createResourceRequestHours($resourceReference=null, $startDate=null, $endDate=null, $hours=0, $deleteExisting=true, $hrsType=resourceRequestRecord::HOURS_TYPE_REGULAR){
        
        $weeksCreated = 0;
        $stopped = false;
        $callbackMessage = '';

        if ($resourceReference === null) {
            error_log("Invalid Resource Reference");
            throw new \Exception("Invalid Resource Reference");
            $stopped = true;
        }

        if ($startDate === null || self::validateDate($startDate) === false) {
            error_log("Invalid Start Date");                
            throw new \Exception("Invalid Start Date");
            $stopped = true;
        }

        if ($endDate === null || self::validateDate($endDate) === false) {
            error_log("Invalid End Date");
            throw new \Exception("Invalid End Date");
            $stopped = true;
        }

        if ($hours == 0) {
            error_log("Invalid Total Hours amount");
            throw new \Exception("Invalid Total Hours amount");
            $stopped = true;
        }

        $invalidHoursType = !in_array($hrsType, resourceRequestRecord::$allHourTypes);
        if ($invalidHoursType) {
            error_log("Invalid Hours Type found");
            throw new \Exception("Invalid Hours Type found");
            $stopped = true;
        }

        $sdate = new \DateTime($startDate);
        $edate = new \DateTime($endDate);

        // get amount of days per type
        $weekendDays = DateClass::weekendDaysFromStartToEnd($sdate, $edate);

        $calculatedBusinessDays = DateClass::businessDaysFromStartToEnd($sdate, $edate);
        list(
            'businessDays' => $businessDays,
            'bankHolidays' => $bankHolidays
        ) = $calculatedBusinessDays;

        $allowedHoursType = array();

        // echo '<br/>';
        // echo ' Amount of business days '.$businessDays;
        // echo '<br/>';
        // echo ' Amount of weekend days '.$weekendDays;

        if ($businessDays > 0 && $weekendDays > 0) {
            // all type are correct
            $allowedHoursType = array(
                resourceRequestRecord::HOURS_TYPE_OT_WEEK_END,
                resourceRequestRecord::HOURS_TYPE_REGULAR,
                resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY
            );
        } else {
            if ($businessDays == 0 && $weekendDays > 0) {
                // If businesshrs is 0 then they must choose Weekend Overtime
                $allowedHoursType = array(
                    resourceRequestRecord::HOURS_TYPE_OT_WEEK_END
                );
                $callbackMessage = 'For selected period of time "'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_END.'" hours type must be choosen.';
            } elseif ($weekendDays == 0 && $businessDays > 0 ) {
                // If weekendhrs (or whatever it is called) is 0 they can choose regular or weekday overtime
                $allowedHoursType = array(
                    resourceRequestRecord::HOURS_TYPE_REGULAR,
                    resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY
                );
                $callbackMessage = 'For selected period of time either "'.resourceRequestRecord::HOURS_TYPE_REGULAR.'" or "'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY.'" hours type must be choosen.';
            } else {
                error_log("Invalid Calculation Of Business Or Weekend Days");
                throw new \Exception("Invalid Calculation Of Business Or Weekend Days");
                $stopped = true;
            }
        }

        // validate if an appropriate type is selected
        $notAllowedHoursType = !in_array($hrsType, $allowedHoursType);

        switch (true) {
            case $notAllowedHoursType:
                // hours type protection
                error_log("Not Allowed Hours Type found");
                if (!empty($callbackMessage)) {
                    throw new \Exception($callbackMessage);
                } else {
                    throw new \Exception("Not Allowed Hours Type found");
                }
                $stopped = true;
                break;
            default:
                break;
        }

        if ($stopped == false) {

            switch ($hrsType) {
                case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                    $effortDays = $weekendDays;
                    $bankHolidays = array();
                    if ($effortDays > 0) {
                        $hrsPerEffortDay = $hours / $effortDays;
                    } else {
                        $hrsPerEffortDay = $hours;
                    }
                    $dayOfWeek = 6;
                    $startDay = 'saturday';
                    $sdate = DateClass::adjustStartDate($sdate, $hrsType); // changed to next saturday
                    break;
                case resourceRequestRecord::HOURS_TYPE_REGULAR:
                case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                    $effortDays = $businessDays;
                    // $bankHolidays = $calculatedBusinessDays['bankHolidays'];
                    if ($effortDays > 0) {
                        $hrsPerEffortDay = $hours / $effortDays;
                    } else {
                        $hrsPerEffortDay = $hours;
                    }
                    $dayOfWeek = 1;
                    $startDay = 'monday';
                    $sdate = DateClass::adjustStartDate($sdate); // changed to next monday
                    break;
                default:
                    error_log("Invalid Hours Type found");
                    throw new \Exception("Invalid Hours Type found");
                    $stopped = true;
                    break;
            }

            $nextDate = clone $sdate;
            $endPeriod = $edate->format('oW');  // number of week
            $nextPeriod = $nextDate->format('oW');  // number of week
            
            $deleteExisting ? $this->clearResourceReference($resourceReference) : null;
    
            $oneWeek = new \DateInterval('P1W');
    
            $iteration = 1;
            while($nextPeriod <= $endPeriod) {
                
                // echo '<br/>';
                // echo ' iteration '.$iteration;
                // echo '<br/>----------------------------------------------';

                // echo '<br/>';
                // echo ' endPeriod '.$endPeriod;
                // echo '<br/>';
                // echo ' nextPeriod '.$nextPeriod;

                // echo '<br/>';
                // echo ' sdate '.$sdate->format('Y-m-d'); // 2021-06-12
                // echo '<br/>';
                // echo ' nextDate '.$nextDate->format('Y-m-d'); // 2021-06-12 / 2021-06-19 / 2021-06-26

                // echo '<br/>';
                // echo ' check 1 '.$nextDate->format('N'); // 6
                // echo '<br/>';
                // echo ' check 2 '.$dayOfWeek; // 6

                if($nextDate > $sdate && $nextDate->format('N') != $dayOfWeek){
                    // Once we're past the Start Date, get 'nextDate' to always be a Monday/Saturday
                    $nextDate->modify('previous ' . $startDay);
                }
                $resourceRequestHoursRecord = new resourceRequestHoursRecord();
                $resourceRequestHoursRecord->RESOURCE_REFERENCE = $resourceReference;
                $resourceRequestHoursRecord->DATE = $nextDate->format('Y-m-d');
                $resourceRequestHoursRecord->YEAR = $nextDate->format('o');
                $resourceRequestHoursRecord->WEEK_NUMBER = $nextDate->format('W');
    
                self::populateComplimentaryDateFields($nextDate, $resourceRequestHoursRecord);
                
                $resourceRequestHoursRecord->DATE = $resourceRequestHoursRecord->WEEK_ENDING_FRIDAY;
                $weelEndingFriday_Date = new \DateTime($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY);
                
                switch ($hrsType) {
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                        if($edate > $weelEndingFriday_Date){
                            $businessDaysInWeek = 2; // Includes whole weekend
                        } else {

                            // echo '<br/>';
                            // echo ' next_Date on '.$nextDate->format('d-M-Y');
                            
                            // echo '<br/>';
                            // echo ' next on '.$nextDate->format('N');
                            
                            // echo '<br/>';
                            // echo ' end_Date on '.$edate->format('d-M-Y');
                            
                            // echo '<br/>';
                            // echo ' ends on '.$edate->format('N');
                            
                            // echo '<br/>';
                            // echo ' weelEndingFriday_Date on '.$weelEndingFriday_Date->format('d-M-Y');
                            
                            switch ($edate->format('N')) {
                                case 6: // Ends on a Saturday
                                    $businessDaysInWeek = 1;
                                    break; 
                                case 7: // Ends on a Sunday
                                    $businessDaysInWeek = 2;
                                    break; 
                                default:
                                    if($nextPeriod < $endPeriod) {
                                        // Ends in the next period
                                        $businessDaysInWeek = 2;
                                    } else {
                                        // Ends before the weekend starts
                                        $businessDaysInWeek = 0;
                                    }
                                    break;
                            }
                        }  
                        break;
                    case resourceRequestRecord::HOURS_TYPE_REGULAR:
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                        $businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY, $bankHolidays, $sdate, $edate);
                        break;        
                    default:
                        $businessDaysInWeek = 0;
                        break;
                }
    
                if($businessDaysInWeek > 0){
                    
                    // echo '<br/>';
                    // echo ' businessDaysInWeek  '.$businessDaysInWeek;
                    // echo '<br/>';
                    // echo ' hrsPerEffortDay  '.$hrsPerEffortDay;

                    $businessHoursInWeek = $businessDaysInWeek * $hrsPerEffortDay;
                    $resourceRequestHoursRecord->HOURS = $businessHoursInWeek;
                    
                    $this->saveRecord($resourceRequestHoursRecord);
                    $weeksCreated++;
                } else {
    
                }
                
                $nextDate->add($oneWeek);
                $nextPeriod = $nextDate->format('oW');

                $iteration++;
            }
        }

        return $weeksCreated;
    }

    static function populateComplimentaryDateFields($date,$record){
        $complimentaryField = self::getDateComplimentaryFields($date);

        $record->WEEK_ENDING_FRIDAY = $complimentaryField['WEEK_ENDING_FRIDAY'];
        $record->CLAIM_CUTOFF = $complimentaryField['CLAIM_CUTOFF'];
        $record->CLAIM_MONTH = $complimentaryField['CLAIM_MONTH'];
        $record->CLAIM_YEAR = $complimentaryField['CLAIM_YEAR'];
    }

    static function getDateComplimentaryFields($date){

        $weekEndingFriday = DateClass::weekEnding($date->format('Y-m-d'));
        $claimCutoff  = DateClass::claimMonth($date->format('Y-m-d'));

        $complimentaryField['WEEK_ENDING_FRIDAY'] = $weekEndingFriday->format('Y-m-d');
        $complimentaryField['CLAIM_CUTOFF'] = $claimCutoff->format('Y-m-d');
        $complimentaryField['CLAIM_MONTH'] = $claimCutoff->format('m');
        $complimentaryField['CLAIM_YEAR'] = $claimCutoff->format('Y');

        return $complimentaryField;
    }

    function clearResourceReference($resourceReference=null){
        if($resourceReference){
            $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName ;
            $sql .= " WHERE RESOURCE_REFERENCE='" . htmlspecialchars($resourceReference) . "' ";
            $this->execute($sql);
            $this->commitUpdates();
        }
    }
    
    function returnHrsPerWeek($predicate= null, $rsOnly = false) {
            $sql = " SELECT RRH.RESOURCE_REFERENCE AS RR, 
                WEEK_ENDING_FRIDAY AS WEF, 
                HOURS, 
                RFS, 
                SERVICE,";
            $sql.= " ( CASE 
                WHEN CHARINDEX('" . resourceRequestTable::$duplicate . "', RESOURCE_NAME) != 0 THEN null
                WHEN CHARINDEX('" . resourceRequestTable::$delta . "', RESOURCE_NAME) != 0 THEN null
                ELSE RESOURCE_NAME
            END) AS RESOURCE_NAME, ";
            $sql.= " HOURS_TYPE, ";
            $sql.= " RR.RATE_TYPE,";
            $sql.= " RFS.RFS_END_DATE, ";
            $sql.= " CASE WHEN ";
            // All open RFS' RR
            $sql.= rfsTable::notArchivedPredicate('RFS');
            $sql.= " OR ";
            // All Archived RFS' RR where the RFS' End Date was in the past 12 months
            $sql.= resourceRequestTable::archivedInLast12MthsPredicate('RFS');
            $sql.= " THEN '' ELSE 'archived' END AS RR_ARCHIVED, ";
            $sql.= " RFS.ARCHIVE AS RFS_ARCHIVED ";
            $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RRH ";
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR  ";
            $sql.= " ON RRH.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE  ";  
            $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
            $sql.= " ON RR.RFS = RFS.RFS_ID ";
            $sql.= empty($predicate) ? null : " WHERE 1=1 AND " . $predicate;
            $sql.= " ORDER BY RRH.RESOURCE_REFERENCE, WEEK_ENDING_FRIDAY";
            
            $resultSet = $this->execute($sql);
            
            switch (true) {
                case $rsOnly:
                    return $resultSet;
                    break;
                case $resultSet:
                    $allData = array();
                    while($row = sqlsrv_fetch_array($resultSet)){
                        $allData[]  = array_map('trim',$row);
                    }
                    return $allData;                
                default:
                    return false;
                    break;
            }
        }

    function returnHrsPerWeekForExtract($rsOnly = false) {

        // The RR Extract should contain all Open RR along with all Archived RR
        // where the RFS_END_DATE is in the last 12 months.
        // Yeah, when an RFS is archived then all associated RR to that RFS should also be marked as Archived
        // not archived = open archived = closed

        $sql = " SELECT RRH.RESOURCE_REFERENCE AS RR, 
            WEEK_ENDING_FRIDAY AS WEF, 
            HOURS, 
            RFS, 
            SERVICE,";
        $sql.= " ( CASE 
            WHEN CHARINDEX('" . resourceRequestTable::$duplicate . "', RESOURCE_NAME) != 0 THEN null
            WHEN CHARINDEX('" . resourceRequestTable::$delta . "', RESOURCE_NAME) != 0 THEN null
            ELSE RESOURCE_NAME
        END) AS RESOURCE_NAME, ";
        $sql.= " HOURS_TYPE, ";
        $sql.= " RR.RATE_TYPE,";
        $sql.= " RFS.RFS_END_DATE, ";
        $sql.= " CASE WHEN ";
        // All open RFS' RR
        $sql.= rfsTable::notArchivedPredicate('RFS');
        $sql.= " OR ";
        // All Archived RFS' RR where the RFS' End Date was in the past 12 months
        $sql.= resourceRequestTable::archivedInLast12MthsPredicate('RFS');
        $sql.= " THEN '' ELSE 'archived' END AS RR_ARCHIVED, ";
        $sql.= " RFS.ARCHIVE AS RFS_ARCHIVED ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RRH ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR  ";
        $sql.= " ON RRH.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE  ";  
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= " ON RR.RFS = RFS.RFS_ID ";
        $sql.= " WHERE 1=1 ";
        $sql.= " AND (";
        // All open RFS' RR
        $sql.= rfsTable::notArchivedPredicate('RFS');
        $sql.= " OR ";
        // All Archived RFS' RR where the RFS' End Date was in the past 12 months 
        $sql.= resourceRequestTable::archivedInLast12MthsPredicate('RFS');
        $sql.= ")";
        $sql.= " ORDER BY RRH.RESOURCE_REFERENCE, WEEK_ENDING_FRIDAY";

        $resultSet = $this->execute($sql);
        
        switch (true) {
            case $rsOnly:
                return $resultSet;
                break;
            case $resultSet:
                $allData = array();
                while($row = sqlsrv_fetch_array($resultSet)){
                    $allData[]  = array_map('trim',$row);
                }
                return $allData;                
            default:
                return false;
                break;
        }
    }

    function returnHrsPerWeekForExtractWithArchived($rsOnly = false) {

        // The RR Extract should contain all Open RR along with all Archived RR
        // where the RFS_END_DATE is in the last 12 months.
        // Yeah, when an RFS is archived then all associated RR to that RFS should also be marked as Archived
        // not archived = open archived = closed

        $sql = " SELECT RRH.RESOURCE_REFERENCE AS RR, 
            WEEK_ENDING_FRIDAY AS WEF, 
            HOURS, 
            RFS, 
            SERVICE,";
        $sql.= " ( CASE 
            WHEN CHARINDEX('" . resourceRequestTable::$duplicate . "', RESOURCE_NAME) != 0 THEN null
            WHEN CHARINDEX('" . resourceRequestTable::$delta . "', RESOURCE_NAME) != 0 THEN null
            ELSE RESOURCE_NAME
        END) AS RESOURCE_NAME, ";
        $sql.= " HOURS_TYPE, ";
        $sql.= " RR.RATE_TYPE,";
        $sql.= " RFS.RFS_END_DATE, ";
        $sql.= " CASE WHEN ";
        // All open RFS' RR
        $sql.= rfsTable::notArchivedPredicate('RFS');
        $sql.= " OR ";
        // All open RFS' RR & All Archived RFS' RR where the RFS' End Date is on or after 01/01/2022
        $sql.= resourceRequestTable::archivedSince2022Predicate('RFS');
        $sql.= " THEN '' ELSE 'archived' END AS RR_ARCHIVED, ";
        $sql.= " RFS.ARCHIVE AS RFS_ARCHIVED ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RRH ";
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR  ";
        $sql.= " ON RRH.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE  ";  
        $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= " ON RR.RFS = RFS.RFS_ID ";
        $sql.= " WHERE 1=1 ";
        $sql.= " AND (";
        // All open RFS' RR
        $sql.= rfsTable::notArchivedPredicate('RFS');
        $sql.= " OR ";         
        // All open RFS' RR & All Archived RFS' RR where the RFS' End Date is on or after 01/01/2022
        $sql.= resourceRequestTable::archivedSince2022Predicate('RFS');
        $sql.= ")";
        $sql.= " ORDER BY RRH.RESOURCE_REFERENCE, WEEK_ENDING_FRIDAY";

        $resultSet = $this->execute($sql);
        
        switch (true) {
            case $rsOnly:
                return $resultSet;
                break;
            case $resultSet:
                $allData = array();
                while($row = sqlsrv_fetch_array($resultSet)){
                    $allData[]  = array_map('trim',$row);
                }
                return $allData;                
            default:
                return false;
                break;
        }
    }
    
    function returnAsArray($predicate=null,$selectableColumns='*', $assoc=false){
        $sql = " SELECT " . $selectableColumns;
        $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= !empty($predicate) ? " WHERE $predicate " : null;

        $resultSet = $this->execute($sql);

        $resultSet ? null : die("SQL Failed");

        $allData = null;

        if($assoc){
            while($row = sqlsrv_fetch_array($resultSet)){
                $allData[]  = $row;
            }
        }  else {
            while($row = sqlsrv_fetch_array($resultSet)){
                $allData[]  = $row;
            }
        }
        return $allData;
    }

    function getHoursRemainingByReference(){
        if($this->hoursRemainingByReference==null){
            $date = new \DateTime();
            $complimentaryFields = $this->getDateComplimentaryFields($date);
            $sql = " select RESOURCE_REFERENCE, SUM(HOURS) as HOURS_TO_GO, count(*) as WEEKS_TO_GO ";
            $sql.= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
            $sql.= " where WEEK_ENDING_FRIDAY > DATE('" . $complimentaryFields['WEEK_ENDING_FRIDAY'] ."') ";
            $sql.= " group by RESOURCE_REFERENCE; ";
            
            $rs = sqlsrv_query($GLOBALS['conn'], $sql);
            
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }
            
            while(($row = sqlsrv_fetch_array($rs))==true){
                $this->hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours'] = $row['HOURS_TO_GO'];
                $this->hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks'] = $row['WEEKS_TO_GO'];
             }
        }        
        return $this->hoursRemainingByReference;
    }

    static function removeHoursRecordsForRfsPriorToday($rfsId){
        
        $sql = " DELETE ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
        $sql.= " WHERE RESOURCE_REFERENCE IN ( ";
        $sql.= "      SELECT RESOURCE_REFERENCE ";
        $sql.= "      FROM " .  $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.= "      where RR.RFS = '" . htmlspecialchars($rfsId) . "' ";
        $sql.= "      ) ";
        $sql.= " AND DATE(WEEK_ENDING_FRIDAY) < CURRENT_TIMESTAMP ";
        
        error_log($sql);
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
               
        error_log("Db2_Num_Rows:" . db2_num_rows($rs));
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        return $rs;
    }

    function prepareSetHoursForWef(int $resourceReference, $data){  
       
        if(!isset($this->preparedSetHrsStatement)){
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql.= " SET HOURS= ? " ;
            $sql.= " WHERE DATE(WEEK_ENDING_FRIDAY) =  ? ";
            $sql.= " AND RESOURCE_REFERENCE= " . htmlspecialchars($resourceReference);   
            $this->preparedSetHrsStatement = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
            
            if(!$this->preparedSetHrsStatement){
                DbTable::displayErrorMessage($this->preparedSetHrsStatement, __CLASS__, __METHOD__, $sql);
            }
        }
              
        return $this->preparedSetHrsStatement ? $this->preparedSetHrsStatement : false;
    }

    function setHoursForWef(int $resourceReference, string $wef, float $hours){
        $parameters = array($hours, $wef);
        $preparedStmt = $this->prepareSetHoursForWef($resourceReference, $parameters);         
        
        $rs = sqlsrv_execute($preparedStmt);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
        }        
        return $rs ? true : false;        
    }

    function getArchieved($resourceReference=null){
        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE RESOURCE_REFERENCE = '" . htmlspecialchars($resourceReference) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return $rs;
    }
}