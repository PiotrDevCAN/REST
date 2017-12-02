<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHourseTable;
use rest\resourceRequestHoursTable;
use itdq\FormClass;

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

$response = array('resourceReference'=>$resourceReference, 'saveResponse' => $saveResponse, 'hoursResponse'=>$hoursResponse, 'Messages'=>$messages,'Update'=>$update);

ob_clean();
echo json_encode($response);