<?php
use itdq\Trace;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

if(isset($data['resourceReference'])){
    $resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceRequestRecord = new resourceRequestRecord();
    $resourceRequestRecord->set('RESOURCE_REFERENCE', $data['resourceReference']);
    $resourceRequestRecord->iterateVisible();
    $resourceRequestData = $resourceRequestTable->getRecord($resourceRequestRecord);
} else {
    $resourceRequestData = array();
}

Trace::pageLoadComplete($_SERVER['PHP_SELF']);

$response = $resourceRequestData;

ob_clean();
echo json_encode($response);