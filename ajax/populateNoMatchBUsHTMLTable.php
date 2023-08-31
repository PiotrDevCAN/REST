<?php

use rest\allTables;
use itdq\Trace;
use rest\resourceRequestTable;
use rest\rfsRecord;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$notArchivePredicate = ' AND ARCHIVE IS NULL ';
$predicate = '';

$tribeNamesString = implode("','", rfsRecord::$allTribeNames);
$tribeNamesString = "('" . $tribeNamesString . "') ";

$predicate.= " RFS.BUSINESS_UNIT IN " . $tribeNamesString .
" AND AR.TRIBE_NAME_MAPPED IN " . $tribeNamesString;

$resourceRequestable = new resourceRequestTable(allTables::$RFS);
$dataAndSql = $resourceRequestable->returnNotMatchingBUsForDataTables($predicate.$notArchivePredicate);
list('data' => $records, 'sql' => $sql) = $dataAndSql;

$message = ob_get_clean();
ob_start();

$response = array(
    'data' => $records,
    'sql' => $sql,
    'message' => $message
);

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