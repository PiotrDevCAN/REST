<?php

use rest\staticBandTable;
use rest\allTables;

set_time_limit(0);
ob_start();

$staticBand = new staticBandTable(allTables::$STATIC_BAND);
$data = $staticBand->returnForDataTables();

$messages = ob_get_clean();
ob_start();

$response = array('data'=>$data,'messages'=>$messages);

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

