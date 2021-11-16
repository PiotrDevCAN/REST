<?php

use itdq\BluePagesSLAPHAPI;
use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;
use itdq\FormClass;
use itdq\Loader;

set_time_limit(0);
ob_start();

$loader = new Loader();

$parmsTrimmed = array_map('trim', $_POST);

$rfsId = !empty($_POST['RFS_ID']) ? trim($_POST['RFS_ID']) : null;

// rfs exists check
$rfsAlreadyExists = false;
if(trim($_POST['mode'])==FormClass::$modeEDIT){
    // skip this check while updating a record
} else {
    $exists = $loader->load('RFS_ID', allTables::$RFS, "RFS_ID='$rfsId'");
    foreach ($exists as $value) {
        if (trim($value) == $rfsId) {
            $rfsAlreadyExists = true;
        }
    }
}

$rfsRequestorEmail = !empty($_POST['REQUESTOR_EMAIL']) ? trim($_POST['REQUESTOR_EMAIL']) : null;
$rfsOriginalRequestorEmail = !empty($_POST['originalREQUESTOR_EMAIL']) ? trim($_POST['originalREQUESTOR_EMAIL']) : null;
$rfsType = !empty($_POST['RFS_TYPE']) ? trim($_POST['RFS_TYPE']) : null;
$rfsStatus = !empty($_POST['RFS_STATUS']) ? trim($_POST['RFS_STATUS']) : null;
$rfsEndDate = !empty($_POST['RFS_END_DATE']) ? trim($_POST['RFS_END_DATE']) : null;

$sp = strpos(strtolower($rfsRequestorEmail),'ocean');
if($sp === FALSE){
    // none ocean
    if ($rfsRequestorEmail == $rfsOriginalRequestorEmail) {
        // nothing has changed
        $invalidRequestorEmail = false;
    } else {        
        // invalid none ocean
        $invalidRequestorEmail = true;
    }
} else {
    // is ocean
    $data = BluePagesSLAPHAPI::getIBMDetailsFromIntranetId($rfsRequestorEmail);
    if (!empty($data)) {
        //valid ocean
        $invalidRequestorEmail = false;
    } else {
        // invalid ocean
        $invalidRequestorEmail = true;
    }
}

$invalidRfsType = !in_array($rfsType, rfsRecord::$rfsType);
$invalidRfsStatus = !in_array($rfsStatus, rfsRecord::$rfsStatus);
$invalidRfsEndDate = rfsTable::validateDate($rfsEndDate) === false;

// default validation values
$saveResponse = false;
$create = false;
$update = false;

switch (true) {
    case $rfsAlreadyExists:
        $messages = 'Cannot save RFS Record due to a record with provided RFS_ID already exists.';
        break; 
    case $invalidRequestorEmail:
        $messages = 'Cannot save RFS Record with provided RFS Requestor Email value.';
        break;
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