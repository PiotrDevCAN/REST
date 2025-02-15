<?php


use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;
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

$endDateObj = new DateTime($_POST['endDate']);
$endDateWasObj = new DateTime($_POST['endDateWas']);

$rrHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

if (sqlsrv_begin_transaction($GLOBALS['conn']) === false ) {
    die( print_r( sqlsrv_errors(), true ));
}

echo $endDateObj->format('Y-m-d');
echo $endDateWasObj->format('Y-m-d');

echo $endDateWasObj > $endDateObj;

$movement = ' ';
$weeksSaved = 0;

if($endDateWasObj > $endDateObj){
    echo "Move date In";
    $movement = " pulled back to ";
    // They've moved the date in - so just delete dates.
    $predicate = " RESOURCE_REFERENCE=" . htmlspecialchars($_POST['resourceReference']) . " and \"DATE\" > '". htmlspecialchars($endDateObj->format('Y-m-d')) ."' ";
    $rrHoursTable->deleteData($predicate);
    $weeksSaved = 0;
} elseif($endDateWasObj < $endDateObj) {
    echo "Moved Out";
    $movement = " pushed out to ";
    // they are adding weeks.
    $weeksSaved = $rrHoursTable->createResourceRequestHours($_POST['resourceReference'], $endDateWasObj->format('Y-m-d'), $endDateObj->format(('Y-m-d')), $_POST['hrsPerWeek'], false);
}
resourceRequestTable::setEndDate($_POST['resourceReference'], $endDateObj->format('Y-m-d'));

sqlsrv_commit($GLOBALS['conn']);

$diaryEntry = "End Date was " . $movement . $_POST['endDate'] . " from " . $_POST['endDateWas'];
$diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['resourceReference']);

// $modifierNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
$modifierNotesid = $_SESSION['ssoEmail'];

$emailEntry = "The end date for your allocation to RFS &&rfs&& under Resource Request &&ref&& has been modified by $modifierNotesid";
$emailEntry.= "<br/>From: " .  $endDateWasObj->format('d M Y');
$emailEntry.= "<br/><b>To: " . $endDateObj->format('d M Y') . "</b>";
$emailPattern = array('RFS'=>'/&&rfs&&/','RESOURCE_REFERENCE'=>'/&&ref&&/');
emailNotifications::sendNotification($_POST['resourceReference'],$emailEntry, $emailPattern); 

$messages = ob_get_clean();
ob_start();

$response = array( 'WeeksSaved'=> $weeksSaved, 'messages'=>$messages, 'DiaryRef'=>$diaryRef);

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

echo json_encode($response);