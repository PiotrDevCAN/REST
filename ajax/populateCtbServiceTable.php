<?php


use rest\StaticCtbServiceTable;
use rest\allTables;

set_time_limit(0);
ob_start();


$staticCtbService = new StaticCtbServiceTable(allTables::$STATIC_CTB_SERVICE);
$data = $staticCtbService->returnForDataTables();

$messages = ob_get_clean();
ob_start();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);

