<?php

use itdq\Loader;
use rest\allTables;
use rest\staticOrganisationTable;

set_time_limit(0);
ob_start();

$predicate=null;

$loader = new Loader();
$predicate = " STATUS='" . staticOrganisationTable::ENABLED . "' ";
$data = $loader->load('ORGANISATION',allTables::$STATIC_ORGANISATION,$predicate);

$messages = ob_get_clean();
$response = array("data"=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
echo json_encode($response);