<?php

use rest\allTables;
use rest\resourceRequestTable;
use itdq\Trace;
use rest\rfsTable;
use itdq\PhpMemoryTrace;
use rest\rfsRecord;

set_time_limit(0);
ini_set('memory_limit','3072M');

ob_start();
PhpMemoryTrace::reportPeek(__FILE__,__LINE__);

Trace::pageOpening($_SERVER['PHP_SELF']);
$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

$pipelineLiveArchive = !empty($_POST['pipelineLiveArchive']) ? $_POST['pipelineLiveArchive'] : 'live' ;
$pipelineLive = $pipelineLiveArchive=='live' ? rfsRecord::RFS_STATUS_LIVE : rfsRecord::RFS_STATUS_PIPELINE;
$pipelineLive = $pipelineLiveArchive=='archive' ? null : $pipelineLive;
$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
$organisation = !empty($_POST['organisation']) ? $_POST['organisation'] : null;
$businessUnit = !empty($_POST['businessunit']) ? $_POST['businessunit'] : null;

$withButtons = true;

// ignore Redis cache and refresh table data from DB
$disableCacheParam = !empty($_POST['disableCache']) ? $_POST['disableCache'] : null;
$disableCache = $disableCacheParam === 'true' ? true: false;

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
    $predicate .= ! empty($rfsId) ? " AND RFS='" . htmlspecialchars($rfsId) . "' " : null;
    $predicate .= ! empty($organisation) ? " AND ORG.ORGANISATION='" . htmlspecialchars($organisation) . "' " : null;
    $predicate .= ! empty($businessUnit) ? " AND BUSINESS_UNIT='" . htmlspecialchars($businessUnit) . "' " : null;

    error_log(__FILE__ . ":" . __LINE__ . ":" . $predicate);

    $dataAndSql = $resourceRequestTable->returnAsArray($predicate, $pipelineLiveArchive, $withButtons, $disableCache);
    list('data' => $data, 'sql' => $sql, 'source' => $source, 'badRecords' => $badRecords) = $dataAndSql;

    PhpMemoryTrace::reportPeek(__FILE__, __LINE__);
    PhpMemoryTrace::reportPeek(__FILE__, __LINE__);

    $messages = ob_get_clean();
    ob_start();

    $response = array(
        'messages' => $messages,
        'badrecords' => $badRecords,
        'peekusage' => $_SESSION['peekUsage']/1024,
        'source' => $source,
        "data" => $data,
        "sql" => $sql
    );
}

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