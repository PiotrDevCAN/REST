<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHourseTable;
use rest\resourceRequestHoursTable;

set_time_limit(0);
ob_start();

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