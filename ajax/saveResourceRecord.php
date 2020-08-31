<?php

use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use itdq\FormClass;
use itdq\Trace;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);
$resourceRecord = new resourceRequestRecord();
$resourceRecord->setFromArray($_POST);
$resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
if(trim($_POST['mode'])==FormClass::$modeEDIT){
    $rrData = $resourceTable->getRecord($resourceRecord);
    $resourceRecord->setFromArray($rrData); // Get current data from the table.
    $resourceRecord->setFromArray($_POST);  // Override with what the user has changed on the screen.
    $saveResponse  = $resourceTable->update($resourceRecord);
    $saveResponse = $saveResponse ? true : false;
    $resourceReference = $resourceRecord->get('RESOURCE_REFERENCE');
    $update = true;
} else {
    $saveResponse  = $resourceTable->insert($resourceRecord);
    $resourceReference = $resourceTable->lastId();
    $update = false;
}
// $resourceRecord = new resourceRequestRecord();
// $resourceRecord->setFromArray($_POST);
// $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
// $saveResponse  = $resourceTable->saveRecord($resourceRecord);

// $resourceReference = $resourceTable->lastId();

$endDate = !empty($_POST['END_DATE']) ? $_POST['END_DATE'] : $_POST['START_DATE'];
$hours   = !empty($_POST['HRS_PER_WEEK']) ? $_POST['HRS_PER_WEEK'] : 0;

$hoursResponse = '';

if($saveResponse && trim($_POST['mode'])!=FormClass::$modeEDIT ){ // if they were editing, dont change the hours, that's done a different way.
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    $resourceHoursSaved = false;
    try {
        $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference,$_POST['START_DATE'],$endDate,$hours );
        $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";
    } catch (Exception $e) {
        $hoursResponse = $e->getMessage();
    }

}

$messages = ob_get_clean();
ob_start();

$response = array('resourceReference'=>$resourceReference, 'saveResponse' => $saveResponse, 'hoursResponse'=>$hoursResponse, 'Messages'=>$messages,'Update'=>$update);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);