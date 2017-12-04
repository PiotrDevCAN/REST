<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;

set_time_limit(0);
ob_start();
$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

$startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = !empty($_POST['endDate']) ? $_POST['endDate'] : null;

$data = $resourceRequestTable->returnAsArray($startDate,$endDate);

$response = array("data"=>$data);


ob_clean();
echo json_encode($response);