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
db2_commit($_SESSION['conn']);

parse_str($_POST['formData'],$adjustedHours);
$originalResourceReference = $adjustedHours['ModalResourceReference'];
$deltaResourceReference = $_POST['deltaResourceRef'];

$loader = new Loader();
$originalWeeklyProfile = $loader->loadIndexed('HOURS','DATE',allTables::$RESOURCE_REQUEST_HOURS," RESOURCE_REFERENCE='$originalResourceReference' ");

$sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
$sql .= " SET HOURS=? " ;
$sql .= " WHERE RESOURCE_REFERENCE=? and DATE=? ";

$hoursUpdate = db2_prepare($_SESSION['conn'], $sql);

if(!$hoursUpdate){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

foreach ($adjustedHours as $key => $value){
    if(substr($key,0,15)== "ModalHRSForWeek"){
        $week = substr($key,15,10);
        $newHours = $value;

        $deltaHours = isset($originalWeeklyProfile[$week]) ? (string)($originalWeeklyProfile[$week] - $newHours) : "0";
        $changeExistingRecord = array($deltaHours,$originalResourceReference, $week);
        $result = db2_execute($hoursUpdate,$changeExistingRecord);

        $changeDeltaRecord = array($newHours,$deltaResourceReference, $week);
        $result = db2_execute($hoursUpdate,$changeDeltaRecord);

        db2_commit($_SESSION['conn']);
    }
}

db2_commit($_SESSION['conn']);

$messages = ob_get_clean();

$response = array('Messages'=>$messages);

ob_clean();
echo json_encode($response);