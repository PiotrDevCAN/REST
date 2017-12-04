<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;

set_time_limit(0);
ob_start();

try {
    $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceTable->updatePlatformTypePrnCode($_POST['ptpcRESOURCE_REFERENCE'], $_POST['CURRENT_PLATFORM'], $_POST['RESOURCE_TYPE'], $_POST['PRN'], $_POST['PROJECT_CODE'] );
    $exception = false;
} catch (Exception $e) {
    $exception = $e->getMessage();
}

$messages = ob_get_clean();

$response = array('resourceReference'=>$_POST['ptpcRESOURCE_REFERENCE'], 'currentPlatform' => $_POST['CURRENT_PLATFORM'], 'resourceType'=>$_POST['RESOURCE_TYPE'], 'prn'=>$_POST['PRN'],'projectCode'=>$_POST['PROJECT_CODE'], 'Messages'=>$messages, 'Exception'=> $exception) ;

ob_clean();
echo json_encode($response);