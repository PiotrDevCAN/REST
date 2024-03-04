<?php

use rest\allTables;
use itdq\Trace;
use rest\rfsClaimMonthlyTable;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$rfsTable = new rfsClaimMonthlyTable(allTables::$RFS);

$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
$valueStream  = !empty($_POST['valuestream'])   ? trim($_POST['valuestream']) : null;
$businessUnit = !empty($_POST['businessunit']) ? trim($_POST['businessunit']) : null;
$requestor    = !empty($_POST['requestor'])    ? trim($_POST['requestor']) : null;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) && $rfsId !=='All'  ? " AND RFS_ID='" . htmlspecialchars($rfsId) . "' " : null;
$predicate .= ! empty($valueStream) && $valueStream!=='All' ? " AND VS.VALUE_STREAM='" . htmlspecialchars($valueStream) . "' " : null;
$predicate .= ! empty($businessUnit) && $businessUnit!=='All' ? " AND BUSINESS_UNIT='" . htmlspecialchars($businessUnit) . "' " : null;
$predicate .= ! empty($requestor) && $requestor !=='All' ? " AND lower(REQUESTOR_EMAIL)='" . htmlspecialchars(strtolower($requestor)) . "' " : null;

if (empty($rfsId) && empty($valueStream) && empty($requestor) && empty($businessUnit)) {
    $response = array(
        'messages' => 'No Drop Down Selection Made By User',
        "data" => array()
    );
} else {
    $dataAndSql = $rfsTable->returnAsArray($predicate);
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