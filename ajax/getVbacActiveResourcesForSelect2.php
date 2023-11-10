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

$activeResources = new activeResourceTable(allTables::$ACTIVE_RESOURCE);
$data = $activeResources->getVbacActiveResourcesForSelect2();
list('tribeEmployees' => $activeResources, 'tribeEmployees' => $tribeEmployees, 'source' => $source) = $data;

error_log('returned from resourceRequestTable->getVbacActiveResourcesForSelect2()');
error_log(count($activeResources) . " active resources");

$messages = ob_get_clean();
$success = empty($messages);
$response = array(
    'data'=>$activeResources,
    'success'=> $success,
    'messages'=>$messages,
    'count'=>count($activeResources),
    'source'=>$source
);
// header('Content-Type: application/json');
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);