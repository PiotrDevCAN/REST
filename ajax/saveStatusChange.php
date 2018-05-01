<?php


use rest\allTables;

set_time_limit(0);
ob_start();

$sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS;
$sql .= " SET STATUS='" . db2_escape_string(trim($_POST['statusRadio'])) . "' " ;
$sql .= " WHERE RESOURCE_REFERENCE=" . db2_escape_string(trim($_POST['statusChangeRR'])) . "  ";

$statusUpdate = db2_exec($_SESSION['conn'], $sql);

if(!$statusUpdate){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$messages = ob_get_clean();

$response = array('success'=>$statusUpdate,'Messages'=>$messages);

ob_clean();
echo json_encode($response);