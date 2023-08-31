<?php
use itdq\Trace;
use rest\staticOrganisationTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$toggleResult = $_POST['currentStatus']==staticOrganisationTable::ENABLED ? staticOrganisationTable::disableService($_POST['ORGANISATION'],$_POST['SERVICE']) : staticOrganisationTable::enableService($_POST['ORGANISATION'],$_POST['SERVICE']);

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