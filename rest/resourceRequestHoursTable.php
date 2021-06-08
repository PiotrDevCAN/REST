<?php
namespace rest;

use itdq\DbTable;
use itdq\DateClass;

class resourceRequestHoursTable extends DbTable
{
    use tableTrait;

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
        $businessDays = $calculatedBusinessDays['businessDays'];

        $bankHolidays = array();
        $allowedHoursType = array();

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
            }
            elseif ($weekendDays == 0 && $businessDays > 0 ) {
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
                    $hrsPerEffortDay = $hours / $effortDays;
                    $dayOfWeek = 6;
                    $startDay = 'saturday';
                    $sdate = DateClass::adjustStartDate($sdate, $hrsType);
                    break;
                case resourceRequestRecord::HOURS_TYPE_REGULAR:
                case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                    $effortDays = $businessDays;
                    $bankHolidays = $calculatedBusinessDays['bankHolidays'];
                    if ($effortDays > 0) {
                        $hrsPerEffortDay = $hours / $effortDays;
                    } else {
                        $hrsPerEffortDay = $hours;
                    }
                    $dayOfWeek = 1;
                    $startDay = 'monday';
                    $sdate = DateClass::adjustStartDate($sdate);
                    break;
                default:
                    error_log("Invalid Hours Type found");
                    throw new \Exception("Invalid Hours Type found");
                    $stopped = true;
                    break;
            }

            $nextDate = clone $sdate;
            $endPeriod = $edate->format('oW');
            $nextPeriod = $nextDate->format('oW');
            
            $deleteExisting ? $this->clearResourceReference($resourceReference) : null;
    
            $oneWeek = new \DateInterval('P1W');
    
            while($nextPeriod <= $endPeriod){
                           
                if($nextDate > $sdate && $nextDate->format('N') != $dayOfWeek){
                    // Once we're past the Start Date, get 'nextDate' to always be a Monday/Saturday
                    $nextDate->modify('previous ' . $startDay);
                }
                $resourceRequestHours = new resourceRequestHoursRecord();
                $resourceRequestHours->RESOURCE_REFERENCE = $resourceReference;
                $resourceRequestHours->DATE = $nextDate->format('Y-m-d');
                $resourceRequestHours->YEAR = $nextDate->format('o');
                $resourceRequestHours->WEEK_NUMBER = $nextDate->format('W');
    
                self::populateComplimentaryDateFields($nextDate, $resourceRequestHours);
                
                $resourceRequestHours->DATE = $resourceRequestHours->WEEK_ENDING_FRIDAY;
                $wefDate = new \DateTime($resourceRequestHours->WEEK_ENDING_FRIDAY);
                
                switch ($hrsType) {
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_END:
                        if($edate > $wefDate){
                            $businessDaysInWeek = 2; // Includes whole weekend
                        } else {
                            switch ($edate->format('N')) {
                                case 6: // Ends on a Saturday
                                    $businessDaysInWeek = 1;
                                    break; 
                                case 7: // Ends on a Sunday
                                    $businessDaysInWeek = 2;
                                    break; 
                                default:
                                    // Ends before the weekend starts
                                    $businessDaysInWeek = 0;
                                    break;
                            }
                        }  
                        break;
                    case resourceRequestRecord::HOURS_TYPE_REGULAR:
                    case resourceRequestRecord::HOURS_TYPE_OT_WEEK_DAY:
                        $businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($resourceRequestHours->WEEK_ENDING_FRIDAY, $bankHolidays, $sdate, $edate);
                        break;        
                    default:
                        $businessDaysInWeek = 0;
                        break;
                }
    
                if($businessDaysInWeek > 0){
                    $businessHoursInWeek = $businessDaysInWeek * $hrsPerEffortDay;
                    $resourceRequestHours->HOURS = $businessHoursInWeek;
                    
                    $this->saveRecord($resourceRequestHours);
                    $weeksCreated++;
                } else {
    
                }
                
                $nextDate->add($oneWeek);
                $nextPeriod = $nextDate->format('oW');
            }
        }

        return $weeksCreated;
    }

    static function populateComplimentaryDateFields($date,$resourceHoursRecord){
        $complimentaryField = self::getDateComplimentaryFields($date);

        $resourceHoursRecord->WEEK_ENDING_FRIDAY = $complimentaryField['WEEK_ENDING_FRIDAY'];
        $resourceHoursRecord->CLAIM_CUTOFF = $complimentaryField['CLAIM_CUTOFF'];
        $resourceHoursRecord->CLAIM_MONTH = $complimentaryField['CLAIM_MONTH'];
        $resourceHoursRecord->CLAIM_YEAR = $complimentaryField['CLAIM_YEAR'];
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
            $sql .= " WHERE RESOURCE_REFERENCE='" . db2_escape_string($resourceReference) . "' ";
            $this->execute($sql);
            $this->commitUpdates();
        }
    }
    
    function returnHrsPerWeek($predicate= null, $rsOnly = false) {
        $sql = " select * ";
        $sql.= " from ( ";
        $sql.= " select RRH.RESOURCE_REFERENCE as RR, WEEK_ENDING_FRIDAY as WEF, HOURS, RFS, SERVICE, RESOURCE_NAME,  HOURS_TYPE ";
        $sql.= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS . " AS RRH ";
        $sql.= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " as RR  ";
        $sql.= " on RRH.RESOURCE_REFERENCE = RR.RESOURCE_REFERENCE  ";
        $sql.= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " as RFS ";
        $sql.= " on RR.RFS = RFS.RFS_ID ";
        $sql.= empty($predicate) ? null : " WHERE 1=1 AND " . $predicate;
        $sql.= " ) ";
        $sql.= " order by 1,2"; 
        
        $resultSet = $this->execute($sql);
        
        switch (true) {
            case $rsOnly:
                return $resultSet;
                break;
            case $resultSet:
                $allData = array();
                while(($row = db2_fetch_assoc($resultSet))==true){
                    $allData[]  = array_map('trim',$row);
                }
                return $allData;                
            default:
                return false;     ;
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
            while(($row = db2_fetch_assoc($resultSet))==true){
                $allData[]  = $row;
            }
        }  else {
            while(($row = db2_fetch_array($resultSet))==true){
                $allData[]  = $row;
            }
        }
        return $allData;
    }

//     function prepareGetTotalHoursStatement(){
//         if(!isset($this->preparedGetTotalHrsStatement)){
//             $sql = " SELECT SUM(HOURS) as TOTAL_HRS ";
//             $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." .  $this->tableName;
//             $sql .= " WHERE RESOURCE_REFERENCE=?";
//             $this->preparedSelectSQL = $sql;
//             $this->preparedGetTotalHrsStatement = db2_prepare($GLOBALS['conn'], $sql);

//         }
//         return $this->preparedGetTotalHrsStatement;
//     }

//     function getTotalHoursForRequest($resourceReference=null){
//         if(empty($resourceReference)){
//             return false;
//         }

//         $preparedStmt = $this->prepareGetTotalHoursStatement();
//         $data = array($resourceReference);
//         $result = db2_execute($preparedStmt,$data);

//         if($result){
//             $row = db2_fetch_assoc($preparedStmt);
//             return $row['TOTAL_HRS'];
//         } else {
//             $this->displayErrorMessage($preparedStmt, __CLASS__, __METHOD__, $this->preparedSelectSQL);
//             return false;
//         }
//     }
    
    function getHoursRemainingByReference(){
        if($this->hoursRemainingByReference==null){
            $date = new \DateTime();
            $complimentaryFields = $this->getDateComplimentaryFields($date);
            $sql = " select RESOURCE_REFERENCE, SUM(HOURS) as HOURS_TO_GO, count(*) as WEEKS_TO_GO ";
            $sql.= " from " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
            $sql.= " where WEEK_ENDING_FRIDAY > DATE('" . $complimentaryFields['WEEK_ENDING_FRIDAY'] ."') ";
            $sql.= " group by RESOURCE_REFERENCE; ";
            
            $rs = db2_exec($GLOBALS['conn'], $sql);
            
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }
            
            while(($row = db2_fetch_assoc($rs))==true){
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
        $sql.= "      where RR.RFS = '" . db2_escape_string($rfsId) . "' ";
        $sql.= "      ) ";
        $sql.= " AND DATE(WEEK_ENDING_FRIDAY) < CURRENT_DATE ";
        
        error_log($sql);
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
               
        error_log("Db2_Num_Rows:" . db2_num_rows($rs));
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        return $rs;
    }

    function prepareSetHoursForWef(int $resourceReference){  
       
        if(!isset($this->preparedSetHrsStatement)){
            $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
            $sql.= " SET HOURS= ? " ;
            $sql.= " WHERE DATE(WEEK_ENDING_FRIDAY) =  ? ";
            $sql.= " AND RESOURCE_REFERENCE= " . db2_escape_string($resourceReference);   
            $this->preparedSetHrsStatement = db2_prepare($GLOBALS['conn'], $sql);
            
            if(!$this->preparedSetHrsStatement){
                DbTable::displayErrorMessage($this->preparedSetHrsStatement, __CLASS__, __METHOD__, $sql);
            }
        }
              
        return $this->preparedSetHrsStatement ? $this->preparedSetHrsStatement : false;
    }

    function setHoursForWef(int $resourceReference, string $wef, float $hours){
        $preparedStmt = $this->prepareSetHoursForWef($resourceReference);         
        $parameters = array($hours, $wef);
        
        $rs = db2_execute($preparedStmt,$parameters);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
        }        
        return $rs ? true : false;        
    }
}