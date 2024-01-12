<?php
use itdq\Trace;
use rest\staticOrganisationServiceTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$toggleResult = $_POST['currentStatus']==staticOrganisationServiceTable::ENABLED ? staticOrganisationServiceTable::disableService($_POST['ORGANISATION'],$_POST['SERVICE']) : staticOrganisationServiceTable::enableService($_POST['ORGANISATION'],$_POST['SERVICE']);

if(!$toggleResult){
    echo json_encode(sqlsrv_errors());
    echo json_encode(sqlsrv_errors());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);