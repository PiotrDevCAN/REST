<?php
use itdq\Trace;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

if(isset($data['rfsId'])){
    $rfsTable = new rfsTable(allTables::$RFS);
    $rfsRecord = new rfsRecord();
    $rfsRecord->set('RFS_ID', $data['rfsId']);
    $rfsRecord->iterateVisible();
    $rfsData = $rfsTable->getRecord($rfsRecord);
} else {
    $rfsData = array();
}

Trace::pageLoadComplete($_SERVER['PHP_SELF']);

$response = $rfsData;

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

header('Content-Type: application/json');
echo json_encode($response);