<?php

use rest\allTables;
use itdq\Trace;
use rest\RRBespokeRateTable;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$RRBespokeRateTable = new RRBespokeRateTable(allTables::$RESOURCE_REQUESTS);

$predicate = " 1=1 ";
$dataAndSql = $RRBespokeRateTable->returnAsArray($predicate);
list('data' => $data, 'sql' => $sql) = $dataAndSql;
$message = ob_get_clean();
ob_start();
$response = array("data"=>$data,'message'=>$message,'sql'=>$sql);

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