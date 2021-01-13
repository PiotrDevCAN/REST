<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;

set_time_limit(0);
ob_start();
$autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);
$hoursResponse = null;
$valid=true;
$resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);


if($valid){
    $resourceRecord = new resourceRequestRecord();
    $resourceRecord->setFromArray(array('RESOURCE_REFERENCE'=>$_POST['ModalResourceReference']));
    
    $rrData = $resourceTable->getRecord($resourceRecord);
    $resourceRecord->setFromArray($rrData); // Get current data from the table.
    $resourceRecord->setFromArray(array('START_DATE'=>$_POST['ModalSTART_DATE'],'END_DATE'=>$_POST['ModalEND_DATE'],'TOTAL_HOURS'=>$_POST['ModalTOTAL_HOURS']));  // Override with what the user has changed on the screen.
    $resp = $resourceTable->update($resourceRecord);
  
    
    $resourceReference = $_POST['ModalResourceReference'];
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    try {
        $resourceHoursTable->createResourceRequestHours($resourceReference,$_POST['ModalSTART_DATE'],$_POST['ModalEND_DATE'],$_POST['ModalTOTAL_HOURS'], true, $_POST['ModalHOURS_TYPE'] );
    } catch (Exception $e) {
        db2_rollback($GLOBALS['conn']);
        $hoursResponse = $e->getMessage();
    }
        
    resourceRequestTable::setTotalHours($_POST['ModalResourceReference'],$_POST['ModalTOTAL_HOURS'] );
        
        
    $diaryEntry = "Request was re-initialised at  " . $_POST['ModalTOTAL_HOURS'] . " Total Hours (Start Date:" . $_POST['ModalSTART_DATE'] . " End Date: " . $_POST['ModalEND_DATE'] . ")";
    $diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);
        
    db2_commit($GLOBALS['conn']);
}

db2_autocommit($GLOBALS['conn'],$autoCommit);

$messages = ob_get_clean();
ob_start();
$response = array( 'hoursResponse'=>$hoursResponse, 'Messages'=>$messages, 'DiaryRef'=>$diaryRef);

ob_clean();
echo json_encode($response);