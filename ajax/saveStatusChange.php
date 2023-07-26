<?php


use rest\allTables;
use itdq\Trace;
use rest\resourceRequestTable;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

// $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
// $sql .= " SET STATUS='" . db2_escape_string(trim($_POST['statusRadio'])) . "' " ;
// $sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string(trim($_POST['statusChangeRR'])) . "  ";

// $statusUpdate = db2_exec($GLOBALS['conn'], $sql);

// if(!$statusUpdate){
//     echo db2_stmt_error();
//     echo db2_stmt_errormsg();
// }

$statusUpdate = resourceRequestTable::setRequestStatus($_POST['statusChangeRR'],$_POST['statusRadio']);

$messages = ob_get_clean();
ob_start();

$response = array('success'=>$statusUpdate,'messages'=>$messages);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);