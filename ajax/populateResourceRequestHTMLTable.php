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

$columns = isset($_POST['columns']) ? $_POST['columns'] : false;
$ordering = isset($_POST['order']) ? $_POST['order'] : false;
$search = isset($_POST['search']) ? $_POST['search'] : false;

// column filtering
$searchPredicate = "";
if ($columns && is_array($columns)) {
    if (count($columns) > 0) {
        foreach($columns as $key => $column) {
            $columnName = $column['data'];
            $searchable = $column['searchable'];
            $searchValue = $column['search']['value'];
            $searchRegex = $column['search']['regex']; // boolean

            if (!empty($column['search']['value'])) {
                $searchPredicate .= " AND " . $columnName . " LIKE '%" . $searchValue . "%'";
            }
        }
        echo $searchPredicate;
    }
}

// column ordering
$orderPredicate = "";
if ($ordering && is_array($ordering)) {
    if (count($ordering) > 0) {
        $orderPredicate .= " ORDER BY ";
        foreach($ordering as $key => $order) {
            $column = isset($order['column']) ? $order['column'] : false;
            $direction = isset($order['dir']) ? $order['dir'] : false;
            if (array_key_exists($column, $columns) 
                && $column !== false
                && $direction !== false
            ) {
                $columnName = $columns[$column]['data'];
                $orderable = $columns[$column]['orderable'];
                if ($orderable == 'true') {
                    $orderPredicate .= " " . $columnName . " " . $direction;
                }
            }
        }
        echo $orderPredicate;
    }
}

// global filtering
if ($search && is_array($search)) {
    if (count($search) > 0) {
        $searchValue = $search['value'];
        $searchRegex = $search['regex']; // boolean
    }
}

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
    $predicate .= $searchPredicate . $orderPredicate;

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
        "recordsTotal" => $data['total'],
        "recordsFiltered" => $data['total'],
        "data" => $data['data'],
        // 'error' => $data['error'],    // Do not include if there is no error.
        'message' => $message,
        'badrecords' => $badRecords,
        'sql' => $data['sql']
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