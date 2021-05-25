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

$rfs = !empty($_POST['RFS']) ? trim($_POST['RFS']) : null;
$startDate = !empty($_POST['START_DATE']) ? trim($_POST['START_DATE']) : null;
$endDate = !empty($_POST['END_DATE']) ? trim($_POST['END_DATE']) : $_POST['START_DATE'];
$totalHours = !empty($_POST['TOTAL_HOURS']) ? trim($_POST['TOTAL_HOURS']) : 0;
$rateType = !empty($_POST['RATE_TYPE']) ? trim($_POST['RATE_TYPE']) : null;
$hoursType = !empty($_POST['HOURS_TYPE']) ? trim($_POST['HOURS_TYPE']) : resourceRequestRecord::HOURS_TYPE_REGULAR;
$organisation = !empty($_POST['ORGANISATION']) ? trim($_POST['ORGANISATION']) : null;
$service = !empty($_POST['SERVICE']) ? trim($_POST['SERVICE']) : null;
$description = !empty($_POST['DESCRIPTION']) ? trim($_POST['DESCRIPTION']) : '';

$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';
$resourceReference = !empty($_POST['RESOURCE_REFERENCE']) ? trim($_POST['RESOURCE_REFERENCE']) : '';
$status = !empty($_POST['STATUS']) ? trim($_POST['STATUS']) : '';
$rrCreator = !empty($_POST['RR_CREATOR']) ? trim($_POST['RR_CREATOR']) : '';

if ($totalHours == 0) {
    // zero total hours protection
    $saveResponse = false;
    $hoursResponse = '';
    $messages = 'Cannot save Resouce Request with zero total hours.';
    $create = false;
    $update = false;
} else {
    if ($startDate == null || $endDate == null || $rateType == null || $organisation == null || $service == null || $mode == null) {
        // required parameters protection
        $saveResponse = false;
        $hoursResponse = '';
        $messages = 'Significant parameters from form are missing.';
        $create = false;
        $update = false;
    } else {
        
        $resourceRecord = new resourceRequestRecord();
        $resourceRecord->setFromArray($_POST);
        $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
        if($mode==FormClass::$modeEDIT){
            $rrData = $resourceTable->getRecord($resourceRecord);
            $resourceRecord->setFromArray($rrData); // Get current data from the table.
            $resourceRecord->setFromArray($_POST);  // Override with what the user has changed on the screen.
            $saveResponse  = $resourceTable->update($resourceRecord);
            $saveResponse = $saveResponse ? true : false;
            $resourceReference = $resourceRecord->get('RESOURCE_REFERENCE');
            $create = false;
            $update = true;
        } else {
            $saveResponse  = $resourceTable->insert($resourceRecord);
            $resourceReference = $resourceTable->lastId();
            $create = true;
            $update = false;
        }

        $hoursResponse = '';

        if($saveResponse && $mode!=FormClass::$modeEDIT){ // if they were editing, dont change the hours, that's done a different way.
            $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
            $resourceHoursSaved = false;
            try {
                $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference, $startDate, $endDate, $totalHours, true, $resourceRecord->getValue('HOURS_TYPE') );
                $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";
            } catch (Exception $e) {
                $hoursResponse = $e->getMessage();
            }
        }

        // if($saveResponse && $mode==FormClass::$modeDEFINE){
        //     $saveResponse = false;

        //     // delete created request
        //     $resourceTable->deleteRecord($resourceRecord);
        // }

        $messages = ob_get_clean();
    }
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