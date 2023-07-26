<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$loader = new Loader();
$data = $loader->load('RFS_ID', allTables::$RFS, " ARCHIVE is null ", false);

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
echo json_encode($response);