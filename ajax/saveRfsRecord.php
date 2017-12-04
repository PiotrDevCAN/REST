<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;
use itdq\FormClass;
set_time_limit(0);
ob_start();

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