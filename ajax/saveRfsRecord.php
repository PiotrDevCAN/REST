<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;
use itdq\FormClass;
use itdq\Loader;

set_time_limit(0);
ob_start();

$parmsTrimmed = array_map('trim', $_POST);

// $rfsId = !empty($_POST['RFS_ID']) ? trim($_POST['RFS_ID']) : null;
// $projectTitle = !empty($_POST['PROJECT_TITLE']) ? trim($_POST['PROJECT_TITLE']) : null;
// $requestorName = !empty($_POST['REQUESTOR_NAME']) ? trim($_POST['REQUESTOR_NAME']) : null;
// $requestorEmail = !empty($_POST['REQUESTOR_EMAIL']) ? trim($_POST['REQUESTOR_EMAIL']) : null;
// $valueStream = !empty($_POST['VALUE_STREAM']) ? trim($_POST['VALUE_STREAM']) : null;

$rfsEndDate = !empty($_POST['RFS_END_DATE']) ? trim($_POST['RFS_END_DATE']) : null;

$rfsType = !empty($_POST['RFS_TYPE']) ? trim($_POST['RFS_TYPE']) : null;
$rfsStatus = !empty($_POST['RFS_STATUS']) ? trim($_POST['RFS_STATUS']) : null;

$invalidRfsType = !in_array($rfsType, rfsRecord::$rfsType);
$invalidRfsStatus = !in_array($rfsStatus, rfsRecord::$rfsStatus);
$invalidRfsEndDate = rfsTable::validateDate($rfsEndDate) === false;

// default validation values
$saveResponse = false;
$create = false;
$update = false;

switch (true) {
    case $invalidRfsType:
        $messages = 'Cannot save RFS Record with provided RFS Type value.';
        break;
    case $invalidRfsStatus:
        $messages = 'Cannot save RFS Record with provided RFS Status value.';
        break;
    case $invalidRfsEndDate:
        $messages = 'Cannot save RFS Record with provided RFS End Date value.';
        break;
    default:
            
        $loader = new Loader();
        $rfsRecord = new rfsRecord();

        $allBusinessUnit = $loader->loadIndexed('BUSINESS_UNIT','VALUE_STREAM', allTables::$STATIC_VALUE_STREAM);
        $businessUnit = isset($_POST['VALUE_STREAM']) ? $allBusinessUnit[$_POST['VALUE_STREAM']]: null;
        
        $rfsRecord->setFromArray($parmsTrimmed);
        $rfsRecord->setFromArray(array('BUSINESS_UNIT'=>$businessUnit));
        
        $rfsTable = new rfsTable(allTables::$RFS);
        if(trim($_POST['mode'])==FormClass::$modeEDIT){
            $rfsTable = new rfsTable(allTables::$RFS);
            $rfsData = $rfsTable->getRecord($rfsRecord);
            $rfsRecord->setFromArray($rfsData);
            $rfsRecord->setFromArray($_POST);
            $rfsRecord->setFromArray(array('BUSINESS_UNIT'=>$businessUnit));
            $saveResponse = $rfsTable->update($rfsRecord);
            $saveResponse = $saveResponse ? true : false;
            $create = false;
            $update = true;
        } else {
            $saveResponse  = $rfsTable->insert($rfsRecord);
            $create = true;
            $update = false;
        }
        $messages = ob_get_clean();
        break;
}

ob_start();

$response = array(
    'rfsId' => $parmsTrimmed['RFS_ID'],
    'saveResponse' => $saveResponse, 
    'messages'=>$messages,
    'create'=>$create,
    'update'=>$update,
    'new'=>true
);

ob_clean();
echo json_encode($response);