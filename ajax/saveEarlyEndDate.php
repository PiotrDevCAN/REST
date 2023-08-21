<?php

use itdq\Trace;
use rest\resourceRequestDiaryTable;
use rest\allTables;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
$sql.= " SET END_DATE = DATE('" . htmlspecialchars($_POST['endEarlyDate']) . "') ";
$sql.= " WHERE RESOURCE_REFERENCE = '" . htmlspecialchars($_POST['resourceReference']) . "' ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

$success = $rs ? true : false;

$diaryEntry = "End Date set to " . $_POST['endEarlyDate'];
$diaryRef = resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['resourceReference']);

$response = array('success'=>$success, 'sql'=>$sql, 'diaryRef'=>$diaryRef);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);