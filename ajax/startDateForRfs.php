<?php
use itdq\Trace;
use rest\resourceRequestDiaryTable;
use rest\allTables;
use itdq\DbTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$sql = " SELECT RFS_START_DATE ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS ;
$sql.= " WHERE RFS_ID='" . htmlspecialchars($_POST['rfs']) . "' ";

$rs = db2_exec($GLOBALS['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, 'ajax', __FILE__, $sql);
}

$row = db2_fetch_assoc($rs);

$endDate = !empty($row['RFS_START_DATE']) ? $row['RFS_START_DATE'] : null;

$messages = ob_get_clean();
ob_start();
$response = array('rfsEndDate'=>$endDate,'messages'=>$messages);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);