<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;

set_time_limit(0);

include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';

include_once '../rest/rfsTable.php';
include_once '../rest/rfsRecord.php';
include_once '../rest/allTables.php';
session_start();

ob_start();
include_once '../connect.php';

$rfsTable = new rfsTable(allTables::$RFS);

$data = $rfsTable->returnAsArray();

$response = array("data"=>$data);


$message = ob_get_clean();

ob_clean();
echo json_encode($response);