<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use rest\staticValueStreamBusinessUnitRecord;
use rest\staticValueStreamBusinessUnitTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$table  = new staticValueStreamBusinessUnitTable(allTables::$STATIC_VALUE_STREAM_BUSINESS_UNIT);
$record = new staticValueStreamBusinessUnitRecord();
$record->setFromArray(array('VALUE_STREAM'=>$_POST['VALUE_STREAM'],'BUSINESS_UNIT'=>$_POST['BUSINESS_UNIT']));

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $db2result = $table->insert($record);
} else {
   $db2result = $table->update($record);
}

if(!$db2result){
    echo json_encode(sqlsrv_errors());
    echo json_encode(sqlsrv_errors());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'mode'=>$_POST['mode'],'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);