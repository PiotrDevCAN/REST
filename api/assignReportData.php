<?php

use itdq\DateClass;
use rest\allTables;
use rest\resourceRequestTable;
use rest\rfsRecord;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

$page = !empty($_REQUEST['page']) ? (int) $_REQUEST['page'] : false;
$perPage = !empty($_REQUEST['perPage']) ? (int) $_REQUEST['perPage'] : false;

$startDate = !empty($_REQUEST['startdate']) ? $_REQUEST['startdate'] : null;
$endDate = !empty($_REQUEST['enddate']) ? $_REQUEST['enddate'] : null;

$startDateObj = new \DateTime($startDate);
$endDateObj = !empty($endDate) ? new \DateTime($endDate) : $startDateObj = DateClass::addTime($startDateObj, 0, 3, 0); // default 3 months from StartDate

$pipelineLiveArchive = !empty($_REQUEST['pipelineLiveArchive']) ? $_REQUEST['pipelineLiveArchive'] : rfsRecord::RFS_STATUS_LIVE;
$predicate = null;

$resourceRequestable = new resourceRequestTable(allTables::$RESOURCE_REQUEST_HOURS);

$dataAndSql = $resourceRequestable->returnForAPI($startDateObj->format('Y-m-d'), $endDateObj->format('Y-m-d'), $predicate, $pipelineLiveArchive, false, $page, $perPage);
list(
    'data' => $data, 
    'sql' => $sql,
    'page' => $page,
    'per_page' => $perPage
) = $dataAndSql;

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

header('Content-Type: application/json');
echo json_encode(array(
        'success'=>$success,
        'data'=>$data,
        'sql'=>$sql,
        'count'=>count($data),
        'messages'=>$messages,
        'page'=>$page,
        'per_page'=>$perPage
    )
);