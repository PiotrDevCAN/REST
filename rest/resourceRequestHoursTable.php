<?php
namespace rest;

use itdq\DbTable;
use itdq\DateClass;


class resourceRequestHoursTable extends DbTable
{
    private $preparedGetTotalHrsStatement;
    private $hoursRemainingByReference;
    
    function createResourceRequestHours($resourceReference, $startDate,$endDate,$hours,$deleteExisting=true, $hrsType=resourceRequestRecord::HOURS_TYPE_REGULAR){
        $sdate = new \DateTime($startDate);
        $edate = new \DateTime($endDate);        
       
        if($hrsType == resourceRequestRecord::HOURS_TYPE_OT_WEEK_END){
            $effortDays = DateClass::weekendDaysFromStartToEnd($sdate, $edate);
            $bankHolidays = array();
            $hrsPerEffortDay = $hours / $effortDays;
        } else {
            $response = DateClass::businessDaysFromStartToEnd($sdate, $edate);
            $effortDays = $response['businessDays'];
            $bankHolidays = $response['bankHolidays'];
            $hrsPerEffortDay = $hours / $effortDays;
        }
      
        $nextDate = $sdate;
        $startPeriod = $sdate->format('oW');
        $endPeriod = $edate->format('oW');
        $nextPeriod = $nextDate->format('oW');

        $weeksCreated = 0;

        $deleteExisting ? $this->clearResourceReference($resourceReference) : null;

        $temp = 3;
        $oneWeek = new \DateInterval('P1W');

        while($nextPeriod <= $endPeriod){
            $resourceRequestHours = new resourceRequestHoursRecord();
            $resourceRequestHours->RESOURCE_REFERENCE = $resourceReference;
            $resourceRequestHours->DATE = $nextDate->format('Y-m-d');
            // $resourceRequestHours->HOURS = $hours;
            $resourceRequestHours->YEAR = $nextDate->format('o');
            $resourceRequestHours->WEEK_NUMBER = $nextDate->format('W');

            self::populateComplimentaryDateFields($nextDate, $resourceRequestHours);
            
            $resourceRequestHours->DATE = $resourceRequestHours->WEEK_ENDING_FRIDAY;
            $wefDate = new \DateTime($resourceRequestHours->WEEK_ENDING_FRIDAY);
            
            if($hrsType == resourceRequestRecord::HOURS_TYPE_OT_WEEK_END){
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
            } else {
                $businessDaysInWeek = DateClass::businessDaysForWeekEndingFriday($resourceRequestHours->WEEK_ENDING_FRIDAY, $bankHolidays,$sdate, $edate);
            }
            
            if($businessDaysInWeek>0){
                $businessHoursInWeek = $businessDaysInWeek * $hrsPerEffortDay;
                $resourceRequestHours->HOURS = $businessHoursInWeek;
                
                $this->saveRecord($resourceRequestHours);
                $weeksCreated++;
            }
            
            $nextDate->add($oneWeek);
            $nextPeriod = $nextDate->format('oW');
            

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
            $rs = $this->execute($sql);
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
        $sql.= "      left join " .  $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
        $sql.= "      on RFS.RFS_ID = RR.RFS ";
        $sql.= "      ) ";
        $sql.= " AND DATE(WEEK_ENDING_FRIDAY) < CURRENT_DATE ";
        
        $rs = db2_exec($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }
        
        return $rs;
    }





}