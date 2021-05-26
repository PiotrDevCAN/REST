<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;

set_time_limit(0);
ob_start();

$resourceReference = !empty($_POST['ModalResourceReference']) ? trim($_POST['ModalResourceReference']) : null;
$startDate = !empty($_POST['ModalSTART_DATE']) ? trim($_POST['ModalSTART_DATE']) : null;
$endDate = !empty($_POST['ModalEND_DATE']) ? trim($_POST['ModalEND_DATE']) : null;
$totalHours = !empty($_POST['ModalTOTAL_HOURS']) ? trim($_POST['ModalTOTAL_HOURS']) : 0;
$hoursType = !empty($_POST['ModalHOURS_TYPE']) ? trim($_POST['ModalHOURS_TYPE']) : resourceRequestRecord::HOURS_TYPE_REGULAR;

$invalidTotalHoursAmount = empty($totalHours);
$invalidStartDate = resourceRequestTable::validateDate($startDate) === false;
$invalidEndDate = resourceRequestTable::validateDate($endDate) === false;

if ($startDate == null || $endDate == null || $resourceReference == null) {
    $invalidOtherParameters = true;
} else {
    $invalidOtherParameters = false;
}

// default validation values
$success = false;
$hoursResponse = '';
$diaryRef = '';

switch (true) {
    case $invalidTotalHoursAmount:
        // zero total hours protection
        $messages = 'Cannot save Resouce Request with zero total hours.';
        break;
    case $invalidStartDate:
        // start date protection
        $messages = 'Cannot save Resouce Request with zprovided Start Date value.';
        break;
    case $invalidEndDate:
        // end date protection
        $messages = 'Cannot save Resouce Request with provided End Date value.';
        break; 
    case $invalidOtherParameters:
        // required parameters protection
        $messages = 'Significant parameters from form are missing.';
        break;
    default:
        $autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);
        $hoursResponse = null;
        $valid=true;
        $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

        if($valid){
            $resourceRecord = new resourceRequestRecord();
            $resourceRecord->setFromArray(array('RESOURCE_REFERENCE'=>$resourceReference));
            
            $rrData = $resourceTable->getRecord($resourceRecord);
            $resourceRecord->setFromArray($rrData); // Get current data from the table.
            $resourceRecord->setFromArray(
                array(
                    'START_DATE'=>$startDate,
                    'END_DATE'=>$endDate,
                    'TOTAL_HOURS'=>$totalHours
                )
            );  // Override with what the user has changed on the screen.
            $resp = $resourceTable->update($resourceRecord);
            
            $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
            try {
                $resourceHoursTable->createResourceRequestHours($resourceReference, $startDate, $endDate, $totalHours, true, $hoursType );
            } catch (Exception $e) {
                db2_rollback($GLOBALS['conn']);
                $hoursResponse = $e->getMessage();
            }
                
            resourceRequestTable::setTotalHours($resourceReference,$totalHours );
            
            $diaryEntry = "Request was re-initialised at  " . $totalHours . " Total Hours (Start Date:" . $startDate . " End Date: " . $endDate . ")";
            $diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);
            
            $success = true;
            db2_commit($GLOBALS['conn']);
        }

        db2_autocommit($GLOBALS['conn'],$autoCommit);

        $messages = ob_get_clean();

        break;
}

ob_start();
$response = array(
    'success'=>$success,
    'hoursResponse'=>$hoursResponse,
    'messages'=>$messages,
    'DiaryRef'=>$diaryRef
);

ob_clean();
echo json_encode($response);