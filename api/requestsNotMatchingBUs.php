<?php

use rest\allTables;
use rest\resourceRequestTable;
use rest\rfsRecord;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

$notArchivePredicate = ' AND ARCHIVE IS NULL ';
$predicate = '';

$tribeNamesString = implode("','", rfsRecord::$allTribeNames);
$tribeNamesString = "('" . $tribeNamesString . "') ";

$predicate.= " RFS.BUSINESS_UNIT IN " . $tribeNamesString;

$resourceRequestable = new resourceRequestTable(allTables::$RFS);
$dataAndSql = $resourceRequestable->returnNotMatchingBUs($predicate.$notArchivePredicate);
list('data' => $data, 'sql' => $sql) = $dataAndSql;

$messages = ob_get_clean();

$success = empty($messages);

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

$returnData['data'] = $data;

header('Content-Type: application/json');
echo json_encode(array(
    'success'=>$success,
    'data'=>$returnData,
    'count'=>count($data),
    'messages'=>$messages)
);