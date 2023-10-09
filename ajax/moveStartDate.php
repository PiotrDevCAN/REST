<?php


use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;
use itdq\BluePages;
use rest\emailNotifications;

set_time_limit(0);
ob_start();
/*
 * If the new END DATE is earlier than the current End Date, just delete the records we don't need,
 *
 * If End Date is LATER than current End Date, then we need to insert some more records.
 *
 * Finally, need to update the RR itself to reflect the new END DATE. *
 *
 */

$startDateObj = new DateTime($_POST['startDate']);       // New Start Date
$startDateWasObj = new DateTime($_POST['startDateWas']); // Original Start Date

$rrHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

if (sqlsrv_begin_transaction($GLOBALS['conn']) === false ) {
    die( print_r( sqlsrv_errors(), true ));
}

echo $startDateObj->format('Y-m-d');
echo $startDateWasObj->format('Y-m-d');

echo $startDateWasObj > $startDateObj;

if($startDateWasObj < $startDateObj){
    echo "Push Start Date Out";
    $movement = " pushed out to ";
    // They've moved the date in - so just delete dates.
    $predicate = " RESOURCE_REFERENCE=" . htmlspecialchars($_POST['resourceReference']) . " and \"WEEK_ENDING_FRIDAY\" < DATE('". htmlspecialchars($startDateObj->format('Y-m-d')) ."') ";
    $rrHoursTable->deleteData($predicate);
    $weeksSaved = 0;
} else {
    echo "Bring Start Forward";
    $movement = " brought forward to ";
    // they are adding weeks.
    $weeksSaved = $rrHoursTable->createResourceRequestHours($_POST['resourceReference'], $startDateObj->format('Y-m-d'),$startDateWasObj->format(('Y-m-d')), $_POST['hrsPerWeek'],false);
}
resourceRequestTable::setStartDate($_POST['resourceReference'], $startDateObj->format('Y-m-d'));

sqlsrv_commit($GLOBALS['conn']);

$diaryEntry = "Start Date was " . $movement . $_POST['startDate'] . " from " . $_POST['startDateWas'];
$diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['resourceReference']);

// $modifierNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
$modifierNotesid = $_SESSION['ssoEmail'];

$emailEntry = "The start date for your allocation to RFS &&rfs&& under Resource Request &&ref&& has been modified by $modifierNotesid";
$emailEntry.= "<br/>From: " .  $startDateWasObj->format('d M Y');
$emailEntry.= "<br/><b>To: " . $startDateObj->format('d M Y') . "</b>";
$emailPattern = array('RFS'=>'/&&rfs&&/','RESOURCE_REFERENCE'=>'/&&ref&&/');
emailNotifications::sendNotification($_POST['resourceReference'],$emailEntry, $emailPattern); 

$messages = ob_get_clean();
ob_start();

$response = array( 'WeeksSaved'=> $weeksSaved, 'messages'=>$messages, 'DiaryRef'=>$diaryRef);

ob_clean();
echo json_encode($response);