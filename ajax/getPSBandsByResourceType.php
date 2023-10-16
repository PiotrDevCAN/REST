<?php

use rest\allTables;
use rest\staticResourceRateTable;

set_time_limit(0);
ob_start();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

$staticResourceType = new staticResourceRateTable(allTables::$RESOURCE_TYPE_RATES);
$data = $staticResourceType->returnPSBandsForResourceTypeId($data['resourceTypeId']);

$messages = ob_get_clean();
$response = array('data'=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
echo json_encode($response);