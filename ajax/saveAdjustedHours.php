<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestTable;
use itdq\Trace;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$resourceReference = $_POST['ModalResourceReference'];

$sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
$sql .= " SET HOURS=? " ;
$sql .= " WHERE RESOURCE_REFERENCE=? and WEEK_ENDING_FRIDAY=? ";

foreach ($_POST as $key => $value){
    if(substr($key,0,14)== "ModalHRSForWef"){
        $week = substr($key,14,10);
        $hours = $value;

        $data = array($hours, $resourceReference, $week);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql, $data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, 'ajax', __FILE__, $sql);
        }
    }
}

$totalHours = !empty($_POST['ModalTOTAL_HOURS']) ? trim($_POST['ModalTOTAL_HOURS']) : 0;
resourceRequestTable::setTotalHours($resourceReference, $totalHours);

$messages = ob_get_clean();
ob_start();

$response = array('messages'=>$messages);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
