<?php

use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;

set_time_limit(0);
ob_start();
/*
 * Read the existing records for this Resource Request into an array.
 *
 * Delete all the existing hours records for this resource request
 *
 * Step through the array of saved records and create a new record object from it.
 * Overwrite the DATE field with the new Date.
 * OVerwrite the "complimentary fields" based on the new date (WEEK_ENDING_FRIDAY, CLAIM_CUTOFF etc)
 * Increment the Week and loop back till all done.
 *
 */

if (sqlsrv_begin_transaction($GLOBALS['conn']) === false ) {
    die( print_r( sqlsrv_errors(), true ));
}

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
    $hoursStartDate = $complimentaryData->WEEK_ENDING_FRIDAY;
    resourceRequestHoursTable::populateComplimentaryDateFields($nextDate, $resourceHoursRecord);
    echo "<pre>";
    $resourceHoursRecord->iterateVisible();
    echo "</pre>";
//    $resourceHoursTable->saveRecord($resourceHoursRecord);
    $resourceHoursTable->insert($resourceHoursRecord);
    $nextDate->modify('+1 week');
    $weeksSaved++;
}

$startDate = new DateTime($hoursStartDate);
$endDate = $nextDate;
$endDate->modify('-1 week');

$resourceRequest->setFromArray(array('START_DATE'=>$startDate->format('Y-m-d'),'END_DATE'=>$endDate->format('Y-m-d')));
$resourceRequestTable->update($resourceRequest);

sqlsrv_commit($GLOBALS['conn']);

$diaryEntry = "Start Date set to " . $_POST['ModalSTART_DATE'];
$diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['ModalResourceReference']);

$messages = ob_get_clean();
ob_start();

$response = array( 'WeeksSaved'=> $weeksSaved, 'messages'=>$messages,'Diary'=>$diaryRef);

ob_clean();
echo json_encode($response);