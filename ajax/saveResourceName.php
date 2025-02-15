<?php

use itdq\Trace;
use itdq\AuditTable;
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestDiaryTable;
use rest\emailNotifications;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$clear = isset($_POST['clear']) ? $_POST['clear'] : null;
$resourceReference = isset($_POST['RESOURCE_REFERENCE']) ? $_POST['RESOURCE_REFERENCE'] : null;
$resourceName = isset($_POST['RESOURCE_NAME']) ? $_POST['RESOURCE_NAME'] : null;
$resourceEmailAddress = isset($_POST['RESOURCE_EMAIL_ADDRESS']) ? $_POST['RESOURCE_EMAIL_ADDRESS'] : null;
$resourceKynEmailAddress = isset($_POST['RESOURCE_KYN_EMAIL_ADDRESS']) ? $_POST['RESOURCE_KYN_EMAIL_ADDRESS'] : null;
$resourceCNUM = isset($_POST['RESOURCE_CNUM']) ? $_POST['RESOURCE_CNUM'] : null;

try {
    $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);   
    $currentResource = $resourceTable->getResourceName($resourceReference);
    $allocatorNotesid = $_SESSION['ssoEmail'];
    
    if(empty($clear) && $currentResource && (strtolower($currentResource) != strtolower(trim($resourceName))) && (substr($currentResource,0,5)!=='Delta')){        
        // resource removed notification
        $emailEntry = "You have been <b>removed from</b> RFS &&rfs&& by $allocatorNotesid ";
        $emailPattern = array('RFS'=>'/&&rfs&&/');
        emailNotifications::sendNotification($resourceReference, $emailEntry, $emailPattern);
    }
    
    if(!empty($clear)){
        // resource unallocated notification
        $emailEntry = "You have been <b>unallocated</b> from RFS &&rfs&& by $allocatorNotesid ";
        $emailPattern = array('RFS'=>'/&&rfs&&/');
        emailNotifications::sendNotification($resourceReference, $emailEntry, $emailPattern);         
    }
    
    $resourceTable->updateResourceName($resourceReference, $resourceName, $resourceEmailAddress, $resourceKynEmailAddress, $resourceCNUM, $clear);

    $diaryEntry = empty($clear) ?  $resourceName . " allocated to request" : " Resource name cleared";
    resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);
    
    if(empty($clear)){
        // resource allocated notification
        $emailEntry = "You have been <b>allocated to</b> RFS &&rfs&& by $allocatorNotesid ";
        $emailPattern = array('RFS'=>'/&&rfs&&/');
        emailNotifications::sendNotification($resourceReference, $emailEntry, $emailPattern); 
    }  
    
    $exception = false;
} catch (Exception $e) {
    $exception = $e->getMessage();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array(
    'success' => $success,
    'resourceReference'=> $resourceReference, 
    'resourceName' => $resourceName,
    'resourceEmail' => $resourceEmailAddress,
    'resourceKynEmail' => $resourceKynEmailAddress,
    'resourceCNUM' => $resourceCNUM,
    'messages'=>$messages, 
    'Exception'=> $exception
    ) ;

AuditTable::audit(__FILE__ . "called by:" . $_SESSION['ssoEmail'] . " Response:" . print_r($response,true),AuditTable::RECORD_TYPE_AUDIT);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);