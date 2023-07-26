<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$predicate=null;

$loader = new Loader();
$data = $loader->load('ORGANISATION', allTables::$RESOURCE_REQUESTS, null, false);

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
echo json_encode($response);