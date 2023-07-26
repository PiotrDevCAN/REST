<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use rest\staticBespokeRateRecord;
use rest\staticBespokeRateTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$table  = new staticBespokeRateTable(allTables::$BESPOKE_RATES);
$record = new staticBespokeRateRecord();
$additionalFields = array(
    'BESPOKE_RATE_ID'=>$_POST['ID'],
    'RFS_ID'=>$_POST['RFS_ID'],
    'RESOURCE_REFERENCE'=>$_POST['RESOURCE_REQUEST'],
    'PS_BAND_ID'=>$_POST['PS_BAND_ID']
);
$record->setFromArray($additionalFields);

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $db2result = $table->insert($record);
} else {
    $recordData = $table->getRecord($record);
    $record->setFromArray($recordData);
    $record->setFromArray($additionalFields);
    $db2result = $table->update($record);
}

if(!$db2result){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'mode'=>$_POST['mode'],'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);