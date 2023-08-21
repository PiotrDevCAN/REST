<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;
use itdq\Loader;

set_time_limit(0);
ob_start();

db2_commit($GLOBALS['conn']);

parse_str($_POST['formData'],$adjustedHours);
$originalResourceReference = $adjustedHours['ModalResourceReference'];
$deltaResourceReference = $_POST['deltaResourceRef'];

$loader = new Loader();
$originalWeeklyProfile = $loader->loadIndexed('HOURS','DATE',allTables::$RESOURCE_REQUEST_HOURS," RESOURCE_REFERENCE='$originalResourceReference' ");

$sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
$sql .= " SET HOURS=? " ;
$sql .= " WHERE RESOURCE_REFERENCE=? and DATE=? ";

$hoursUpdate = db2_prepare($GLOBALS['conn'], $sql);

if(!$hoursUpdate){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

foreach ($adjustedHours as $key => $value){
    if(substr($key,0,15)== "ModalHRSForWeek"){
        $week = substr($key,15,10);
        $newHours = $value;

        $deltaHours = isset($originalWeeklyProfile[$week]) ? (string)($originalWeeklyProfile[$week] - $newHours) : "0";

        $changeExistingRecord = array($newHours,$originalResourceReference, $week);
        var_dump($changeExistingRecord);

        $result = sqlsrv_execute($hoursUpdate,$changeExistingRecord);

        $changeDeltaRecord = array($deltaHours,$deltaResourceReference, $week);
        var_dump($changeDeltaRecord);

        $result = sqlsrv_execute($hoursUpdate,$changeDeltaRecord);

        var_dump($result);
        echo "<hr/>";


        db2_commit($GLOBALS['conn']);
    }
}

db2_commit($GLOBALS['conn']);

$messages = ob_get_clean();
ob_start();

$response = array('messages'=>$messages);

ob_clean();
echo json_encode($response);