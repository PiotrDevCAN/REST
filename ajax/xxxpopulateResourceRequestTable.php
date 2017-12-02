<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;

set_time_limit(0);

include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';

include_once '../rest/resourceRequestTable.php';
include_once '../rest/resourceRequestRecord.php';
include_once '../rest/allTables.php';
session_start();

ob_start();
include_once '../connect.php';

$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

$startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = !empty($_POST['endDate']) ? $_POST['endDate'] : null;

$data = $resourceRequestTable->returnAsArray($startDate,$endDate);

$response = array("data"=>$data);


ob_clean();
echo json_encode($response);