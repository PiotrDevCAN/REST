<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;

set_time_limit(0);
ob_start();
$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

$startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = !empty($_POST['endDate']) ? $_POST['endDate'] : null;

$data = $resourceRequestTable->returnAsArray($startDate,$endDate);

$testJson = json_encode($data);
$badRecords = 0;
$badRecordArray = array();
if (!$testJson){
    foreach ($data as $ref => $record){
        $testRecord = json_encode($record);
        if(!$testRecord){
            $badRecords++;
            unset($data[$ref]);
        }
    }
}

echo "Bad Records removed:$badRecords";


$messages = ob_get_clean();

$response = array('messages'=>$messages,'badrecords'=>$badRecords,"data"=>$data);

ob_clean();

$json = json_encode($response);

if($json){
    echo $json;
} else {
    echo json_encode(array('code'=>json_last_error(),'msg'=>json_last_error_msg()));
}
