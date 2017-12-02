<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;

session_start();

set_time_limit(0);



include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';
include_once '../itdq/Date.php';

include_once '../rest/resourceRequestTable.php';
include_once '../rest/resourceRequestRecord.php';
include_once '../rest/resourceRequestHoursTable.php';
include_once '../rest/resourceRequestHoursRecord.php';
include_once '../rest/allTables.php';

include_once '../rest/allTables.php';


ob_start();

include_once '../connect.php';

$resourceReference = $_POST['ModalResourceReference'];

$sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS;
$sql .= " SET HOURS=? " ;
$sql .= " WHERE RESOURCE_REFERENCE=? and DATE=? ";

$hoursUpdate = db2_prepare($_SESSION['conn'], $sql);

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

$response = array('Messages'=>$messages);

ob_clean();
echo json_encode($response);