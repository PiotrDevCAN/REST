<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
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

$autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);


$resourceRecord = new resourceRequestRecord();
$resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$resourceData = $resourceTable->getWithPredicate(" RESOURCE_REFERENCE='" . $_POST['resourceReference'] . "' ");
$resourceRecord->setFromArray($resourceData);

$originalRecord = print_r($resourceRecord,true);

$resourceRecord->set('RESOURCE_REFERENCE', null); // So we get a new record when we insert.
$resourceRecord->set('RR_CREATED_TIMESTAMP', null); // so we know when the clone was created.
$currentResource =$resourceRecord->get('RESOURCE_NAME');

$resourceNamePrefix = empty($_POST['delta']) ? 'Dup of' : 'Delta from ';

!empty($currentResource) ? $resourceRecord->set('RESOURCE_NAME', resourceNamePrefix . $resourceRecord->get('RESOURCE_NAME')) : null;

$resourceRecord->set('CLONED_FROM',$_POST['resourceReference']);

if(trim($_POST['drawDown'])=='true'){
    $drawDown = 'yes';
    $resourceRecord->set('CURRENT_PLATFORM',resourceRequestRecord::$tbd);
    $resourceRecord->set('RESOURCE_TYPE',resourceRequestRecord::$tbd);
    $resourceRecord->set('PARENT_BWO',$_POST['resourceReference']);
}

$saveResponse  = $resourceTable->insert($resourceRecord);

$resourceReference = $resourceTable->lastId();

$startDate = $resourceRecord->get('START_DATE');
$endDate = $resourceRecord->get('END_DATE');
$hours   = $resourceRecord->get('HRS_PER_WEEK');


if($saveResponse){
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    $resourceHoursSaved = false;
    try {
        $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference,$startDate,$endDate,$hours );
        $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";
        db2_commit($_SESSION['conn']);
    } catch (Exception $e) {
        $hoursResponse = $e->getMessage();
        db2_rollback($_SESSION['conn']);
    }

}

db2_autocommit($_SESSION['conn'],$autoCommit);

$messages = ob_get_clean();

$response = array('resourceReference'=>$resourceReference, 'saveResponse' => $saveResponse, 'hoursResponse'=>$hoursResponse,
                  'Messages'=>$messages);

ob_clean();
echo json_encode($response);