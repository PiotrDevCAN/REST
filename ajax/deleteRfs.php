<?php
namespace ajax;
use itdq\Loader;
use itdq\BluePages;
use rest\allTables;
use rest\rfsTable;
use rest\resourceRequestHoursTable;
use rest\resourceRequestTable;
use rest\resourceRequestDiaryTable;
use rest\emailNotifications;

set_time_limit(0);

ob_start();

$rrhTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$rrTable  = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$rfsTable = new rfsTable(allTables::$RFS);

$loader = new Loader();

$allRequests = $loader->load('RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS," RFS='" . htmlspecialchars($_POST['RFS_ID']) . "' ");

// $allocatorNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
$allocatorNotesid = $_SESSION['ssoEmail'];
$emailEntry = "A Resource Request &&rr&& linked to RFS &&rfs&& has been deleted by $allocatorNotesid ";
$emailPattern = array('RESOURCE_REFERENCE'=>'/&&rr&&/','RFS'=>'/&&rfs&&/');

foreach ($allRequests as $resourceReference) {
    emailNotifications::sendNotification($resourceReference, $emailEntry, $emailPattern);    
    $diaryEntry =  $resourceReference . " was deleted because it's owning RFS " . $_POST['RFS_ID'] . " was deleted by " . $allocatorNotesid;
    resourceRequestDiaryTable::insertEntry($diaryEntry, $resourceReference);
    $rrhTable->deleteData(" RESOURCE_REFERENCE='" . htmlspecialchars($resourceReference) . "'",true);
    $rrTable->deleteData(" RESOURCE_REFERENCE='" . htmlspecialchars($resourceReference) . "'",true );    
}

$rfsTable->deleteData(" RFS_ID='" . htmlspecialchars($_POST['RFS_ID']) . "'",true );

$messages = ob_get_clean();

$response = array('rfsId' => $_POST['RFS_ID'], 'messages'=>$messages);

echo json_encode($response);