<?php
use rest\allTables;
use rest\resourceRequestTable;
use itdq\Trace;
use rest\rfsTable;
use rest\rfsRecord;
use itdq\PhpMemoryTrace;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
ini_set('memory_limit','1024M');
ob_start();
PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
Trace::pageOpening($_SERVER['PHP_SELF']);

$length = isset($_POST['length']) ? $_POST['length'] : false;
$start = isset($_POST['start']) ? $_POST['start'] : false;
$draw = isset($_POST['draw']) ? $_POST['draw'] : false;

$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

$startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = !empty($_POST['endDate']) ? $_POST['endDate'] : null;
$pipelineLiveArchive = !empty($_POST['pipelineLiveArchive']) ? $_POST['pipelineLiveArchive'] : 'live' ;
$pipelineLive = $pipelineLiveArchive=='live' ? rfsRecord::RFS_STATUS_LIVE : rfsRecord::RFS_STATUS_PIPELINE;
$pipelineLive = $pipelineLiveArchive=='archive' ? null : $pipelineLive;

// $rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
// $organisation = !empty($_POST['organisation']) ? $_POST['organisation'] : null;
// $businessUnit = !empty($_POST['businessunit']) ? $_POST['businessunit'] : null;

$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : 'All';
$organisation = !empty($_POST['organisation']) ? $_POST['organisation'] : 'All';
$businessUnit = !empty($_POST['businessunit']) ? $_POST['businessunit'] : 'All';

// if (empty($rfsId) && empty($organisation) && empty($businessUnit)) {
//     $response = array(
//         'messages' => 'User hasnt selected from the drop downs.',
//         'badrecords' => 0,
//         "data" => array()
//     );
// } else {

    $rfsId = $rfsId=='All' ? null : $rfsId;
    $organisation = $organisation=='All' ? null : $organisation;
    $businessUnit = $businessUnit=='All' ? null : $businessUnit;

    PhpMemoryTrace::reportPeek(__FILE__, __LINE__);

    $predicate  =   empty($rfsId) ? rfsTable::rfsPredicateFilterOnPipeline($pipelineLive) : null;
    $predicate .= ! empty($rfsId) ? " AND RFS='" . db2_escape_string($rfsId) . "' " : null;
    $predicate .= ! empty($organisation) ? " AND ORGANISATION='" . db2_escape_string($organisation) . "' " : null;
    $predicate .= ! empty($businessUnit) ? " AND BUSINESS_UNIT='" . db2_escape_string($businessUnit) . "' " : null;

    // merge all preducates
    $predicate .= $resourceRequestTable->prepareSearchPredicate() . $resourceRequestTable->prepareComplexSearchPredicate() . $resourceRequestTable->prepareOrderingPredicate();

    error_log(__FILE__ . ":" . __LINE__ . ":" . $predicate);

    $dataAndSql = $resourceRequestTable->returnAsArray($startDate, $endDate, $predicate, $pipelineLiveArchive, true, $length, $start);
    $data = $dataAndSql['data'];
    $sql = $dataAndSql['sql'];
    $recordsTotal = $dataAndSql['total'];
    $recordsFiltered = $dataAndSql['total'];

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
    echo " ".$_SESSION['peekUsage'];

    $messages = ob_get_clean();
    ob_start();

    $response = array(
        "draw" => $draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $data,
        // 'error' => $error,    // Do not include if there is no error.
        'message' => $messages,
        'badrecords' => $badRecords,
        'sql' => $sql
    );
// }

$json = json_encode($response);

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

if($json){
    echo $json;
} else {
    echo json_encode(array('code'=>json_last_error(),'msg'=>json_last_error_msg()));
}

PhpMemoryTrace::reportPeek(__FILE__,__LINE__);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);