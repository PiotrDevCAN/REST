<?php

use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use itdq\Trace;
use itdq\AuditTable;
use rest\resourceRequestDiaryTable;
use itdq\BluePages;
use rest\emailNotifications;


set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$clear = isset($_POST['clear']) ? $_POST['clear'] : null;

try {
    $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);   
    $currentResource = $resourceTable->getResourceName($_POST['RESOURCE_REFERENCE']);
    $allocatorNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
    
           
    if(empty($clear) && $currentResource && (strtolower($currentResource) != strtolower(trim($_POST['RESOURCE_NAME']))) && (substr($currentResource,0,5)!=='Delta')){        
        $emailEntry = "You have been <b>removed from</b> RFS &&rfs&& by $allocatorNotesid ";
        $emailPattern = array('RFS'=>'/&&rfs&&/');
        emailNotifications::sendNotification($_POST['RESOURCE_REFERENCE'],$emailEntry, $emailPattern);
    }
        
    if(!empty($clear)){
        $emailEntry = "You have been <b>unallocated</b> from RFS &&rfs&& by $allocatorNotesid ";
        $emailEntry.= print_r($clear,true);
        $emailPattern = array('RFS'=>'/&&rfs&&/');
        emailNotifications::sendNotification($_POST['RESOURCE_REFERENCE'],$emailEntry, $emailPattern);         
    }
    
    $resourceTable->updateResourceName($_POST['RESOURCE_REFERENCE'], $_POST['RESOURCE_NAME'], $clear);    
    $diaryEntry = empty($clear) ?  $_POST['RESOURCE_NAME'] . " allocated to request" : " Resource name cleared";
    resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['RESOURCE_REFERENCE']);
    
    if(empty($clear)){
        $emailEntry = "You have been <b>allocated to</b> RFS &&rfs&& by $allocatorNotesid ";
        $emailPattern = array('RFS'=>'/&&rfs&&/');
        emailNotifications::sendNotification($_POST['RESOURCE_REFERENCE'],$emailEntry, $emailPattern); 
    }  
    
    $exception = false;
} catch (Exception $e) {
    $exception = $e->getMessage();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'resourceReference'=>$_POST['RESOURCE_REFERENCE'], 'resourceName' => $_POST['RESOURCE_NAME'], 'Messages'=>$messages, 'Exception'=> $exception) ;

AuditTable::audit(__FILE__ . "called by:" . $_SESSION['ssoEmail'] . " Response:" . print_r($response,true),AuditTable::RECORD_TYPE_AUDIT);


ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);