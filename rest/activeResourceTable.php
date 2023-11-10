<?php
namespace rest;

use itdq\DbTable;
use itdq\AuditTable;
use rest\activeResourceRecord;

class activeResourceTable extends DbTable {

    const INT_STATUS_ACTIVE = 'active';
    const INT_STATUS_INACTIVE = 'inactive'; 

    function __construct($table,$pwd=null,$log=true){
        parent::__construct($table,$pwd,$log);
    }

    function returnForDataTables(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE STATUS='" . self::INT_STATUS_ACTIVE. "'";

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
            $display['EMAIL_ADDRESS'] = !empty($row['EMAIL_ADDRESS']) ? $row['EMAIL_ADDRESS'] : 'unavailable in VBAC';
            $display['NOTES_ID'] = $row['NOTES_ID'];
            $display['FIRST_NAME'] = $row['FIRST_NAME'];
            $display['LAST_NAME'] = $row['LAST_NAME'];
            $display['PES_STATUS'] = $row['PES_STATUS'];
            $displayAble[] = $display;
        }
        return $displayAble;
    }

    function getVbacActiveResourcesForSelect2(){

        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE;
        $sql.= " WHERE STATUS = 'active' ";

        $allEmployees = array();
        $allTribes = array();
        $vbacEmployees = array();
        $myTribe = '';

        $redis = $GLOBALS['redis'];
        $key = 'getVbacActiveResources_DB_employeeDetails';
        $redisKey = md5($key.'_key_'.$_ENV['environment']);
        if (!$redis->get($redisKey)) {
            $source = 'SQL Server';

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
                    $key = trim($employeeDetails->CNUM);
                    $vbacEmployees[$key] = array(
                        'id'=>trim($employeeDetails->KYN_EMAIL_ADDRESS),
                        'cnum'=>trim($employeeDetails->CNUM),
                        'emailAddress'=>trim($employeeDetails->EMAIL_ADDRESS),
                        'kynEmailAddress'=>trim($employeeDetails->KYN_EMAIL_ADDRESS),
                        'notesId'=>trim($employeeDetails->NOTES_ID),
                        'text'=>trim($employeeDetails->FIRST_NAME) . ' ' . trim($employeeDetails->LAST_NAME) . ' (' . trim($employeeDetails->KYN_EMAIL_ADDRESS) . ')',
                            'role'=>trim($employeeDetails->SQUAD_NAME),
                            'tribe'=>trim($employeeDetails->TRIBE_NAME),
                            'distance'=>'remote'
                    );
                // }
                    
                // get employee's tribe name
                if (array_key_exists('ssoEmail', $_SESSION)) {
                    if (strtolower($employeeDetails->EMAIL_ADDRESS) == strtolower($_SESSION['ssoEmail'])){
                        $myTribe = $employeeDetails->TRIBE_NAME;
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
        // process the employees, flagging as 'local' those in the "myTribe" tribe
        foreach ($vbacEmployees as $value) {
            // if (strtolower(substr(trim($value['id']), -4))=='/ibm' || strtolower(substr(trim($value['id']), -6))=='/ocean'){  // Filter out invalid Notes Ids
                // if (!empty(trim($value['role']) && !empty(trim($value['tribe'])))) {
                    if (!empty($myTribe) && strtolower($value['tribe']) == strtolower($myTribe)){
                        $value['distance']='local';
                        // $tribeEmployees[trim($value['id'])] = $value;
                        $tribeEmployees[] = $value;
                    } else {
                        if (isset($_SESSION['isAdmin']) || isset($_SESSION['isSupplyX'])){
                            // $tribeEmployees[trim($value['id'])] = $value;
                            $tribeEmployees[] = $value;
                        }
                    }
                // } else {
                    // $value['distance']='removed';
                    // $tribeEmployees[trim($value['id'])] = $value;
                    // $tribeEmployees[] = $value;
                // }
            // }
        }
        
        $result = array(
            'allEmployees' => $allEmployees,
            'vbacEmployees' => $vbacEmployees,
            'tribeEmployees' => $tribeEmployees,
            'source' => $source
        );

        return $result;
    }
}