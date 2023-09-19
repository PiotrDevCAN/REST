<?php
namespace rest\traits;

use itdq\DbTable;
use itdq\DateClassSaturday as DateClass;
// use itdq\DateClass;
use rest\allTables;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;

trait resourceRequestHoursTableTrait
{
    private $preparedGetTotalHrsStatement;
    private $preparedSetHrsStatement;
    private $hoursRemainingByReference;
    
    function createResourceRequestHours($resourceReference=null, $startDate=null, $endDate=null, $hours=0, $deleteExisting=true, $hrsType=resourceRequestRecord::HOURS_TYPE_REGULAR, $trace = false, $saveHours = true){
        
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

        $dateDiff = $edate->diff($sdate)->days;
        $totalDays = $dateDiff+1;
        $weeks = (int)($totalDays / 7);

        if ($trace) {
            echo "<br/>----------------------------------------------";
            echo "<br/><b>Inital parameters check up</b>";
            echo "<br/>----------------------------------------------";
            echo '<br/>';
            echo ' Resource Reference: '.$resourceReference;
            echo '<br/>';
            echo ' Hours: '.$hours;
            echo '<br/>';
            echo ' Hours Type: '.$hrsType;
            echo '<br/>----------------------------------------------';
            echo "<br/>Start: <b>" . $sdate->format('d-M-Y') . "</b>";
            echo "<br/>End: <b>" . $edate->format('d-M-Y') . "</b>";
        }

        /*
        * get amount of days per type
        */
        ob_start();
        $allTypesDaysData = DateClass::getAllTypesDaysFromStartToEnd($sdate, $edate, $trace);
        list(
            'workingDays'=> $workingDays,
            'workingDaysDates'=> $workingDaysDates,
            'businessDays' => $businessDays,
            'businessDaysDates' => $businessDaysDates,
            'bankHolidays' => $bankHolidays,
            'bankHolidaysDates' => $bankHolidaysDates,
            'saturdayDays'=> $saturdayDays,
            'saturdayDaysDates'=> $saturdayDaysDates,
            'sundayDays' => $sundayDays,
            'sundayDaysDates' => $sundayDaysDates,
            'weekendDays' => $weekendDays,
            'weekendDaysDates' => $weekendDaysDates
        ) = $allTypesDaysData;
        $messages = ob_get_clean();
        
        if ($trace) {
            echo '<br/>----------------------------------------------';
            echo '<br/>';
            echo ' Amount of total days: '.$totalDays;
            echo '<br/>';
            echo ' Amount of weeks: '.$weeks;
            echo '<br/>';
            echo ' Amount of working days: '.$workingDays;
            echo '<br/>';
            echo ' Amount of business days: '.$businessDays;
            echo '<br/>';
            echo ' Amount of bank holiday days: '.$bankHolidays;
            echo '<br/>';
            echo ' Amount of Saturdays: '.$saturdayDays;
            echo '<br/>';
            echo ' Amount of Sundays: '.$sundayDays;
            echo '<br/>';
            echo ' Amount of weekend days: '.$weekendDays;
            echo '<br/>----------------------------------------------';
        }

        ob_start();
        $allowedHoursType = array();

        /*
        if ($businessDays > 0) {
            if ($weekendDays > 0) {
                // all type are correct
                $allowedHoursType = array(
                    resourceRequestRecord::HOURS_TYPE_OT_WEEK_END,
                    resourceRequestRecord::HOURS_TYPE_REGULAR,
                    resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY
                );
                $callbackMessage = 'For selected period of time all hours types are applicable.';
            } else {
                // $weekendDays = 0
                // If weekendhrs (or whatever it is called) is 0 they can choose regular or weekday overtime
                $allowedHoursType = array(
                    resourceRequestRecord::HOURS_TYPE_REGULAR,
                    resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY
                );
                $callbackMessage = 'For selected period of time either "'.resourceRequestRecord::HOURS_TYPE_REGULAR.'" or "'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY.'" hours type must be choosen.';
            }
        } else {
            // $businessDays = 0
            if ($weekendDays > 0) {
                // If businesshrs is 0 then they must choose Weekend Overtime
                $allowedHoursType = array(
                    resourceRequestRecord::HOURS_TYPE_OT_WEEK_END
                );
                $callbackMessage = 'For selected period of time "'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_END.'" hours type must be choosen.';
            } else {
                // $weekendDays = 0
                $callbackMessage = 'Invalid Calculation Of Business Or Weekend Days';
                error_log($callbackMessage);
                throw new \Exception($callbackMessage);
                $stopped = true;
            }
        }
        */

        if ($businessDays > 0) {
            // Mon - Fri
            if (!in_array(resourceRequestRecord::HOURS_TYPE_REGULAR, $allowedHoursType)) {
                array_push($allowedHoursType, resourceRequestRecord::HOURS_TYPE_REGULAR);
            }
        }
        if ($saturdayDays > 0) {
            // Sat
            if (!in_array(resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY, $allowedHoursType)) {
                array_push($allowedHoursType, resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY);
            }
        }
        if ($sundayDays > 0) {
            // Sun
            if (!in_array(resourceRequestRecord::HOURS_TYPE_OT_WEEK_END, $allowedHoursType)) {
                array_push($allowedHoursType, resourceRequestRecord::HOURS_TYPE_OT_WEEK_END);
            }
        }

        if (!empty($allowedHoursType)) {
            $count = count($allowedHoursType);
            switch($count) {
                case 1:
                    $callbackMessage = 'For selected period of time <b>"'.$allowedHoursType[0].'"</b> hours type must be choosen.';
                    break;
                case 2:                
                    $callbackMessage = 'For selected period of time either <b>"'.$allowedHoursType[0].'"</b> or <b>"'.$allowedHoursType[1].'"</b> hours type must be choosen.';
                    break;
                case 3:
                    $callbackMessage = 'For selected period of time all hours types can be choosen.';
                    break;
                default:
                    break;
            }
        } else {
            error_log("Invalid Calculation Of Business Or Weekend Days");
            throw new \Exception("Invalid Calculation Of Business Or Weekend Days");
            $stopped = true;
        }

        // validate if an appropriate type is selected
        $notAllowedHoursType = !in_array($hrsType, $allowedHoursType);
        
        try {
            switch (true) {
                case $notAllowedHoursType:
                    // hours type protection
                    error_log("Not Allowed Hours Type found");
                    $stopped = true;
                    if (!empty($callbackMessage)) {
                        throw new \Exception($callbackMessage);
                    } else {
                        throw new \Exception("Not Allowed Hours Type found");
                    }
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            $summary = ob_get_clean();
        }
        
        if ($stopped == false) {

            switch ($hrsType) {
                case resourceRequestRecord::HOURS_TYPE_REGULAR:
                    if ($trace) {
                        echo '<b>'.resourceRequestRecord::HOURS_TYPE_REGULAR.'</b> hours type selected therefore take amount of <b>Business Days</b> as Effort Days';
                    }
                    $hrsPerEffortDay = self::getHoursPerEffortDay($hours, $businessDays, $trace);
                    break;
                case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                    if ($trace) {
                        echo '<b>'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY.'</b> hours type selected therefore take amount of <b>Saturday Days</b> as Effort Days';
                    }
                    $hrsPerEffortDay = self::getHoursPerEffortDay($hours, $saturdayDays, $trace);
                    break;
                case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                    if ($trace) {
                        echo '<b>'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_END.'</b> hours type selected therefore take amount of <b>Sunday Days</b> as Effort Days';
                    }
                    $hrsPerEffortDay = self::getHoursPerEffortDay($hours, $sundayDays, $trace);
                    break;
                default:
                    error_log("Invalid Hours Type found");
                    throw new \Exception("Invalid Hours Type found");
                    break;
            }

            // change to next Monday, Saturday or Sunday

            // If it's not a Saturday then roll forward to next Saturday  - Weekday Overtime
            // If it's not a Sunday then roll forward to next Sunday      - Weekend Overtime
            // If it's a Saturday/Sunday, roll forward to the next Monday - Regular
            
            // Moves the start date to appropriate day by selected Hours Type

            $sdateData = DateClass::adjustStartDate($sdate, $hrsType, $trace);
            list(
                'dayOfWeek' => $dayOfWeek,
                'startDay' => $startDay,
                'adjustedDate' => $sdate
            ) = $sdateData;

            $deleteExisting ? $this->clearResourceReference($resourceReference) : null;
                
            $nextDate = clone $sdate;
            $endPeriod = $edate->format('oW');  // number of week
            $nextPeriod = $nextDate->format('oW');  // number of week
    
            $oneWeek = new \DateInterval('P1W');
    
            $iteration = 1;
            while($nextPeriod <= $endPeriod) {
                
                if ($trace) {
                    echo '<br/>----------------------------------------------';
                    echo '<br/>';
                    echo ' <b>Iteration no.'.$iteration.' vel Hour Type record</b>';
                    
                    // echo '<br/>----------------------------------------------';
                    // echo '<br/>';
                    // echo ' Initial USER Start Date '.$sdate->format('Y-m-d');
                    // echo '<br/>';
                    // echo ' Initial USER End Date '.$edate->format('Y-m-d');
                    // echo '<br/>';
                    // echo ' Next USER Start Date '.$nextDate->format('Y-m-d');

                    echo '<br/>----------------------------------------------';
                    echo '<br/>';
                    echo ' endPeriod '.$endPeriod;
                    echo '<br/>';
                    echo ' nextPeriod '.$nextPeriod;

                    // echo '<br/>';
                    // echo ' check 1 '.$nextDate->format('N'); // 6
                    // echo '<br/>';
                    // echo ' check 2 '.$dayOfWeek; // 6
                }

                if($nextDate > $sdate && $nextDate->format('N') != $dayOfWeek){
                    // Once we're past the Start Date, get 'nextDate' to always be a Monday/Saturday
                    $nextDate->modify('previous ' . $startDay);
                }
                $resourceRequestHoursRecord = new resourceRequestHoursRecord();
                $resourceRequestHoursRecord->RESOURCE_REFERENCE = $resourceReference;
                $resourceRequestHoursRecord->DATE = $nextDate->format('Y-m-d');
                $resourceRequestHoursRecord->YEAR = $nextDate->format('o');
                $resourceRequestHoursRecord->WEEK_NUMBER = $nextDate->format('W');

                // add Complimentary Date Fields 
                // DATE, WEEK_ENDING_FRIDAY, CLAIM_CUTOFF, CLAIM_MONTH, CLAIM_YEAR
                self::populateComplimentaryDateFields($nextDate, $resourceRequestHoursRecord, $trace);

                $weelEnding_Date = new \DateTime($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY);
                
                switch ($hrsType) {
                    case resourceRequestRecord::HOURS_TYPE_REGULAR:
                        $businessDaysInWeek = DateClass::businessDaysForWeekEnding($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY, $bankHolidaysDates, $sdate, $edate);
                        break;
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                        $businessDaysInWeek = DateClass::otherDaysForWeekEnding($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY, $dayOfWeek, $sdate, $edate, $nextPeriod, $endPeriod);
                        break;
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                        $businessDaysInWeek = DateClass::otherDaysForWeekEnding($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY, $dayOfWeek, $sdate, $edate, $nextPeriod, $endPeriod);
                        break;
                    default:
                        $businessDaysInWeek = 0;
                        break;
                }
                
                if ($trace) {
                    echo '<br/>';
                    echo '<b>WEF date:</b> '.$weelEnding_Date->format('Y-m-d');
                }

                if($businessDaysInWeek > 0){
                    
                    $businessHoursInWeek = $businessDaysInWeek * $hrsPerEffortDay;
                    $resourceRequestHoursRecord->HOURS = $businessHoursInWeek;

                    if ($trace) {
                        echo '<br/>';
                        echo '<b>businessDaysInWeek:</b> '.$businessDaysInWeek;
                        echo '<br/>';
                        echo '<b>hrsPerEffortDay:</b> '.$hrsPerEffortDay;
                        echo '<br/>';
                        echo '<b>businessHoursInWeek:</b> '.$businessHoursInWeek;
                    }
                    
                    if ($trace) {
                        // echo '<pre>';
                        // $resourceRequestHoursRecord->iterateVisible();
                        // echo '</pre>';
                    }

                    $saveHours ? $this->saveRecord($resourceRequestHoursRecord) : null;
                    $weeksCreated++;
                } else {
    
                }
                
                // go to the next week
                $nextDate->add($oneWeek);
                $nextPeriod = $nextDate->format('oW');  // number of week

                $iteration++;
            }

            $summary = ob_get_clean();
        }

        return array(
            'messages' => $messages,
            'summary' => $summary,
            'weeksCreated' => $weeksCreated
        );
    }

    static function populateComplimentaryDateFields($date, $record, $trace = false){
        $complimentaryField = self::getDateComplimentaryFields($date);

        if ($trace) {
            // echo '<pre>';
            // var_dump($complimentaryField);
            // echo '</pre>';
        }

        $record->DATE = $complimentaryField['WEEK_ENDING_FRIDAY'];
        $record->WEEK_ENDING_FRIDAY = $complimentaryField['WEEK_ENDING_FRIDAY'];
        $record->CLAIM_CUTOFF = $complimentaryField['CLAIM_CUTOFF'];
        $record->CLAIM_MONTH = $complimentaryField['CLAIM_MONTH'];
        $record->CLAIM_YEAR = $complimentaryField['CLAIM_YEAR'];
    }

    static function getHoursPerEffortDay($hours, $effortDays, $trace){    
        if ($effortDays > 0) {
            $hrsPerEffortDay = $hours / $effortDays;
        } else {
            $hrsPerEffortDay = $hours;
        }

        if ($trace) {
            echo '<br/>';
            echo ' <b>Ammount of Effort Days: '.$effortDays.'</b>';
            echo '<br/>';
            echo ' <b>Calculated hours per Effort Day: '.$hrsPerEffortDay.'</b>';
        }

        return $hrsPerEffortDay;
    }

    static function getDateComplimentaryFields($date){

        $weekEndingFriday = DateClass::weekEndingFriday($date->format('Y-m-d'));
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
        $sql = " select RRH.RESOURCE_REFERENCE as RR, WEEK_ENDING_FRIDAY as WEF, HOURS, RFS, SERVICE,";
        $sql.= " ( CASE 
            WHEN CHARINDEX('" . resourceRequestTable::$duplicate . "', RESOURCE_NAME) != 0 THEN null
            WHEN CHARINDEX('" . resourceRequestTable::$delta . "', RESOURCE_NAME) != 0 THEN null
            ELSE RESOURCE_NAME
        END) AS RESOURCE_NAME, ";
        $sql.= " HOURS_TYPE ";
        $sql.= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RRH ";
        $sql.= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR  ";
        $sql.= " on RRH.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE  ";
        $sql.= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql.= " on RR.RFS = RFS.RFS_ID ";
        $sql.= empty($predicate) ? null : " WHERE 1=1 AND " . $predicate;
        $sql.= " order by RRH.RESOURCE_REFERENCE, WEEK_ENDING_FRIDAY";
        
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

        $allData = array();

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
            $sql = " select RESOURCE_REFERENCE, SUM(CAST(HOURS as decimal(6,2))) as HOURS_TO_GO, count(*) as WEEKS_TO_GO ";
            $sql.= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
            $sql.= " where WEEK_ENDING_FRIDAY > DATE('" . $complimentaryFields['WEEK_ENDING_FRIDAY'] ."') ";
            $sql.= " group by RESOURCE_REFERENCE; ";
            
            $rs = sqlsrv_query($GLOBALS['conn'], $sql);
            
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }
            
            while($row = sqlsrv_fetch_array($rs)){
                $this->hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours'] = $row['HOURS_TO_GO'];
                $this->hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks'] = $row['WEEKS_TO_GO'];
             }
        }        
        return $this->hoursRemainingByReference;
    }

    static function removeHoursRecordsForRfsPriorToday($rfsId){
        
        $sql = " DELETE ";
        $sql.= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
        $sql.= " WHERE RESOURCE_REFERENCE IN ( ";
        $sql.= "      SELECT RESOURCE_REFERENCE ";
        $sql.= "      from " .  $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.= "      where RR.RFS = '" . htmlspecialchars($rfsId) . "' ";
        $sql.= "      ) ";
        $sql.= " AND WEEK_ENDING_FRIDAY < GETDATE() ";
        
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
            $sql.= " WHERE WEEK_ENDING_FRIDAY =  ? ";
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