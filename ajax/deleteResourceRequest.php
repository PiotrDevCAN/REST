<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestDiaryTable;
use rest\resourceRequestHoursTable;
use rest\emailNotifications;

ob_start();
set_time_limit(0);

// $allocatorNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
$allocatorNotesid = $_SESSION['ssoEmail'];
$emailEntry = "A Resource Request linked to  RFS &&rfs&& has been deleted by $allocatorNotesid ";
$emailPattern = array('RFS'=>'/&&rfs&&/');
emailNotifications::sendNotification($_POST['RESOURCE_REFERENCE'],$emailEntry, $emailPattern);

$rrTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$rrTable->deleteData(" RESOURCE_REFERENCE='" . htmlspecialchars($_POST['RESOURCE_REFERENCE']) . "'",true );

$rrhTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$rrhTable->deleteData(" RESOURCE_REFERENCE='" . htmlspecialchars($_POST['RESOURCE_REFERENCE']) . "'",true);

$diaryEntry = !empty($_POST['RESOURCE_NAME']) ? $_POST['RESOURCE_NAME'] . " deleted " : "Unallocated request " . $_POST['RESOURCE_REFERENCE'] . " deleted";
resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['RESOURCE_REFERENCE']);

$messages = ob_get_clean();
ob_start();

$response = array(
    'rrId' => $_POST['RESOURCE_REFERENCE'],
    'messages'=>$messages
);

ob_clean();
echo json_encode($response);