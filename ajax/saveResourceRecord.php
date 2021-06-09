<?php

use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use itdq\FormClass;
use itdq\Trace;
use rest\resourceRequestDiaryTable;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$rfs = !empty($_POST['RFS']) ? trim($_POST['RFS']) : null;

$startDate = !empty($_POST['START_DATE']) ? trim($_POST['START_DATE']) : null;
$endDate = !empty($_POST['END_DATE']) ? trim($_POST['END_DATE']) : $_POST['START_DATE'];

$totalHours = !empty($_POST['TOTAL_HOURS']) ? trim($_POST['TOTAL_HOURS']) : 0;
$rateType = !empty($_POST['RATE_TYPE']) ? trim($_POST['RATE_TYPE']) : resourceRequestRecord::RATE_TYPE_BLENDED;
$hoursType = !empty($_POST['HOURS_TYPE']) ? trim($_POST['HOURS_TYPE']) : resourceRequestRecord::HOURS_TYPE_REGULAR;
$organisation = !empty($_POST['ORGANISATION']) ? trim($_POST['ORGANISATION']) : null;
$service = !empty($_POST['SERVICE']) ? trim($_POST['SERVICE']) : null;
$description = !empty($_POST['DESCRIPTION']) ? trim($_POST['DESCRIPTION']) : '';

$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';
$resourceReference = !empty($_POST['RESOURCE_REFERENCE']) ? trim($_POST['RESOURCE_REFERENCE']) : '';
$resourceName = !empty($_POST['RESOURCE_NAME']) ? trim($_POST['RESOURCE_NAME']) : '';
$status = !empty($_POST['STATUS']) ? trim($_POST['STATUS']) : '';
$rrCreator = !empty($_POST['RR_CREATOR']) ? trim($_POST['RR_CREATOR']) : '';

$invalidRateType = !in_array($rateType, resourceRequestRecord::$allRateTypes);
$invalidHoursType = !in_array($hoursType, resourceRequestRecord::$allHourTypes);
$invalidTotalHoursAmount = empty($totalHours);
$invalidStartDate = resourceRequestTable::validateDate($startDate) === false;
$invalidEndDate = resourceRequestTable::validateDate($endDate) === false;

if ($startDate == null || $endDate == null || $rateType == null || $organisation == null || $service == null || $mode == null) {
    $invalidOtherParameters = true;
} else {
    $invalidOtherParameters = false;
}

// default validation values
$saveResponse = false;
$hoursResponse = '';
$create = false;
$update = false;

switch (true) {
    case $invalidRateType:
        // rate type protection
        $messages = 'Cannot save Resouce Request with provided Rate Type value.';
        break;
    case $invalidHoursType:
        // hours type protection
        $messages = 'Cannot save Resouce Request with provided Hours Type value.';
        break;
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

        $resourceRecord = new resourceRequestRecord();
        $resourceRecord->setFromArray($_POST);
        $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

        switch ($mode) {
            case FormClass::$modeDEFINE:
                
                $saveResponse = $resourceTable->insert($resourceRecord);
                $resourceReference = $resourceTable->lastId();
                $create = true;
                $update = false;

                if($saveResponse){
                    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
                    $resourceHoursSaved = false;
                    try {
                        $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference, $startDate, $endDate, $totalHours, true, $resourceRecord->getValue('HOURS_TYPE') );
                        $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";
                    } catch (Exception $e) {
                        $hoursResponse = $e->getMessage();
                        $create = false;
                    }
                }

                if ($saveResponse && $create == true) {
                    db2_commit($GLOBALS['conn']);
                } else {
                    $resourceReference = 'Record has been not created';
                    $saveResponse = false;
                    db2_rollback($GLOBALS['conn']);
                }
                break;
            case FormClass::$modeEDIT:
                
                $rrData = $resourceTable->getRecord($resourceRecord);
                $resourceRecord->setFromArray($rrData); // Get current data from the table.

                // unassigned requests
                if (empty($resourceRecord->get('RESOURCE_NAME'))) {
                    // we can change hours type
                    $originalHoursType = $resourceRecord->get('HOURS_TYPE');
                    if ($originalHoursType != $_POST['HOURS_TYPE']) {
                        $reinitialiseHours = true;
                    } else {
                        $reinitialiseHours = false;
                    }                    
                } else {
                    // keep previous type
                    unset($_POST['HOURS_TYPE']);
                    $reinitialiseHours = false;
                }

                $resourceRecord->setFromArray($_POST);  // Override with what the user has changed on the screen.
                $saveResponse = $resourceTable->update($resourceRecord);
                $saveResponse = $saveResponse ? true : false;
                $resourceReference = $resourceRecord->get('RESOURCE_REFERENCE');
                $create = false;
                $update = true;

                if ($saveResponse && $reinitialiseHours) {
                    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
                    $resourceHoursSaved = false;
                    try {

                        // echo $resourceReference;
                        // echo $resourceRecord->getValue('HOURS_TYPE');
                        // echo $startDate;
                        // echo $endDate;
                        // echo $totalHours;

                        $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference, $startDate, $endDate, $totalHours, true, $resourceRecord->getValue('HOURS_TYPE') );
                        $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";
                    } catch (Exception $e) {
                        $hoursResponse = $e->getMessage();
                        $update = false;
                    }

                    $diaryEntry = "Hours Type in Request was changed from  " . $originalHoursType . " to " . $_POST['HOURS_TYPE'];
                    $diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);

                    $diaryEntry = "Request was re-initialised at  " . $totalHours . " Total Hours (Start Date:" . $startDate . " End Date: " . $endDate . ")";
                    $diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);
                }

                if ($saveResponse && $update == true) {
                    db2_commit($GLOBALS['conn']);
                } else {
                    $resourceReference = 'Record has been not updated';
                    $saveResponse = false;
                    db2_rollback($GLOBALS['conn']);
                }
                break;
            default:
                echo 'Cannot save Resouce Request with provided Mode value.';
                break;
        }

        db2_autocommit($GLOBALS['conn'],$autoCommit);

        $messages = ob_get_clean();

        break;
}

ob_start();

$response = array(
    'resourceReference'=>$resourceReference, 
    'saveResponse' => $saveResponse, 
    'hoursResponse'=>$hoursResponse, 
    'messages'=>$messages,
    'create'=>$create,
    'update'=>$update
);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);