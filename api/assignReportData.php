<?php

use rest\allTables;
use rest\resourceRequestTable;
use rest\rfsRecord;

$startDate = !empty($_POST['startdate']) ? $_POST['startdate'] : null;
$endDate = !empty($_POST['enddate']) ? $_POST['enddate'] : null;

$startDateObj = new \DateTime($startDate);
$endDateObj = !empty($endDate) ? new \DateTime($endDate) : $startDateObj->add(new DateInterval('P3M')); // default 3 months from StartDate

$pipelineLiveArchive = !empty(($_POST['pipelineLiveArchive'])) ? $_POST['pipelineLiveArchive'] : rfsRecord::RFS_STATUS_LIVE;
$predicate = null;

$resourceRequestable = new resourceRequestTable(allTables::$RESOURCE_REQUEST_HOURS);

$dataAndSql = $resourceRequestable->returnAsArray($startDateObj->format('Y-m-d'), $endDateObj->format('Y-m-d'), $predicate, $pipelineLiveArchive, false);

$messages = ob_get_clean();

$success = empty($messages);

header('Content-Type: application/json');
echo json_encode(array('success'=>$success,'data'=>$dataAndSql['data'],'messages'=>$messages));

