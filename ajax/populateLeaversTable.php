<?php

use rest\allTables;
use rest\inactivePersonTable;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
ob_start();

$inactivePerson = new inactivePersonTable(allTables::$INACTIVE_PERSON);
$data = $inactivePerson->returnForDataTables();

$messages = ob_get_clean();
ob_start();

$response = array("data"=>$data,'messages'=>$messages);

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

