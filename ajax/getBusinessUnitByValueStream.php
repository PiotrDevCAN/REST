<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$predicate=null;

$loader = new Loader();

$predicate = !empty($_POST['valueStream']) ? "VALUE_STREAM = '" . db2_escape_string($_POST['valueStream']) . "'" : false ;
$data = $loader->load('BUSINESS_UNIT', allTables::$STATIC_VALUE_STREAM, $predicate, FALSE);

if (count($data) > 0) {
    foreach($data as $key => $value) {
        $businessUnit = $key;
    }
} else {
    $businessUnit = '';
}

$messages = ob_get_clean();
$response = array("businessUnit"=>$businessUnit,'messages'=>$messages);

ob_clean();
echo json_encode($response);