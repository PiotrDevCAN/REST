<?php
use rest\allTables;
use rest\inflightProjectsTable;
use rest\uploadLogTable;
use rest\uploadLogRecord;
set_time_limit(0);
ob_start();

$tableName = isset($_POST['tableName']) ? $_POST['tableName'] : 'INFLIGHT_PROJECTS';

$inflightTable = new inflightProjectsTable($tableName);

$data = $inflightTable->returnAsArray();

$uploadLogTable = new uploadLogTable(allTables::$UPLOAD_LOG);
$detailsOfLastLoad = $uploadLogTable->wasLastLoadSuccesssful();

if($detailsOfLastLoad){
    $lastCompletedLogRecord = $detailsOfLastLoad['lastCompletedLogRecord'];
    $lastLoadAttempted      = $detailsOfLastLoad['lastLoadLogRecord'];
}


$messages = ob_get_clean();

$response = array('messages'=>$messages
                  ,'lastLoad'=>array('tableName'=>trim($lastCompletedLogRecord->UPLOAD_TABLENAME),'fileName'=>trim($lastCompletedLogRecord->UPLOAD_FILENAME)
                                                  ,'intranet'=>trim($lastCompletedLogRecord->UPLOAD_INTRANET),'timestamp'=>trim($lastCompletedLogRecord->UPLOAD_TIMESTAMP))
                  ,'lastAttempted'=>array('tableName'=>trim($lastLoadAttempted->UPLOAD_TABLENAME),'fileName'=>trim($lastLoadAttempted->UPLOAD_FILENAME)
                                         ,'intranet'=>trim($lastLoadAttempted->UPLOAD_INTRANET),'timestamp'=>trim($lastLoadAttempted->UPLOAD_TIMESTAMP ))
                 ,'lastloadSuccessful'=> $detailsOfLastLoad['Successful']
//                 ,'data'=>$data
                );


ob_clean();
var_dump($response);
echo "<hr/>";
var_dump($data);
echo "<hr/>";

$dataJson = json_encode($data);
var_dump($dataJson);

$json =  json_encode($response);
var_dump($json);
die('the end');
