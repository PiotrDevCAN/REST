<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use rest\staticOrganisationRecord;
use rest\staticOrganisationTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$table  = new staticOrganisationTable(allTables::$STATIC_ORGANISATION_SERVICE);
$record = new staticOrganisationRecord();
$record->setFromArray(array('ORGANISATION'=>$_POST['ORGANISATION']));

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