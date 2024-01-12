<?php

use itdq\Loader;
use itdq\WorkerAPI;
use rest\emailNotifications;
use rest\allTables;
use rest\rfsRecord;
use rest\resourceRequestHoursTable;
use rest\resourceRequestRecord;
use rest\resourceRequestDiaryTable;
use rest\resourceRequestTable;

set_time_limit(0);
ob_start();

if (sqlsrv_begin_transaction($GLOBALS['conn']) === false ) {
    die( print_r( sqlsrv_errors(), true ));
}

$rfsRequestorEmail = !empty($_POST['requestorEmail']) ? trim($_POST['requestorEmail']) : null;
$rfsRequestorName = !empty($_POST['requestorName']) ? trim($_POST['requestorName']) : null;
$rfsId = !empty($_POST['rfsid']) ? trim($_POST['rfsid']) : null;

$sp = strpos(strtolower($rfsRequestorEmail),'kyndryl');
if($sp === FALSE){
    // none ocean
    $invalidRequestorEmail = true;
} else {
    // is ocean or kyndryl
    $workerAPI = new WorkerAPI();
    $data = $workerAPI->getworkerByEmail($rfsRequestorEmail);
    if (array_key_exists('count', $data) && $data['count'] > 0) {
        //valid ocean
        $invalidRequestorEmail = false;
    } else {
        // invalid ocean
        $invalidRequestorEmail = true;
    }
}

switch (true) {
    case $invalidRequestorEmail:
        $success = false;
        $messages = 'Cannot save RFS Record with provided RFS Requestor Email value.';
        break;
    default:
        // If the start date is in the past - bring it up to today.
        $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);$loader = new Loader();
        $predicate = " RFS='" . htmlspecialchars($rfsId) . "' AND START_DATE <= CURRENT_TIMESTAMP AND TOTAL_HOURS is not null AND END_DATE is not null " ;
        $allRequestsHours   = $loader->loadIndexed('TOTAL_HOURS','RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS, $predicate, 'asc');
        $allRequestsEnd     = $loader->loadIndexed('END_DATE'   ,'RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS, $predicate, 'asc');
        $allRequestsStart   = $loader->loadIndexed('START_DATE' ,'RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS, $predicate, 'asc');
        $allRequestsHrsType = $loader->loadIndexed('HOURS_TYPE' ,'RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS, $predicate, 'asc');

        foreach ($allRequestsHours as $resourceReference => $hours) {
            $nextPossibleStartDate = new DateTime();
            $dayOfWeek = $nextPossibleStartDate->format('N');
                
            if($allRequestsHrsType[$resourceReference] == resourceRequestRecord::HOURS_TYPE_OT_WEEK_END){
                // It's a request for Weekend OVertime, so we have to roll forward to a Sat or Sun
                switch ($dayOfWeek) {
                    case 6:
                        // Sat roll forward to Sunday
                        $modification  = "+1 day ";
                    break;
                    case 7:
                        // Sun roll forward to Saturday
                        $modification  = "+6 days ";
                        break;
                    default:
                        // mid-week roll forward to Saturday
                        $modification  = "+" . (6-$dayOfWeek) . " days ";   
                    break;
                }
            } else {
                // It's not a weekend over time request, so must be a business day
                switch ($dayOfWeek) {
                    case 5:
                    case 6:
                    case 7:
                        // Fri, Sat, Sun roll forward to Monday
                        $modification  = "+" . (8-$dayOfWeek) . " days ";
                    break;            
                    default:
                        // Mon to Thu roll forward one day
                        $modification  = "+1 day ";
                    break;
                }
            }
            $nextPossibleStartDate->modify($modification);    
            $nextPossibleStartDateString = $nextPossibleStartDate->format('Y-m-d'); 
            
            $resourceHoursTable->createResourceRequestHours($resourceReference, $nextPossibleStartDateString, $allRequestsEnd[$resourceReference], $hours, true,$allRequestsHrsType[$resourceReference]  );    
            resourceRequestTable::setStartDate($resourceReference, $nextPossibleStartDateString);
            
            $diaryEntry = " Start date moved from " . $allRequestsStart[$resourceReference] . " to " . $nextPossibleStartDateString . " with release to Live.";
            resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);
        }

        // Update the requestor from the Form they've just submitted, as it often changes with going live.

        $sql = " UPDATE ";
        $sql.=   $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
        $sql.= " SET RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' ";
        $sql.= " , REQUESTOR_NAME = '" . htmlspecialchars($rfsRequestorName) . "' " ;
        $sql.= " , REQUESTOR_EMAIL = '" . htmlspecialchars($rfsRequestorEmail) . "' " ;
        $sql.= " WHERE RFS_ID='" . htmlspecialchars($rfsId) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            echo json_encode(sqlsrv_errors());
            echo json_encode(sqlsrv_errors());
        }

        // $allocatorNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
        $allocatorNotesid = $_SESSION['ssoEmail'];
        $emailEntry = "You have been assigned as the Project Manager for RFS &&rfs&& by $allocatorNotesid ";
        $emailPattern = array('RFS_ID'=>'/&&rfs&&/');
        emailNotifications::sendRfsNotification($rfsId,$emailEntry, $emailPattern);

        $messages = ob_get_clean();
        ob_start();
        $success = empty($messages) && $rs;

        $success ? sqlsrv_commit($GLOBALS['conn']) : sqlsrv_rollback($GLOBALS['conn']);
        break;
}

$response = array('success'=>$success,'rfsId' => $rfsId, 'messages'=>$messages);

ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

header('Content-Type: application/json');
echo json_encode($response);