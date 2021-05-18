<?php
use itdq\Trace;
use rest\staticOrganisationTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$toggleResult = $_POST['currentStatus']==staticOrganisationTable::ENABLED ? staticOrganisationTable::disableService($_POST['ORGANISATION'],$_POST['SERVICE']) : staticOrganisationTable::enableService($_POST['ORGANISATION'],$_POST['SERVICE']);

if(!$toggleResult){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$messages = ob_get_flush();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);