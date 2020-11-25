<?php


use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;

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

$autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

echo $endDateObj->format('Y-m-d');
echo $endDateWasObj->format('Y-m-d');

echo $endDateWasObj > $endDateObj;


if($endDateWasObj > $endDateObj){
    echo "Move date IN";
    // They've moved the date in - so just delete dates.
    $predicate = " RESOURCE_REFERENCE=" . db2_escape_string($_POST['resourceReference']) . " and \"DATE\" > DATE('". db2_escape_string($endDateObj->format('Y-m-d')) ."') ";
    $rrHoursTable->deleteData($predicate);
    $weeksSaved = 0;
} elseif($endDateWasObj < $endDateObj) {
    echo "Moved Out";
    // they are adding weeks.
    $weeksSaved = $rrHoursTable->createResourceRequestHours($_POST['resourceReference'], $endDateWasObj->format('Y-m-d'),$endDateObj->format(('Y-m-d')), $_POST['hrsPerWeek'],false);
}
resourceRequestTable::setEndDate($_POST['resourceReference'], $endDateObj->format('Y-m-d'));

$rrHoursTable->commitUpdates();


$diaryEntry = "End Date set to " . $_POST['endDate'];
$diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['resourceReference']);

db2_autocommit($GLOBALS['conn'],$autoCommit);

$messages = ob_get_clean();
ob_start();

$response = array( 'WeeksSaved'=> $weeksSaved, 'Messages'=>$messages, 'DiaryRef'=>$diaryRef);

ob_clean();
echo json_encode($response);