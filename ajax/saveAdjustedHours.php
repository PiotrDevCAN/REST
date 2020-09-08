<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;
use itdq\Trace;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$resourceReference = $_POST['ModalResourceReference'];

$sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
$sql .= " SET HOURS=? " ;
$sql .= " WHERE RESOURCE_REFERENCE=? and DATE=? ";

$hoursUpdate = db2_prepare($GLOBALS['conn'], $sql);

if(!$hoursUpdate){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

var_dump($hoursUpdate);

foreach ($_POST as $key => $value){
    if(substr($key,0,15)== "ModalHRSForWeek"){
        $week = substr($key,15,10);
        $hours = $value;

        $data = array($hours,$resourceReference, $week);
        $result = db2_execute($hoursUpdate,$data);

        var_dump($result);
    }
}

$messages = ob_get_clean();
ob_start();

$response = array('Messages'=>$messages);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
