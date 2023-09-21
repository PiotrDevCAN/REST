<?php
use itdq\Trace;
use rest\resourceRequestDiaryTable;
use rest\allTables;
use itdq\DbTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$sql = " SELECT RFS_END_DATE ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS ;
$sql.= " WHERE RFS_ID='" . htmlspecialchars($_POST['rfs']) . "' ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, 'ajax', __FILE__, $sql);
}

$row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$endDate = !empty($row['RFS_END_DATE']) ? $row['RFS_END_DATE'] : null;

$messages = ob_get_clean();
ob_start();
$response = array('rfsEndDate'=>$endDate,'messages'=>$messages);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);