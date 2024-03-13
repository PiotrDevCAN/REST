<?php

use itdq\DbTable;
use rest\allTables;
use itdq\Loader;

set_time_limit(0);
ob_start();

sqlsrv_commit($GLOBALS['conn']);

parse_str($_POST['formData'],$adjustedHours);
$originalResourceReference = $adjustedHours['ModalResourceReference'];
$deltaResourceReference = $_POST['deltaResourceRef'];

$loader = new Loader();
$originalWeeklyProfile = $loader->loadIndexed('HOURS','DATE',allTables::$RESOURCE_REQUEST_HOURS," RESOURCE_REFERENCE='$originalResourceReference' ");

$sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
$sql .= " SET HOURS = ? " ;
$sql .= " WHERE RESOURCE_REFERENCE = ? ";
$sql .= " AND DATE = ? ";

foreach ($adjustedHours as $key => $value){
    if(substr($key,0,15)== "ModalHRSForWeek"){
        $week = substr($key,15,10);
        $newHours = $value;

        $deltaHours = isset($originalWeeklyProfile[$week]) ? (string)($originalWeeklyProfile[$week] - $newHours) : "0";

        $changeExistingRecord = array($newHours, $originalResourceReference, $week);
        var_dump($changeExistingRecord);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql, $changeExistingRecord);

        if(!$rs){
            DbTable::displayErrorMessage($rs, 'ajax', __FILE__, $sql);
        }

        $changeDeltaRecord = array($deltaHours, $deltaResourceReference, $week);
        var_dump($changeDeltaRecord);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql, $changeDeltaRecord);

        if(!$rs){
            DbTable::displayErrorMessage($rs, 'ajax', __FILE__, $sql);
        }

        var_dump($rs);
        echo "<hr/>";

        sqlsrv_commit($GLOBALS['conn']);
    }
}

sqlsrv_commit($GLOBALS['conn']);

$messages = ob_get_clean();
ob_start();

$response = array('messages'=>$messages);

ob_clean();
echo json_encode($response);