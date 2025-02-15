<?php

use itdq\Loader;
use rest\allTables;
use rest\resourceRequestTable;

set_time_limit(0);
ob_start();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

$loader = new Loader();
$predicate = " RFS='" . htmlspecialchars($data['rfsId']) . "'";
$data = $loader->load('RESOURCE_REFERENCE', allTables::$RESOURCE_REQUESTS, $predicate);

$messages = ob_get_clean();
$response = array('data'=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

header('Content-Type: application/json');
echo json_encode($response);