<?php

use rest\allTables;
use rest\resourceRequestHoursTable;

$resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$data = $resourceRequestHoursTable->returnHrsPerWeek();

$messages = ob_get_clean();

$success = empty($messages);

header('Content-Type: application/json');
echo json_encode(array('success'=>$success,'data'=>$data,'messages'=>$messages));

