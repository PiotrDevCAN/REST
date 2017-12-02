<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;
use itdq\FormClass;

session_start();

set_time_limit(0);

include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';
include_once '../itdq/Date.php';

include_once '../rest/rfsTable.php';
include_once '../rest/rfsRecord.php';
include_once '../rest/allTables.php';

ob_start();

include_once '../connect.php';

$rfsRecord = new rfsRecord();
$rfsRecord->setFromArray($_POST);
$rfsTable = new rfsTable(allTables::$RFS);
if(trim($_POST['mode'])==FormClass::$modeEDIT){
    $rfsTable = new rfsTable(allTables::$RFS);
    $rfsData = $rfsTable->getRecord($rfsRecord);
    $rfsRecord->setFromArray($rfsData);
    $rfsRecord->setFromArray($_POST);
    $saveResponse  = $rfsTable->update($rfsRecord);
    $saveResponse = $saveResponse ? true : false;
    $update = true;
} else {
    $saveResponse  = $rfsTable->insert($rfsRecord);
    $update = false;
}
$messages = ob_get_clean();

$response = array('rfsId' => $_POST['RFS_ID'], 'saveResponse' => $saveResponse, 'Messages'=>$messages,'Update'=>$update);

ob_clean();
echo json_encode($response);