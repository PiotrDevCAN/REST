<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use itdq\DbTable;
use rest\StaticCountryMarketRecord;
use rest\StaticCountryMarketTable;
use rest\StaticCtbServiceTable;
use rest\StaticCtbServiceRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$ctbServiceTable  = new StaticCtbServiceTable(allTables::$STATIC_ORGANISATION);
$ctbServiceRecord = new StaticCtbServiceRecord();
$ctbServiceRecord->setFromArray(array('ORGANISATION'=>$_POST['ORGANISATION'],'CTB_SUB_SERVICE'=>$_POST['CTB_SUB_SERVICE'],'STATUS'=>$_POST['statusRadio']));

if($_POST['mode']==FormClass::$modeDEFINE){
   $db2result = $ctbServiceTable->insert($ctbServiceRecord);
} else {
   $db2result = $ctbServiceTable->update($ctbServiceRecord);
}

if(!$db2result){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$messages = ob_get_flush();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'mode'=>$_POST['mode'],'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);