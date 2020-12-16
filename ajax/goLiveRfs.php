<?php

use itdq\DbTable;
use itdq\BluePages;
use rest\emailNotifications;
use rest\allTables;
use rest\rfsRecord;
use rest\resourceRequestHoursTable;

set_time_limit(0);
ob_start();

$autocommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);
// If the start date is in the past - bring it up to today.
$sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
$sql.= " SET START_DATE = CURRENT_DATE ";
$sql.= " WHERE RFS = '" . db2_escape_string($_POST['rfsid']) . "' ";
$sql.= " AND START_DATE < CURRENT_DATE ";

$rs = db2_exec($GLOBALS['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
}

resourceRequestHoursTable::removeHoursRecordsForRfsPriorToday($_POST['rfsid']);

$sql = " UPDATE ";
$sql.=   $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
$sql.= " SET RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' ";
$sql.= "   , REQUESTOR_NAME = '" . db2_escape_string(trim($_POST['requestorName'])) . "' " ;
$sql.= "   , REQUESTOR_EMAIL = '" . db2_escape_string(trim($_POST['requestorEmail'])) . "' " ;
$sql.= " WHERE RFS_ID='" . db2_escape_string(trim($_POST['rfsid'])) . "' ";

$rs = db2_exec($GLOBALS['conn'], $sql);

if(!$rs){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$allocatorNotesid = BluePages::getNotesidFromIntranetId($_SESSION['ssoEmail']);
$emailEntry = "You have been assigned as the Project Manager for RFS &&rfs&& by $allocatorNotesid ";
$emailPattern = array('RFS_ID'=>'/&&rfs&&/');
emailNotifications::sendRfsNotification($_POST['rfsid'],$emailEntry, $emailPattern);




$messages = ob_get_clean();
ob_start();
$success = empty($messages) && $rs;

$success ? db2_commit($GLOBALS['conn']) : db2_rollback($GLOBALS['conn']);
db2_autocommit($GLOBALS['conn'],$autocommit);

$response = array('success'=>$success,'rfsId' => $_POST['rfsid'], 'Messages'=>$messages);

ob_clean();
echo json_encode($response);