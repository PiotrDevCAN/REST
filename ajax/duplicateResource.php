<?php

use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;
use itdq\Loader;

set_time_limit(0);
ob_start();

// $autoCommit = sqlsrv_commit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

$resourceRecord = new resourceRequestRecord();
$resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$resourceData = $resourceTable->getWithPredicate(" RESOURCE_REFERENCE='" . $_POST['resourceReference'] . "' ");
$resourceRecord->setFromArray($resourceData);

$resourceRecord->set('RESOURCE_REFERENCE', null); // So we get a new record when we insert.
$resourceRecord->set('RR_CREATED_TIMESTAMP', null); // so we know when the clone was created.
$currentResource =$resourceRecord->get('RESOURCE_NAME');

echo $_POST['delta']=='true' ? "delta is true" : "delta is not true";
$delta = $_POST['delta']=='true' ? true : false;

// When duplicating or auto-d'ing I would just update the resource name as null so it shows as unallocated
// $resourceNamePrefix = $_POST['delta']=='true' ? resourceRequestTable::DELTA : resourceRequestTable::DUPLICATE;
// !empty($currentResource) ? $resourceRecord->set('RESOURCE_NAME', $resourceNamePrefix . $resourceRecord->get('RESOURCE_NAME')) : null;
$resourceNamePrefix = '';
$resourceRecord->set('RESOURCE_NAME', null);
$resourceRecord->set('STATUS', resourceRequestRecord::STATUS_NEW);

$resourceRecord->set('CLONED_FROM',$_POST['resourceReference']);

$saveResponse  = $resourceTable->insert($resourceRecord);

$resourceReference = $resourceTable->lastId();

$startDate = $resourceRecord->get('START_DATE');
$endDate = $resourceRecord->get('END_DATE');
$hours   = $resourceRecord->get('TOTAL_HOURS');

if($saveResponse){
    try {
        $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference,$startDate,$endDate,$hours );
        $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";
        
        $loader = new Loader();
        $predicate = " RESOURCE_REFERENCE='" . htmlspecialchars($_POST['resourceReference']). "'";
        $currentHoursPerWef = $loader->loadIndexed('HOURS','WEEK_ENDING_FRIDAY',allTables::$RESOURCE_REQUEST_HOURS,$predicate);
        
        if($delta){
            // we need to amend the Hrs per week based on what the value was and what they've entered in the form
            foreach ($_POST as $key => $value){
                if(substr($key,0,14)== "ModalHRSForWef"){
                    $wef = substr($key,14,10);
                    $formHoursPerWef[$wef] = $value;   
                    $originalHoursPerWef[$wef] = $_POST['ModalHRSForWas' . $wef];
                }
            } 
            $totalDeltaHours = 0;
            $totalOriginalHours = 0;
            foreach ($currentHoursPerWef as $currentWef => $currentHours) {
                $deltaHours =(float)$originalHoursPerWef[$currentWef] - (float)$formHoursPerWef[$currentWef]; 
                $resourceHoursTable->setHoursForWef($resourceReference, $currentWef, $deltaHours);
                $totalDeltaHours+= $deltaHours; 
                $totalOriginalHours+=$originalHoursPerWef[$wef];
            }
            
            $newOriginalRequestsTotalHours = (float)$totalOriginalHours - (float)$totalDeltaHours;
            
            resourceRequestTable::setTotalHours($resourceReference, $totalDeltaHours);
            resourceRequestTable::setTotalHours($_POST['resourceReference'], $newOriginalRequestsTotalHours );
            
            $diaryEntry = "Request was auto-delta'd from:" . $_POST['resourceReference'];
            resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);  
            
            $diaryEntry = "Request was auto-delta'd into:" . $resourceReference ;
            resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['resourceReference']);
            
        } else {
            foreach ($currentHoursPerWef as $currentWef => $currentHours) {
                $resourceHoursTable->setHoursForWef($resourceReference, $currentWef, $currentHours);
            }
            
            $diaryEntry = "Request was cloned from:" . $_POST['resourceReference'];
            resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);   
            
            $diaryEntry = "Request was cloned into:" . $resourceReference ;
            resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['resourceReference']);   
            
        }

        sqlsrv_commit($GLOBALS['conn']);
    } catch (Exception $e) {
        $hoursResponse = $e->getMessage();
        sqlsrv_rollback($GLOBALS['conn']);
    }

}

// sqlsrv_commit($GLOBALS['conn'],$autoCommit);

$messages = ob_get_clean();
ob_start();

$response = array(
    'resourceReference'=>$resourceReference, 
    'saveResponse' => $saveResponse, 
    'hoursResponse'=>$hoursResponse,
    'messages'=>$messages, 
    'POST'=>print_r($_POST,true),
    'resourceNamePrefix',$resourceNamePrefix
);

ob_clean();
echo json_encode($response);