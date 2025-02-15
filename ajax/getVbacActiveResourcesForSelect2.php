<?php
use itdq\Trace;
use rest\activeResourceTable;
use rest\allTables;

Trace::pageOpening($_SERVER['PHP_SELF']);
// ob_start();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

error_log('opening ' . __FILE__);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

$fetchAll = null;
if(isset($data['fetchAll'])){
    $fetchAll = true;
}

$activeResources = new activeResourceTable(allTables::$ACTIVE_RESOURCE);
$data = $activeResources->getVbacActiveResourcesForSelect2($fetchAll);
list('tribeEmployees' => $tribeEmployees, 'source' => $source, 'sql' => $sql) = $data;

error_log('returned from resourceRequestTable->getVbacActiveResourcesForSelect2()');
error_log(count($tribeEmployees) . " active resources");

$messages = ob_get_clean();
$success = empty($messages);
$response = array(
    'data'=>$tribeEmployees,
    'success'=> $success,
    'messages'=>$messages,
    'count'=>count($tribeEmployees),
    'source'=>$source,
    'sql'=>$sql
);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);

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