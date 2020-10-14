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

$pipelineLiveArchive    = !empty($_POST['pipelineLiveArchive'])    ? trim($_POST['pipelineLiveArchive']) : null;
$pipelineSelection = rfsRecord::$rfsStatusMapping[$pipelineLiveArchive];
$withArchive = $pipelineLiveArchive=='archive' ? true : false;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) && $rfsId !=='All'  ? " AND RFS.RFS_ID='" . db2_escape_string($rfsId) . "' " : null;
$predicate .= ! empty($requestor) && $requestor !=='All' ? " AND lower(REQUESTOR_EMAIL)='" . db2_escape_string(strtolower($requestor)) . "' " : null;
$predicate .= ! empty($businessUnit) && $businessUnit!=='All' ? " AND BUSINESS_UNIT='" . db2_escape_string($businessUnit) . "' " : null;
$predicate .= ! empty($valueStream) && $valueStream!=='All' ? " AND VALUE_STREAM='" . db2_escape_string($valueStream) . "' " : null;
$predicate .= ! empty($pipelineLiveArchive) && !$withArchive ? " AND RFS_STATUS='" . db2_escape_string($pipelineSelection) . "' " : null;

if (empty($rfsId) && empty($valueStream) && empty($requestor) && empty($businessUnit)) {
    $response = array(
        'messages' => 'No Drop Down Selection Made By User',
        "data" => array()
    );
} else {
    $data = $rfsTable->returnAsArray($predicate,$withArchive);
    $message = ob_get_clean();
    ob_start();
    $response = array("data"=>$data['data'],'message'=>$message,'sql'=>$data['sql']);
}

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);