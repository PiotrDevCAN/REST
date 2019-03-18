<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;
use itdq\Trace;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$rfsTable = new rfsTable(allTables::$RFS);
$data = $rfsTable->returnAsArray();
$message = ob_get_clean();
$response = array("data"=>$data,'message'=>$message);
ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);