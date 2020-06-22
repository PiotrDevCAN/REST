<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHourseTable;
use rest\resourceRequestHoursTable;
use itdq\Trace;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$clear = isset($_POST['clear']) ? $_POST['clear'] : null;

try {
    $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceTable->updateResourceName($_POST['RESOURCE_REFERENCE'], $_POST['RESOURCE_NAME'], $clear);
    $exception = false;
} catch (Exception $e) {
    $exception = $e->getMessage();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'resourceReference'=>$_POST['RESOURCE_REFERENCE'], 'resourceName' => $_POST['RESOURCE_NAME'], 'Messages'=>$messages, 'Exception'=> $exception) ;

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);