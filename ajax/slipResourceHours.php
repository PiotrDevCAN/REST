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

/*
 * Read the existing records for this Resouce Request into an array.
 *
 * Delete all the existing hours records for this resource request
 *
 * Step through the array of saved records and create a new record object from it.
 * Overwrite the DATE field with the new Date.
 * OVerwrite the "complimentary fields" based on the new date (WEEK_ENDING_FRIDAY, CLAIM_CUTOFF etc)
 * Increment the Week and loop back till all done.
 *
 */


$autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

if(empty($_POST['ModalSTART_DATE'])){
    throw new Exception('No Start Date provided for Slipping Start Date function');
}

$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$resourceRequest = new resourceRequestRecord();
$resourceRequest->set('RESOURCE_REFERENCE', $_POST['ModalResourceReference']);
$resourceRequestData = $resourceRequestTable->getRecord($resourceRequest);
$resourceRequest->setFromArray($resourceRequestData);

$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$currentHours = $resourceHoursTable->returnAsArray(" RESOURCE_REFERENCE='" . $_POST['ModalResourceReference'] . "' ","*",true);

$resourceHoursTable->deleteData(" RESOURCE_REFERENCE='" . $_POST['ModalResourceReference'] . "' ");

$nextDate = new DateTime($_POST['ModalSTART_DATE']);
$weeksSaved = 0;

foreach ($currentHours as $oldRecord){
    $resourceHoursRecord = new resourceRequestHoursRecord();
    $resourceHoursRecord->setFromArray($oldRecord);
    $resourceHoursRecord->DATE = $nextDate->format(('Y-m-d'));
    $complimentaryData = resourceRequestHoursTable::getDateComplimentaryFields($nextDate);
    resourceRequestHoursTable::populateComplimentaryDateFields($nextDate, $resourceHoursRecord);
    $resourceHoursTable->saveRecord($resourceHoursRecord, true, true, false);
    $nextDate->modify('+1 week');
    $weeksSaved++;
}

$startDate = new DateTime($_POST['ModalSTART_DATE']);
$endDate = $nextDate;
$endDate->modify('-1 week');

$resourceRequest->setFromArray(array('START_DATE'=>$startDate->format('Y-m-d'),'END_DATE'=>$endDate->format('Y-m-d')));
$resourceRequestTable->update($resourceRequest);

$resourceHoursTable->commitUpdates();

db2_autocommit($_SESSION['conn'],$autoCommit);

$messages = ob_get_clean();

$response = array( 'WeeksSaved'=> $weeksSaved, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);