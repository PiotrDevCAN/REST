<?php
use itdq\Trace;
use rest\allTables;
use rest\rfsPcrRecord;
use rest\rfsPcrTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

if(isset($data['rfsId']) && isset($data['rfsPcrId'])){
    $pcrTable = new rfsPcrTable(allTables::$RFS_PCR);
    $pcrRecord = new rfsPcrRecord();
    $pcrRecord->set('PCR_ID', $data['rfsPcrId']);
    $pcrRecord->set('RFS_ID', $data['rfsId']);
    $pcrData = $pcrTable->getRecord($pcrRecord);
} else {
    $pcrData = array();
}

Trace::pageLoadComplete($_SERVER['PHP_SELF']);

$response = $pcrData;

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