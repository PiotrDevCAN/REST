<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use rest\staticOrganisationTable;
use rest\staticOrganisationRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$organisationTable  = new staticOrganisationTable(allTables::$STATIC_ORGANISATION);
$organisationTableRecord = new staticOrganisationRecord();
$organisationTableRecord->setFromArray(array('ORGANISATION'=>$_POST['ORGANISATION'],'SERVICE'=>$_POST['SERVICE'],'STATUS'=>$_POST['statusRadio']));

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $db2result = $organisationTable->insert($organisationTableRecord);
} else {
   $db2result = $organisationTable->update($organisationTableRecord);
}

if(!$db2result){
    echo print_r(sqlsrv_errors());
    echo print_r(sqlsrv_errors());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'mode'=>$_POST['mode'],'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);