<?php

use itdq\Loader;
use rest\allTables;
use rest\staticOrganisationServiceTable;

set_time_limit(0);
ob_start();

$predicate = " SOS.STATUS='" . staticOrganisationServiceTable::ENABLED . "' ";
$data = staticOrganisationServiceTable::getAllOrganisationsAndServices($predicate);

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