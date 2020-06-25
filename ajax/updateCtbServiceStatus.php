<?php
use itdq\Trace;
use rest\StaticCountryMarketRecord;
use rest\StaticCountryMarketTable;
use rest\allTables;
use itdq\FormClass;
use itdq\DbTable;
use rest\StaticCtbServiceTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$toggleResult = $_POST['currentStatus']==StaticCtbServiceTable::ENABLED ? StaticCtbServiceTable::disableService($_POST['ORGANISATION'],$_POST['CTB_SUB_SERVICE']) : StaticCtbServiceTable::enableService($_POST['CTB_SERVICE'],$_POST['CTB_SUB_SERVICE']);

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