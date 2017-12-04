<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;

set_time_limit(0);
ob_start();

$rfsTable = new rfsTable(allTables::$RFS);
$data = $rfsTable->returnAsArray();
$response = array("data"=>$data);
$message = ob_get_clean();
ob_clean();
echo json_encode($response);