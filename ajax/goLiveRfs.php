<?php

use itdq\DbTable;
use itdq\BluePages;
use itdq\BluePagesSLAPHAPI;
use rest\emailNotifications;
use rest\allTables;
use rest\rfsRecord;
use rest\resourceRequestHoursTable;
use itdq\Loader;
use rest\resourceRequestRecord;
use rest\resourceRequestDiaryTable;
use rest\resourceRequestTable;

set_time_limit(0);
ob_start();

$autocommit = sqlsrv_commit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

$rfsRequestorEmail = !empty($_POST['requestorEmail']) ? trim($_POST['requestorEmail']) : null;
$rfsRequestorName = !empty($_POST['requestorName']) ? trim($_POST['requestorName']) : null;
$rfsId = !empty($_POST['rfsid']) ? trim($_POST['rfsid']) : null;

$sp = strpos(strtolower($rfsRequestorEmail),'ocean');
if($sp === FALSE){
    // none ocean
    $invalidRequestorEmail = true;
} else {
    // is ocean
    $data = BluePagesSLAPHAPI::getIBMDetailsFromIntranetId($rfsRequestorEmail);
    if (!empty($data)) {
        //valid ocean
        $invalidRequestorEmail = false;
    } else {
        //invalid ocean
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
        $predicate = " RFS='" . htmlspecialchars($rfsId) . "' AND START_DATE <= CURRENT_DATE AND TOTAL_HOURS is not null AND END_DATE is not null " ;
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

        $allocatorNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
        $emailEntry = "You have been assigned as the Project Manager for RFS &&rfs&& by $allocatorNotesid ";
        $emailPattern = array('RFS_ID'=>'/&&rfs&&/');
        emailNotifications::sendRfsNotification($rfsId,$emailEntry, $emailPattern);

        $messages = ob_get_clean();
        ob_start();
        $success = empty($messages) && $rs;

        $success ? sqlsrv_commit($GLOBALS['conn']) : sqlsrv_rollback($GLOBALS['conn']);
        sqlsrv_commit($GLOBALS['conn'],$autocommit);
        break;
}

$response = array('success'=>$success,'rfsId' => $rfsId, 'messages'=>$messages);

ob_clean();
echo json_encode($response);