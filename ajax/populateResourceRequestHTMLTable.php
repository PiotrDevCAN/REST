<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use itdq\Trace;
use rest\rfsTable;
use itdq\PhpMemoryTrace;
use rest\rfsRecord;

set_time_limit(0);
ini_set('memory_limit','300M');

ob_start();
PhpMemoryTrace::reportPeek(__FILE__,__LINE__);

Trace::pageOpening($_SERVER['PHP_SELF']);
$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

$startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = !empty($_POST['endDate']) ? $_POST['endDate'] : null;
$piplineLive = $_POST['pipelineLive']=='true' ? rfsRecord::RFS_STATUS_LIVE : rfsRecord::RFS_STATUS_PIPELINE;
$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
$ctbservice = !empty($_POST['ctbservice']) ? $_POST['ctbservice'] : null;

PhpMemoryTrace::reportPeek(__FILE__,__LINE__);

$predicate = rfsTable::rfsPredicateFilterOnPipeline($piplineLive);
$predicate.= !empty($rfsId) ? " AND RFS='" . db2_escape_string($rfsId) . "' " : null;
$predicate.= !empty($ctbservice) ? " AND ORGANISATION='" . db2_escape_string($ctbservice) . "' " : null;

$data = $resourceRequestTable->returnAsArray($startDate,$endDate,$predicate);

PhpMemoryTrace::reportPeek(__FILE__,__LINE__);


$testJson = json_encode($data);
$badRecords = 0;
if (!$testJson){
    foreach ($data as $ref => $record){
        $testRecord = json_encode($record);
        if(!$testRecord){
            $badRecords++;
            unset($data[$ref]);
        }
    }
}

PhpMemoryTrace::reportPeek(__FILE__,__LINE__);

echo "Bad Records removed:$badRecords";


$messages = ob_get_clean();
ob_start();

$response = array('messages'=>$messages,'badrecords'=>$badRecords,"data"=>$data);

$json = json_encode($response);

if($json){
    echo $json;
} else {
    echo json_encode(array('code'=>json_last_error(),'msg'=>json_last_error_msg()));
}

PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);