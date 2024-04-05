<?php
namespace rest;

use itdq\DbTable;

class activeResourceTable extends DbTable {

    const INT_STATUS_ACTIVE = 'active';
    const INT_STATUS_INACTIVE = 'inactive'; 

    const INT_STATUS_LOCAL = 'local';
    const INT_STATUS_REMOTE = 'remote';

    function __construct($table,$pwd=null,$log=true){
        parent::__construct($table,$pwd,$log);
    }

    static function addRecord($row = array()){
        // initial state of record
        $row['disabled'] = false;
        // override disabled if record is inactive
        if ($row['status'] == self::INT_STATUS_INACTIVE) {
            $row['disabled'] = true;
        }
        return $row;
    }

    function returnForDataTables(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE STATUS = '" . self::INT_STATUS_ACTIVE. "'";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $displayAble = array();

        while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $display = array();
            $row = array_map('trim', $row);
            $display['CNUM'] = $row['CNUM'];
            $display['WORKER_ID'] = $row['WORKER_ID'];
            $display['EMAIL_ADDRESS'] = !empty($row['EMAIL_ADDRESS']) ? $row['EMAIL_ADDRESS'] : 'unavailable in VBAC';
            $display['NOTES_ID'] = $row['NOTES_ID'];
            $display['FIRST_NAME'] = $row['FIRST_NAME'];
            $display['LAST_NAME'] = $row['LAST_NAME'];
            $display['PES_STATUS'] = $row['PES_STATUS'];
            $display['TRIBE_NAME'] = $row['TRIBE_NAME'];
            $display['SQUAD_NAME'] = $row['SQUAD_NAME'];
            $display['TRIBE_NAME_MAPPED'] = $row['TRIBE_NAME_MAPPED'];
            $display['ASSIGNMENT_TYPE'] = $row['ASSIGNMENT_TYPE'];
            $displayAble[] = $display;
        }
        return $displayAble;
    }

    function getVbacActiveResourcesForSelect2($fetchAll = null){

        $allEmployees = array();
        $allTribes = array();
        $vbacEmployees = array();
        $myTribes = array();
        $sql = '';
        $redis = $GLOBALS['redis'];
        $key = 'getVbacActiveResources_DB_employeeDetails';
        $redisKey = md5($key.'_key_'.$_ENV['environment'].'_param_'.$fetchAll);
        if (!$redis->get($redisKey)) {
            $source = 'SQL Server';

            $sql = " SELECT CNUM, WORKER_ID, NOTES_ID, EMAIL_ADDRESS, KYN_EMAIL_ADDRESS, FIRST_NAME, LAST_NAME, SQUAD_NAME, TRIBE_NAME, ASSIGNMENT_TYPE, STATUS ";
            $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE;
            $sql.= " ORDER BY STATUS ASC, EMAIL_ADDRESS ASC";

            $rs = sqlsrv_query($GLOBALS['conn'], $sql);
            
            if (!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            }

            while ($employeeDetails = sqlsrv_fetch_object($rs)){

                // save read employee
                $allEmployees[] = $employeeDetails;
            
                // get all tribe names
                !empty($employeeDetails->TRIBE_NAME) ? $allTribes[trim($employeeDetails->TRIBE_NAME)] = trim($employeeDetails->TRIBE_NAME) : null;
                
                // Filter out invalid Notes Ids                    
                // if (strtolower(substr(trim($employeeDetails->NOTES_ID), -4))=='/ibm' || strtolower(substr(trim($employeeDetails->NOTES_ID), -6))=='/ocean') {
                    // $vbacEmployees[] = array(
                    // $key = trim($employeeDetails->NOTES_ID);
                    // $key = trim($employeeDetails->CNUM);
                    // $key = trim($employeeDetails->EMAIL_ADDRESS);
                    $key = md5($employeeDetails->EMAIL_ADDRESS
                        .'_'.$employeeDetails->CNUM
                        .'_'.$employeeDetails->SQUAD_NAME
                        .'_'.$employeeDetails->TRIBE_NAME
                        .'_'.$employeeDetails->ASSIGNMENT_TYPE
                    );
                    $vbacEmployees[$key] = array(
                        'id'=>trim($employeeDetails->KYN_EMAIL_ADDRESS),
                        'cnum'=>trim($employeeDetails->CNUM),
                        'workerId'=>trim($employeeDetails->WORKER_ID),
                        'emailAddress'=>trim($employeeDetails->EMAIL_ADDRESS),
                        'kynEmailAddress'=>trim($employeeDetails->KYN_EMAIL_ADDRESS),
                        'notesId'=>trim($employeeDetails->NOTES_ID),
                        'text'=>trim($employeeDetails->FIRST_NAME) . ' ' . trim($employeeDetails->LAST_NAME) . ' (' . trim($employeeDetails->KYN_EMAIL_ADDRESS) . ')',
                        'role'=>trim($employeeDetails->SQUAD_NAME),
                        'tribe'=>trim($employeeDetails->TRIBE_NAME),
                        'type'=>trim($employeeDetails->ASSIGNMENT_TYPE),
                        'status'=>trim($employeeDetails->STATUS),
                        'distance'=>self::INT_STATUS_REMOTE,
                        'disabled'=>true
                    );
                // }
                    
                // get employee's tribe name
                if (array_key_exists('ssoEmail', $_SESSION)) {
                    if (strtolower($employeeDetails->EMAIL_ADDRESS) == strtolower($_SESSION['ssoEmail'])){
                        $myTribes[] = strtolower($employeeDetails->TRIBE_NAME);
                    }
                }
            }

            $redis->set($redisKey, json_encode($vbacEmployees));
            $redis->expire($redisKey, REDIS_EXPIRE);

        } else {
            $source = 'Redis Server';
            $vbacEmployees = json_decode($redis->get($redisKey), true);
        }

        // Find business unit for this tribe.     
        // $bestMatchScore = 0;
        // $bestMatch = '';
        // if (!empty($myTribe)){
        //     foreach ($allTribes as $tribe) {
        //         $matchScore = similar_text($myTribe, $tribe);
        //         if ($matchScore > $bestMatchScore){
        //             $bestMatchScore = $matchScore;
        //             $bestMatch = $tribe;
        //         }
        //     }
        // } else {
        //     throw new Exception("No tribe found for : " . $_SESSION['ssoEmail']);
        // }
            
        $tribeEmployees = array();
        if (isset($_SESSION['isAdmin']) || isset($_SESSION['isSupplyX']) || !is_null($fetchAll)){
            foreach ($vbacEmployees as $value) {
                $tribeEmployees[] = self::addRecord($value);
            }
        } else {
            // put local individuals at the top of list
            $localEmails = array();
            foreach ($vbacEmployees as $value) {
                if (!empty($myTribes) && in_array(strtolower($value['tribe']), $myTribes) ){
                    $value['distance'] = self::INT_STATUS_LOCAL;
                    $tribeEmployees[] = self::addRecord($value);
                    $localEmails[] = $value['emailAddress'];
                }
            }
            foreach ($vbacEmployees as $value) {
                if (!in_array($value['emailAddress'], $localEmails)) {
                    $tribeEmployees[] = self::addRecord($value);
                }
            }
        }

        $result = array(
            'allEmployees' => $allEmployees,
            'vbacEmployees' => $vbacEmployees,
            'tribeEmployees' => $tribeEmployees,
            'source' => $source,
            'sql' => $sql
        );

        return $result;
    }
}