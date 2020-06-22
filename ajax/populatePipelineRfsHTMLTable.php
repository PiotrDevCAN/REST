<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;
use itdq\Trace;
use rest\rfsPipelineView;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$rfsTable = new rfsPipelineView(allTables::$RFS_PIPELINE);
$data = $rfsTable->returnAsArray();
$message = ob_get_clean();
ob_start();
$response = array("data"=>$data,'message'=>$message);
ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);