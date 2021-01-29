<?php

use rest\allTables;
use rest\resourceRequestTable;
use rest\rfsRecord;

$startDate = !empty($_REQUEST['startdate']) ? $_REQUEST['startdate'] : null;
$endDate = !empty($_REQUEST['enddate']) ? $_REQUEST['enddate'] : null;

$startDateObj = new \DateTime($startDate);
$endDateObj = !empty($endDate) ? new \DateTime($endDate) : $startDateObj->add(new DateInterval('P3M')); // default 3 months from StartDate


error_log(__FILE__ . ":" . __LINE__ . ":" . $_REQUEST['pipelineLiveArchive']);

$pipelineLiveArchive = !empty($_REQUEST['pipelineLiveArchive']) ? $_REQUEST['pipelineLiveArchive'] : rfsRecord::RFS_STATUS_LIVE;
$predicate = null;

$resourceRequestable = new resourceRequestTable(allTables::$RESOURCE_REQUEST_HOURS);

$dataAndSql = $resourceRequestable->returnAsArray($startDateObj->format('Y-m-d'), $endDateObj->format('Y-m-d'), $predicate, $pipelineLiveArchive, false);

$messages = ob_get_clean();

$success = empty($messages);

header('Content-Type: application/json');
echo json_encode(array('success'=>$success,'data'=>$dataAndSql['data'],'messages'=>$messages));

