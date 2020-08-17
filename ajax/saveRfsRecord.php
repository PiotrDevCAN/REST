<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;
use itdq\FormClass;
set_time_limit(0);
ob_start();

$loader = new Loader();
$allBusinessUnit = $loader->loadIndexed('BUSINESS_UNIT','VALUE_STREAM', allTables::$STATIC_VALUE_STREAM);
$businessUnit = isset($_POST['VALUE_STREAM']) ? $allBusinessUnit[$_POST['VALUE_STREAM']]: null;

$rfsRecord = new rfsRecord();
$parmsTrimmed = array_map('trim', $_POST);

$rfsRecord->setFromArray($parmsTrimmed);
$rfsTable = new rfsTable(allTables::$RFS);
if(trim($_POST['mode'])==FormClass::$modeEDIT){
    $rfsTable = new rfsTable(allTables::$RFS);
    $rfsData = $rfsTable->getRecord($rfsRecord);
    $rfsRecord->setFromArray($rfsData);
    $rfsRecord->setFromArray($_POST);
    $rfsRecord->setFromArray(array('BUSINESS_UNIT',$businessUnit));
    $saveResponse  = $rfsTable->update($rfsRecord);
    $saveResponse = $saveResponse ? true : false;
    $update = true;
} else {
    $saveResponse  = $rfsTable->insert($rfsRecord);
    $update = false;
}
$messages = ob_get_clean();
ob_start();

$response = array('rfsId' => $parmsTrimmed['RFS_ID'], 'saveResponse' => $saveResponse, 'Messages'=>$messages,'Update'=>$update,'new'=>true);

ob_clean();
echo json_encode($response);