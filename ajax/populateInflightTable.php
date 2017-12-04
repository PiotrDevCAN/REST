<?php
use rest\allTables;
use rest\inflightProjectsTable;
use rest\uploadLogTable;
use rest\uploadLogRecord;

set_time_limit(0);

// include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
// include_once '../itdq/FormClass.php';
// include_once '../itdq/DbRecord.php';
// include_once '../itdq/DbTable.php';
// include_once '../itdq/log.php';
// include_once '../itdq/trace.php';
// include_once '../itdq/AllItdqTables.php';

// include_once '../rest/inflightProjectsTable.php';
// include_once '../rest/inflightProjectsRecord.php';
// include_once '../rest/uploadLogTable.php';
// include_once '../rest/uploadLogRecord.php';
// include_once '../rest/allTables.php';

// include_once '../rest/allTables.php';

session_start();

ob_start();
include_once '../connect.php';

$inflightTable = new inflightProjectsTable($_POST['tableName']);

$data = $inflightTable->returnAsArray();

$uploadLogTable = new uploadLogTable(allTables::$UPLOAD_LOG);
$detailsOfLastLoad = $uploadLogTable->wasLastLoadSuccesssful();

if($detailsOfLastLoad){
    $lastCompletedLogRecord = $detailsOfLastLoad['lastCompletedLogRecord'];
    $lastLoadAttempted      = $detailsOfLastLoad['lastLoadLogRecord'];
    $lastLoadSuccessful     = $detailsOfLastLoad['Successful'];
}


$messages = ob_get_clean();

$response = array('messages'=>$messages
                 ,'lastLoad'=>array('tableName'=>trim($lastCompletedLogRecord->UPLOAD_TABLENAME),'fileName'=>trim($lastCompletedLogRecord->UPLOAD_FILENAME)
                                                 ,'intranet'=>trim($lastCompletedLogRecord->UPLOAD_INTRANET),'timestamp'=>trim($lastCompletedLogRecord->UPLOAD_TIMESTAMP))
                 ,'lastAttempted'=>array('tableName'=>trim($lastLoadAttempted->UPLOAD_TABLENAME),'fileName'=>trim($lastLoadAttempted->UPLOAD_FILENAME)
                                        ,'intranet'=>trim($lastLoadAttempted->UPLOAD_INTRANET),'timestamp'=>trim($lastLoadAttempted->UPLOAD_TIMESTAMP ))
                ,'lastloadSuccessful'=> $detailsOfLastLoad['Successful']
                ,'data'=>$data
                );


ob_clean();
echo json_encode($response);