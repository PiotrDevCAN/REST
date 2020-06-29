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
$cio = !empty($_POST['cio']) ? $_POST['cio'] : null;
$requestor = !empty($_POST['requestor']) ? $_POST['requestor'] : null;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) ? " AND RFS_ID='" . db2_escape_string($rfsId) . "' " : null;
$predicate .= ! empty($requestor) ? " AND lower(REQUESTOR_EMAIL)='" . db2_escape_string(strtolower($requestor)) . "' " : null;
$predicate .= ! empty($cio) ? " AND CIO='" . db2_escape_string($cio) . "' " : null;

if (empty($rfsId) && empty($cio) && empty($requestor)) {
    $response = array(
        'messages' => 'Nothing Selected',
        "data" => array()
    );
} else {
    $data = $rfsTable->returnAsArray($predicate);
    $message = ob_get_clean();
    ob_start();
    $response = array("data"=>$data,'message'=>$message);
}

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);