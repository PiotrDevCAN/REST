<?php

use rest\allTables;
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

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);