<?php
namespace rest;

use itdq\DbTable;
use itdq\DateClass;


class resourceRequestHoursTable extends DbTable
{
    private $preparedGetTotalHrsStatement;

    function createResourceRequestHours($resourceReference, $startDate,$endDate,$hours,$additionOnly=false){
        $sdate = new \DateTime($startDate);
        $edate = new \DateTime($endDate);
        $nextDate = $sdate;
        $startPeriod = $sdate->format('oW');
        $endPeriod = $edate->format('oW');
        $nextPeriod = $nextDate->format('oW');

        $weeksCreated = 0;

        $additionOnly ? $this->clearResourceReference($resourceReference) : null;

        $temp = 3;
        $oneWeek = new \DateInterval('P1W');

        while($nextPeriod <= $endPeriod){
            $resourceRequestHours = new resourceRequestHoursRecord();
            $resourceRequestHours->RESOURCE_REFERENCE = $resourceReference;
            $resourceRequestHours->DATE = $nextDate->format('Y-m-d');
            $resourceRequestHours->HOURS = $hours;
            $resourceRequestHours->YEAR = $nextDate->format('o');
            $resourceRequestHours->WEEK_NUMBER = $nextDate->format('W');

            self::populateComplimentaryDateFields($nextDate, $resourceRequestHours);

            $this->saveRecord($resourceRequestHours);
            $nextDate->add($oneWeek);
            $nextPeriod = $nextDate->format('oW');
            $weeksCreated++;
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

    function prepareGetTotalHoursStatement(){
        if(!isset($this->preparedGetTotalHrsStatement)){
            $sql = " SELECT SUM(HOURS) as TOTAL_HRS ";
            $sql .= " FROM " . $GLOBALS['Db2Schema'] . "." .  $this->tableName;
            $sql .= " WHERE RESOURCE_REFERENCE=?";
            $this->preparedSelectSQL = $sql;
            $this->preparedGetTotalHrsStatement = db2_prepare($GLOBALS['conn'], $sql);

        }
        return $this->preparedGetTotalHrsStatement;
    }



    function getTotalHoursForRequest($resourceReference=null){
        if(empty($resourceReference)){
            return false;
        }

        $preparedStmt = $this->prepareGetTotalHoursStatement();
        $data = array($resourceReference);
        $result = db2_execute($preparedStmt,$data);

        if($result){
            $row = db2_fetch_assoc($preparedStmt);
            return $row['TOTAL_HRS'];
        } else {
            $this->displayErrorMessage($preparedStmt, __CLASS__, __METHOD__, $this->preparedSelectSQL);
            return false;
        }
    }






}