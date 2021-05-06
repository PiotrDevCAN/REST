<?php


use rest\StaticOrganisationTable;
use rest\allTables;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
ob_start();

$staticOrganisation = new StaticOrganisationTable(allTables::$STATIC_ORGANISATION);
$data = $staticOrganisation->returnForDataTables();

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

