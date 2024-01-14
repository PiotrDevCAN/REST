<?php

use rest\allTables;
use rest\rfsTable;

set_time_limit(0);
ini_set('memory_limit','3072M');

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

$rfsId = !empty($_GET['rfsid']) ? $_GET['rfsid'] : null;
$valueStream  = !empty($_GET['valuestream'])   ? trim($_GET['valuestream']) : null;
$businessUnit = !empty($_GET['businessunit']) ? trim($_GET['businessunit']) : null;
$requestor    = !empty($_GET['requestor'])    ? trim($_GET['requestor']) : null;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) && $rfsId !=='All'  ? " AND RFS_ID='" . htmlspecialchars($rfsId) . "' " : null;
$predicate .= ! empty($valueStream) && $valueStream!=='All' ? " AND VS.VALUE_STREAM='" . htmlspecialchars($valueStream) . "' " : null;
$predicate .= ! empty($businessUnit) && $businessUnit!=='All' ? " AND BUSINESS_UNIT='" . htmlspecialchars($businessUnit) . "' " : null;
$predicate .= ! empty($requestor) && $requestor !=='All' ? " AND lower(REQUESTOR_EMAIL)='" . htmlspecialchars(strtolower($requestor)) . "' " : null;

$rfsTable = new rfsTable(allTables::$RFS);
$data = $rfsTable->returnClaimReportAsJson($predicate);

$messages = ob_get_clean();

$success = empty($messages);

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

header('Content-Type: application/json');
echo json_encode(array(
    'success'=>$success,
    'data'=>$data,
    'count'=>count($data['data']),
    'messages'=>$messages)
);