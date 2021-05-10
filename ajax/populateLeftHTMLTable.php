<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;
use itdq\Trace;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$rfsTable = new rfsTable(allTables::$RFS);

$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
$valueStream  = !empty($_POST['valuestream'])   ? trim($_POST['valuestream']) : null;
$businessUnit = !empty($_POST['businessunit']) ? trim($_POST['businessunit']) : null;
$requestor    = !empty($_POST['requestor'])    ? trim($_POST['requestor']) : null;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) && $rfsId !=='All'  ? " AND RFS_ID='" . db2_escape_string($rfsId) . "' " : null;
$predicate .= ! empty($requestor) && $requestor !=='All' ? " AND lower(REQUESTOR_EMAIL)='" . db2_escape_string(strtolower($requestor)) . "' " : null;
$predicate .= ! empty($businessUnit) && $businessUnit!=='All' ? " AND BUSINESS_UNIT='" . db2_escape_string($businessUnit) . "' " : null;
$predicate .= ! empty($valueStream) && $valueStream!=='All' ? " AND VALUE_STREAM='" . db2_escape_string($valueStream) . "' " : null;

if (empty($rfsId) && empty($valueStream) && empty($requestor) && empty($businessUnit)) {
    $response = array(
        'messages' => 'No Drop Down Selection Made By User',
        "data" => array()
    );
} else {
    $data = $rfsTable->returnLeftReportAsArray($predicate);
    $message = ob_get_clean();
    ob_start();
    $response = array("data"=>$data['data'],'message'=>$message,'sql'=>$data['sql']);
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