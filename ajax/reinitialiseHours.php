<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;

set_time_limit(0);
ob_start();
$autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

switch (true) {
    case empty($_POST['ModalHRS_PER_WEEK']):
        echo 'No Hrs/Week provided for Reinitialise Hours function';
        $valid = false;
        break;

    default:
        $valid = true;
    break;
}
$hoursResponse = null;

if($valid){
    $resourceReference = $_POST['ModalResourceReference'];
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    try {
        $currentRecords = $resourceHoursTable->returnAsArray(" RESOURCE_REFERENCE='" . db2_escape_string($resourceReference) . "' ","*",true);
        $weeksSaved = 0;
        foreach ($currentRecords as $hrsRecord){
            $resourceHoursRecord = new resourceRequestHoursRecord();             // Start a new record
            $hrsRecord['HOURS'] = db2_escape_string($_POST['ModalHRS_PER_WEEK']);  // Set the hours from the form
            $resourceHoursRecord->setFromArray($hrsRecord);                      // Populate the new record 
            $resourceHoursTable->update($resourceHoursRecord);                   // Update the record in the Table
            $weeksSaved++;                                                       // Track how many we did.
        }
        $hoursResponse = $weeksSaved . " records updated.";
        
        resourceRequestTable::setHrsPerWeek($_POST['ModalResourceReference'],$_POST['ModalHRS_PER_WEEK'] );
        
        
        $diaryEntry = "Request was reinitialised at  " . $_POST['ModalHRS_PER_WEEK'] . " Hours per week";
        $diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);
        
        
        
        db2_commit($GLOBALS['conn']);
    } catch (Exception $e) {
        db2_rollback($GLOBALS['conn']);
        $hoursResponse = $e->getMessage();
    }

    $resourceHoursTable->commitUpdates();
}

db2_autocommit($GLOBALS['conn'],$autoCommit);

$messages = ob_get_clean();
ob_start();
$response = array( 'hoursResponse'=>$hoursResponse, 'Messages'=>$messages, 'DiaryRef'=>$diaryRef);

ob_clean();
echo json_encode($response);