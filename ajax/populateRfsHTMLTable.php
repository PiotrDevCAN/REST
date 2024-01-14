<?php

use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;
use itdq\Trace;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$rfsTable = new rfsTable(allTables::$RFS);

$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
$valueStream  = !empty($_POST['valuestream'])   ? trim($_POST['valuestream']) : null;
$businessUnit = !empty($_POST['businessunit']) ? trim($_POST['businessunit']) : null;
$requestor    = !empty($_POST['requestor'])    ? trim($_POST['requestor']) : null;

$pipelineLiveArchive = !empty($_POST['pipelineLiveArchive']) ? trim($_POST['pipelineLiveArchive']) : null;
if (array_key_exists($pipelineLiveArchive, rfsRecord::$rfsStatusMapping)) {
    $pipelineSelection = rfsRecord::$rfsStatusMapping[$pipelineLiveArchive];
} else {
    $pipelineSelection = null;
}
$bothStatuses = $pipelineLiveArchive=='both' ? true : false;
$withArchive = $pipelineLiveArchive=='archive' ? true : false;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) && $rfsId !=='All'  ? " AND RFS.RFS_ID='" . htmlspecialchars($rfsId) . "' " : null;
$predicate .= ! empty($valueStream) && $valueStream!=='All' ? " AND VS.VALUE_STREAM='" . htmlspecialchars($valueStream) . "' " : null;
$predicate .= ! empty($businessUnit) && $businessUnit!=='All' ? " AND BUSINESS_UNIT='" . htmlspecialchars($businessUnit) . "' " : null;
$predicate .= ! empty($requestor) && $requestor !=='All' ? " AND lower(REQUESTOR_EMAIL)='" . htmlspecialchars(strtolower($requestor)) . "' " : null;

if ($bothStatuses && !$withArchive) {
    $predicate .= " AND (";
    foreach (rfsRecord::$rfsStatus as $key => $rfsState) {
        if ($key == 0) {
            $predicate .= " ( RFS_STATUS='" . htmlspecialchars($rfsState) . "')";
        } else {
            $predicate .= " OR ( RFS_STATUS='" . htmlspecialchars($rfsState) . "')";
        }
    }
    $predicate .= ")";
} else {
    $predicate .= ! empty($pipelineLiveArchive) && !$withArchive ? " AND RFS_STATUS='" . htmlspecialchars($pipelineSelection) . "' " : null;
}

if (empty($rfsId) && empty($valueStream) && empty($requestor) && empty($businessUnit)) {
    $response = array(
        'messages' => 'No Drop Down Selection Made By User',
        "data" => array()
    );
} else {
    $dataAndSql = $rfsTable->returnAsArray($predicate,$withArchive);
    list('data' => $data, 'sql' => $sql) = $dataAndSql;
    $message = ob_get_clean();
    ob_start();
    $response = array("data"=>$data,'message'=>$message,'sql'=>$sql);
}

ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);