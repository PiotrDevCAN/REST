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
    function createResourceRequestHours($resourceReference=null, $startDate=null, $endDate=null, $hours=0, $deleteExisting=true, $hrsType=resourceRequestRecord::HOURS_TYPE_REGULAR){
        
        $weeksCreated = 0;
        $stopped = false;
        $callbackMessage = '';

        if ($resourceReference === null) {
            error_log("Invalid Resource Reference (" . $resourceReference . ")");
            throw new \Exception("Invalid Resource Reference (" . $resourceReference . ")");
            $stopped = true;
        }

        if ($startDate === null || self::validateDate($startDate) === false) {
            error_log("(" . $resourceReference . ") Invalid Start Date (" . $startDate . ")");
            throw new \Exception("(" . $resourceReference . ") Invalid Start Date (" . $startDate . ")");
            $stopped = true;
        }

        if ($endDate === null || self::validateDate($endDate) === false) {
            error_log("(" . $resourceReference . ") Invalid End Date (" . $endDate . ")");
            throw new \Exception("(" . $resourceReference . ") Invalid End Date (" . $endDate . ")");
            $stopped = true;
        }

        $sdate = new \DateTime($startDate);
        $edate = new \DateTime($endDate);

        if ($sdate > $edate) {
            error_log("(" . $resourceReference . ") Start Date (" . $startDate . ") and End Date (" . $endDate . ") are swapped");
            throw new \Exception("(" . $resourceReference . ") Start Date (" . $startDate . ") and End Date (" . $endDate . ") are swapped");
            $stopped = true;
        }

        if ($hours == 0) {
            error_log("(" . $resourceReference . ") Invalid Total Hours amount (" . $hours . ")");
            throw new \Exception("(" . $resourceReference . ") Invalid Total Hours amount (" . $hours . ")");
            $stopped = true;
        }

        $invalidHoursType = !in_array($hrsType, resourceRequestRecord::$allHourTypes);
        if ($invalidHoursType) {
            error_log("(" . $resourceReference . ") Invalid Hours Type found (" . $hrsType . ")");
            throw new \Exception("(" . $resourceReference . ") Invalid Hours Type found (" . $hrsType . ")");
            $stopped = true;
        }

        // get amount of days per type
        $weekendDays = DateClass::weekendDaysFromStartToEnd($sdate, $edate);

        $calculatedBusinessDays = DateClass::businessDaysFromStartToEnd($sdate, $edate);
        list(
            'businessDays' => $businessDays,
            'bankHolidays' => $bankHolidays,
            'bankHolidaysList' => $bankHolidaysList
        ) = $calculatedBusinessDays;

        // echo '<br/>';
        // echo "Start date " . $sdate->format('d-m-Y');        
        // error_log("Start date " . $sdate->format('d-m-Y'));
        // echo '<br/>';
        // error_log("End date " . $edate->format('d-m-Y'));
        // echo "End date " . $edate->format('d-m-Y');
        // echo '<br/>';
        // echo ' Amount of business days '.$businessDays;
        // error_log("Amount of business days ".$businessDays);
        // echo '<br/>';
        // echo ' Amount of weekend days '.$weekendDays;
        // error_log("Amount of weekend days ".$weekendDays);

        $allowedHoursType = array();

        /*
        *   Validation of Hours Type
        */
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
                $callbackMessage = '(' . $resourceReference . ') For selected period of time "'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_END.'" hours type must be chosen.';
            } elseif ($weekendDays == 0 && $businessDays > 0 ) {
                // If weekendhrs (or whatever it is called) is 0 they can choose regular or weekday overtime
                $allowedHoursType = array(
                    resourceRequestRecord::HOURS_TYPE_REGULAR,
                    resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY
                );
                $callbackMessage = '(' . $resourceReference . ') For selected period of time either "'.resourceRequestRecord::HOURS_TYPE_REGULAR.'" or "'.resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY.'" hours type must be chosen.';
            } else {
                if ($bankHolidays > 0) {
                    $allowedHoursType = array(
                        resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY
                    );
                } else {
                    error_log("Invalid Calculation Of Business Or Weekend Days");
                    throw new \Exception("(" . $resourceReference . ") Invalid Calculation Of Business Or Weekend Days");
                    $stopped = true;
                }
            }
        }

        /*
        *   validate if an appropriate type is selected
        */
        $notAllowedHoursType = !in_array($hrsType, $allowedHoursType);

        switch (true) {
            case $notAllowedHoursType:
                // hours type protection
                if (!empty($callbackMessage)) {
                    error_log($callbackMessage);
                    throw new \Exception($callbackMessage);
                } else {
                    error_log("(" . $resourceReference . ") Not Allowed Hours Type found (" . $hrsType . ") in " . serialize($allowedHoursType) );
                    throw new \Exception("(" . $resourceReference . ") Not Allowed Hours Type found (" . $hrsType . ") in " . serialize($allowedHoursType) );
                }
                $stopped = true;
                break;
            default:
                break;
        }

        /*
        *   The main part of calculation
        */
        if ($stopped == false) {

            $sdateInit = clone $sdate;
            
            // echo '<br/>';
            // echo ' start date INITIAL '.$sdate->format('Y-m-d'); // 2024-04-21

            switch ($hrsType) {
                // 'Weekend Overtime'
                case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                    $effortDays = $weekendDays;
                    if ($effortDays > 0) {
                        $hrsPerEffortDay = $hours / $effortDays;
                    } else {
                        $hrsPerEffortDay = $hours;
                    }
                    $dayOfWeek = 6;
                    $startDay = 'Saturday';
                    $sdate = DateClass::adjustStartDate($sdate, $hrsType); // changed to next Saturday
                    break;
                // 'Regular'
                // 'Weekday Overtime'
                case resourceRequestRecord::HOURS_TYPE_REGULAR:
                case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                    $effortDays = $businessDays;
                    if ($effortDays > 0) {
                        $hrsPerEffortDay = $hours / $effortDays;
                    } else {
                        $hrsPerEffortDay = $hours;
                    }
                    $dayOfWeek = 1;
                    $startDay = 'Monday';
                    $sdate = DateClass::adjustStartDate($sdate); // changed to next Monday
                    break;
                default:
                    break;
            }

            $nextDate = clone $sdate;
            $nextPeriod = $nextDate->format('oW');  // number of week
            $endPeriod = $edate->format('oW');  // number of week
            
            $deleteExisting ? $this->clearResourceReference($resourceReference) : null;
    
            $oneWeek = new \DateInterval('P1W');
    
            $iteration = 1;
            while($nextPeriod <= $endPeriod) {
                
                // echo '<br/>';
                // echo ' <b>iteration '.$iteration. ' </b>';
                // echo '<br/>----------------------------------------------';

                // echo '<br/>';
                // echo ' endPeriod '.$endPeriod;
                // echo '<br/>';
                // echo ' nextPeriod '.$nextPeriod;

                // echo '<br/>';
                // echo ' start date ADJUSTED '.$sdate->format('Y-m-d'); // 2024-04-21
                // echo '<br/>';
                // echo ' next date '.$nextDate->format('Y-m-d'); // 2024-04-21 / 2024-04-28
                // echo '<br/>';
                // echo ' end date '.$edate->format('Y-m-d'); // 2024-04-21
                // echo '<br/>----------------------------------------------';

                // echo '<br/>';
                // echo ' check 1 - DAY OF WEEK '.$dayOfWeek; // 6
                // echo '<br/>';
                // echo ' check 2 - START DATE '.$sdate->format('N'); // 6
                // echo '<br/>';
                // echo ' check 3 - NEXT DATE '.$nextDate->format('N'); // 6
                // echo '<br/>';
                // echo ' check 4 - END DATE '.$edate->format('N'); // 6
                
                if($nextDate > $sdate && $nextDate->format('N') != $dayOfWeek){
                    // Once we're past the Start Date, get 'nextDate' to always be a Monday/Saturday
                    $nextDate->modify('previous ' . $startDay);
                    // echo '<br/>';
                    // echo ' <b>ENTERS HERE - get back to previous '.$startDay.'</b>';
                    // echo '<br/>----------------------------------------------';
                }
                // echo '<br/>';
                // echo ' NEW NEXT DATE '.$nextDate->format('Y-m-d'); // 2024-04-21 / 2024-04-28
                // echo '<br/>';
                // echo ' NEW NEXT DATE NUMBER '.$nextDate->format('N'); // 6
                // echo '<br/>----------------------------------------------';

                $resourceRequestHoursRecord = new resourceRequestHoursRecord();
                $resourceRequestHoursRecord->RESOURCE_REFERENCE = $resourceReference;
                $resourceRequestHoursRecord->DATE = $nextDate->format('Y-m-d');
                $resourceRequestHoursRecord->YEAR = $nextDate->format('o');
                $resourceRequestHoursRecord->WEEK_NUMBER = $nextDate->format('W');
    
                self::populateComplimentaryDateFields($nextDate, $resourceRequestHoursRecord);
                
                $resourceRequestHoursRecord->DATE = $resourceRequestHoursRecord->WEEK_ENDING_FRIDAY;
                $weekEndingFriday_Date = new \DateTime($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY);
                
                // echo '<br/>';
                // echo ' WEEK ENDING FRIDAY '.$weekEndingFriday_Date->format('Y-m-d'); // 2024-04-21
                // echo '<br/>';
                // echo ' check 6 - WEEK ENDING FRIDAY '.$weekEndingFriday_Date->format('N'); // 6

                switch ($hrsType) {
                    // 'Weekend Overtime'
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                        if($edate > $weekEndingFriday_Date) {
                            if($iteration == 1) {
                                switch ($sdateInit->format('N')) {
                                    case 6: // Starts on a Saturday
                                        $businessDaysInWeek = 2;
                                        break;
                                    case 7: // Ends on a Sunday
                                        $businessDaysInWeek = 1;
                                        break; 
                                    default:
                                        if($nextPeriod < $endPeriod) {
                                            // Starts in the next period
                                            $businessDaysInWeek = 2;
                                        } else {
                                            // Starts before the weekend starts
                                            $businessDaysInWeek = 0;
                                        }
                                        break;
                                }
                            } else {
                                $businessDaysInWeek = 2; // Includes whole weekend
                            }
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
                            // echo ' weekEndingFriday_Date on '.$weekEndingFriday_Date->format('d-M-Y');
                            
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
                    // 'Regular'
                    case resourceRequestRecord::HOURS_TYPE_REGULAR:
                        $businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY, $bankHolidaysList, $sdate, $edate);
                        break;
                    // 'Weekday Overtime'
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                        $businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($resourceRequestHoursRecord->WEEK_ENDING_FRIDAY, $bankHolidaysList, $sdate, $edate, false);
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

                    $saved = $this->saveRecord($resourceRequestHoursRecord);
                    if ($saved !== null) {
                        $weeksCreated++;
                    }
                } else {
                    
                }
                
                $nextDate->add($oneWeek);
                $nextPeriod = $nextDate->format('oW');

                $iteration++;
            }
        }

        return $weeksCreated;
    }

    static function getDateComplimentaryFields($date){

        $weekEndingFriday = DateClass::weekEnding($date->format('Y-m-d'));
        $claimCutoff  = DateClass::claimMonth($date->format('Y-m-d'));

        $complimentaryField = array(
            'WEEK_ENDING_FRIDAY' => $weekEndingFriday->format('Y-m-d'),
            'CLAIM_CUTOFF' => $claimCutoff->format('Y-m-d'),
            'CLAIM_MONTH' => $claimCutoff->format('m'),
            'CLAIM_YEAR' => $claimCutoff->format('Y')
        );

        return $complimentaryField;
    }

    static function populateComplimentaryDateFields($date, $record){
        $complimentaryFields = self::getDateComplimentaryFields($date);
        list(
            'WEEK_ENDING_FRIDAY' => $wef, 
            'CLAIM_CUTOFF' => $claimCutOff,
            'CLAIM_MONTH' => $claimMonth,
            'CLAIM_YEAR' => $claimYear
        ) = $complimentaryFields;

        $record->WEEK_ENDING_FRIDAY = $wef;
        $record->CLAIM_CUTOFF = $claimCutOff;
        $record->CLAIM_MONTH = $claimMonth;
        $record->CLAIM_YEAR = $claimYear;
    }

    function clearResourceReference($resourceReference=null){
        if($resourceReference){
            $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName ;
            $sql .= " WHERE RESOURCE_REFERENCE='" . htmlspecialchars($resourceReference) . "' ";
            $this->execute($sql);
        }
    }
    
    function returnHrsPerWeek($predicate= null, $rsOnly = false) {
        
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
        $sql.= empty($predicate) ? null : " WHERE 1=1 AND " . $predicate;
        $sql.= " ORDER BY RRH.RESOURCE_REFERENCE, WEEK_ENDING_FRIDAY";
        
        $resultSet = $this->execute($sql);
        
        switch (true) {
            case $rsOnly:
                return $resultSet;
                break;
            case $resultSet:
                $allData = array();
                while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                    $allData[]  = array_map('trim', $row);
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
                while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                    $allData[]  = array_map('trim', $row);
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
        $sql.= " RFS.RFS_START_DATE, ";
        $sql.= " RFS.RFS_END_DATE, ";
        $sql.= " RR.DESCRIPTION, ";
        $sql.= " RFS.RFS_TYPE, ";
        $sql.= " RFS.PROJECT_TITLE, ";
        $sql.= " RFS.REQUESTOR_NAME, ";
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
                while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                    $allData[]  = array_map('trim', $row);
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
            while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                $allData[]  = $row;
            }
        }  else {
            while($row = sqlsrv_fetch_array($resultSet, SQLSRV_FETCH_ASSOC)){
                $allData[]  = $row;
            }
        }
        return $allData;
    }

    function getHoursRemainingByReference(){

        $allData = array();
        
        $redis = $GLOBALS['redis'];
        $key = 'getHoursRemainingByReference';
        $redisKey = md5($key.'_key_'.$_ENV['environment']);
        if (!$redis->get($redisKey)) {
            $source = 'SQL Server';

            $sql = " select RESOURCE_REFERENCE, SUM(CAST(HOURS as decimal(6,2))) as HOURS_TO_GO, count(*) as WEEKS_TO_GO ";
            $sql.= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
            $sql.= " where WEEK_ENDING_FRIDAY > ? ";
            $sql.= " group by RESOURCE_REFERENCE; ";

            // this week ending date
            $date = new \DateTime();
            $complimentaryFields = $this->getDateComplimentaryFields($date);
            list(
                'WEEK_ENDING_FRIDAY' => $wef
            ) = $complimentaryFields;

            $data = array($wef);
            $rs = sqlsrv_query($GLOBALS['conn'], $sql, $data);
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }

            $hoursRemainingByReference = array();
            while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
                $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['hours'] = $row['HOURS_TO_GO'];
                $hoursRemainingByReference[$row['RESOURCE_REFERENCE']]['weeks'] = $row['WEEKS_TO_GO'];
            }

            $redis->set($redisKey, json_encode($hoursRemainingByReference));
            $redis->expire($redisKey, REDIS_EXPIRE);
        } else {
            $source = 'Redis Server';
            $hoursRemainingByReference = json_decode($redis->get($redisKey), true);
        }

        $allData['data'] = $hoursRemainingByReference;
        $allData['source'] = $source;
        
        return $allData;
    }

    static function removeHoursRecordsForRfsPriorToday($rfsId){
        
        $sql = " DELETE ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
        $sql.= " WHERE RESOURCE_REFERENCE IN ( ";
        $sql.= " SELECT RESOURCE_REFERENCE ";
        $sql.= " FROM " .  $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
        $sql.= " where RR.RFS = '" . htmlspecialchars($rfsId) . "' ";
        $sql.= " ) ";
        $sql.= " AND WEEK_ENDING_FRIDAY < GETDATE() ";
        
        error_log($sql);
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        error_log("Db2_Num_Rows:" . db2_num_rows($rs));
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        return $rs;
    }

    function prepareSetHoursForWef($data){  
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET HOURS = ? " ;
        $sql.= " WHERE WEEK_ENDING_FRIDAY = ? ";
        $sql.= " AND RESOURCE_REFERENCE = ? ";
        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        
        if(!$preparedStmt){
            DbTable::displayErrorMessage($preparedStmt, __CLASS__, __METHOD__, $sql);
        }

        return $preparedStmt;
    }

    function setHoursForWef(int $resourceReference, string $wef, float $hours){
        $data = array($hours, $wef, $resourceReference);
        $stmt = $this->prepareSetHoursForWef($data);         
        
        $result = sqlsrv_execute($stmt);
        
        if(!$result){
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, 'prepared sql');
        }        
        return $result;        
    }

    function getArchieved($resourceReference=null){
        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE RESOURCE_REFERENCE = '" . htmlspecialchars($resourceReference) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return $rs;
    }
}