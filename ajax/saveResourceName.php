<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHourseTable;
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


try {
    $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceTable->updateResourceName($_POST['RESOURCE_REFERENCE'], $_POST['RESOURCE_NAME']);
    $exception = false;
} catch (Exception $e) {
    $exception = $e->getMessage();
}

$messages = ob_get_clean();

$response = array('resourceReference'=>$_POST['RESOURCE_REFERENCE'], 'resourceName' => $_POST['RESOURCE_NAME'], 'Messages'=>$messages, 'Exception'=> $exception) ;

ob_clean();
echo json_encode($response);