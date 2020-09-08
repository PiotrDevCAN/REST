<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;

session_start();
set_time_limit(0);
ob_start();

$sql = " UPDATE ";
$sql.=   $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
$sql.= " SET RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' ";
$sql.= " WHERE RFS_ID='" . db2_escape_string(trim($_POST['rfsid'])) . "' ";

$rs = db2_exec($GLOBALS['conn'], $sql);

if(!$rs){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages) && $rs;

$response = array('success'=>$success,'rfsId' => $_POST['rfsid'], 'Messages'=>$messages);

ob_clean();
echo json_encode($response);