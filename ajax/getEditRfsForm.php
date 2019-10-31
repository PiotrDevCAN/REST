<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use itdq\DbTable;
use rest\rfsTable;
use rest\rfsRecord;


Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();
echo "<h2>RFS Definition Form</h2>";

if(isset($_REQUEST['rfsId'])){
    $mode = FormClass::$modeEDIT;
    $rfsTable = new rfsTable(allTables::$RFS);
    $rfsRecord = new rfsRecord();
    $rfsRecord->set('RFS_ID', $_REQUEST['rfsId']);
    $rfsData = $rfsTable->getRecord($rfsRecord);
    $rfsRecord->setFromArray($rfsData);

} else {
    $rfsRecord = new rfsRecord();
    $mode = FormClass::$modeDEFINE;
}

$rfsRecord->displayForm($mode);

$form = ob_get_clean();

$response = array('form'=>$form);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);