<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;

set_time_limit(0);
ob_start();

$rfsTable = new rfsTable(allTables::$RFS);
$data = $rfsTable->returnAsArray();
$message = ob_get_clean();
$response = array("data"=>$data,'message'=>$message);
ob_clean();
echo json_encode($response);