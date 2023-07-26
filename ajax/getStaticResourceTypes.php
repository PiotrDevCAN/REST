<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$predicate=null;

$loader = new Loader();
$predicate = "";
$data = $loader->loadIndexed('RESOURCE_TYPE','RESOURCE_TYPE_ID',allTables::$STATIC_RESOURCE_TYPE,$predicate);

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
echo json_encode($response);