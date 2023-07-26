<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Loader;
use rest\allTables;
use rest\rfsPcrRecord;
use rest\rfsPcrTable;

set_time_limit(0);
ob_start();

$loader = new Loader();

$parmsTrimmed = array_map('trim', $_POST);

$pcrId = !empty($_POST['PCR_ID']) ? trim($_POST['PCR_ID']) : null;
$rfsId = !empty($_POST['RFS_ID']) ? trim($_POST['RFS_ID']) : null;

// rfs exists check
$rfsAlreadyExists = false;
// if(trim($_POST['mode'])==FormClass::$modeEDIT){
//     // skip this check while updating a record
// } else {
//     $exists = $loader->load('RFS_ID', allTables::$RFS, "RFS_ID='$rfsId'");
//     foreach ($exists as $value) {
//         if (trim($value) == $rfsId) {
//             $rfsAlreadyExists = true;
//         }
//     }
// }

$pcrNumber = !empty($_POST['PCR_NUMBER']) ? trim($_POST['PCR_NUMBER']) : null;
$pcrStartDate = !empty($_POST['PCR_START_DATE']) ? trim($_POST['PCR_START_DATE']) : null;
$pcrEndDate = !empty($_POST['PCR_END_DATE']) ? trim($_POST['PCR_END_DATE']) : null;
$pcrAmount = !empty($_POST['PCR_AMOUNT']) ? trim($_POST['PCR_AMOUNT']) : null;
$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';

$invalidRfsId = empty($rfsId);
$invalidPcrNumber = empty($pcrNumber);
$invalidPcrStartDate = rfsPcrTable::validateDate($pcrStartDate) === false;
$invalidPcrEndDate = rfsPcrTable::validateDate($pcrEndDate) === false;
$invalidPcrAmount = empty($pcrAmount);

// clean all potential errors
ob_clean();

// default validation values
$saveResponse = false;
$create = false;
$update = false;

switch (true) {
    case $rfsAlreadyExists:
        $messages = 'Cannot save PCR Record due to a record with provided PCR_ID already exists.';
        break;
    case $invalidRfsId:
        $messages = 'Cannot save PCR Record with provided PCR RFS_ID value.';
        break;
    case $invalidPcrNumber:
        $messages = 'Cannot save PCR Record with provided PCR Number value.';
        break;
    case $invalidPcrStartDate:
        $messages = 'Cannot save PCR Record with provided PCR Start Date value.';
        break;
    case $invalidPcrEndDate:
        $messages = 'Cannot save PCR Record with provided PCR End Date value.';
        break;
    case $invalidPcrAmount:
        $messages = 'Cannot save PCR Record with provided PCR Amount value.';
        break;
    default:
        
        // switch ($mode) {
        //     case FormClass::$modeDEFINE:
        //         break;
        //     case FormClass::$modeEDIT:
        //         break;
        //     default:
        //         echo 'Cannot save PCR with provided Mode value.';
        //         break;
        // }

        if (!empty($pcrId)) {
            $mode = FormClass::$modeEDIT;
        } else {
            $mode = FormClass::$modeDEFINE;
        }
        
        $rfsPcrRecord = new rfsPcrRecord();
        $rfsPcrRecord->setFromArray($parmsTrimmed);
        $rfsPcrTable = new rfsPcrTable(allTables::$RFS_PCR);
        // if(trim($_POST['mode'])==FormClass::$modeEDIT){
        if(trim($mode)==FormClass::$modeEDIT){
            $rfsData = $rfsPcrTable->getRecord($rfsPcrRecord);
            $rfsPcrRecord->setFromArray($rfsData);
            $rfsPcrRecord->setFromArray($_POST);
            $saveResponse = $rfsPcrTable->update($rfsPcrRecord);
            $saveResponse = $saveResponse ? true : false;
            $create = false;
            $update = true;
        } else {
            $saveResponse  = $rfsPcrTable->insert($rfsPcrRecord);
            $create = true;
            $update = false;
        }
        $messages = ob_get_clean();
        break;
}

ob_start();

$response = array(
    'rfsId' => $parmsTrimmed['RFS_ID'],
    'pcrId' => db2_last_insert_id($GLOBALS['conn']),
    'pcrNumber' => $pcrNumber,
    'pcrStartDate' => $pcrStartDate,
    'pcrEndDate' => $pcrEndDate,
    'pcrAmount' => $pcrAmount,
    'saveResponse' => $saveResponse, 
    'messages'=>$messages,
    'create'=>$create,
    'update'=>$update
);

ob_clean();
echo json_encode($response);