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
$pipelineLiveArchive = !empty($_POST['pipelineLiveArchive']) ? $_POST['pipelineLiveArchive'] : 'live' ;
$pipelineLive = $pipelineLiveArchive=='live' ? rfsRecord::RFS_STATUS_LIVE : rfsRecord::RFS_STATUS_PIPELINE;
$pipelineLive = $pipelineLiveArchive=='archive' ? null : $pipelineLive;
$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
$organisation = !empty($_POST['organisation']) ? $_POST['organisation'] : null;
$businessUnit = !empty($_POST['businessunit']) ? $_POST['businessunit'] : null;


if (empty($rfsId) && empty($organisation) && empty($businessUnit)) {
    $response = array(
        'messages' => 'User hasnt selected from the drop downs.',
        'badrecords' => 0,
        "data" => array()
    );
} else {

    $rfsId        = $rfsId=='All'        ? null : $rfsId;
    $organisation = $organisation=='All' ? null : $organisation;
    $businessUnit = $businessUnit=='All' ? null : $businessUnit;

    PhpMemoryTrace::reportPeek(__FILE__, __LINE__);

    $predicate  =   empty($rfsId)  ? rfsTable::rfsPredicateFilterOnPipeline($pipelineLive) : null;
    $predicate .= ! empty($rfsId) ? " AND RFS='" . db2_escape_string($rfsId) . "' " : null;
    $predicate .= ! empty($organisation) ? " AND ORGANISATION='" . db2_escape_string($organisation) . "' " : null;
    $predicate .= ! empty($businessUnit) ? " AND BUSINESS_UNIT='" . db2_escape_string($businessUnit) . "' " : null;
    
    
    error_log(__FILE__ . ":" . __LINE__ . ":" . $predicate);
    
    
    $dataAndSql = $resourceRequestTable->returnAsArray($startDate, $endDate, $predicate, $pipelineLiveArchive);
    $data = $dataAndSql['data'];
    $sql = $dataAndSql['sql'];
   

    PhpMemoryTrace::reportPeek(__FILE__, __LINE__);

    $testJson = json_encode($data);
    $badRecords = 0;
    if (! $testJson) {
        foreach ($data as $ref => $record) {
            $testRecord = json_encode($record);
            if (! $testRecord) {
                $badRecords ++;
                unset($data[$ref]);
            }
        }
    }

    PhpMemoryTrace::reportPeek(__FILE__, __LINE__);

    echo "Bad Records removed:$badRecords";

    $messages = ob_get_clean();
    ob_start();

    $response = array(
        'messages' => $messages,
        'badrecords' => $badRecords,
        "data" => $data,
        "sql" => $sql
    );
}

$json = json_encode($response);

if($json){
    echo $json;
} else {
    echo json_encode(array('code'=>json_last_error(),'msg'=>json_last_error_msg()));
}

PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);