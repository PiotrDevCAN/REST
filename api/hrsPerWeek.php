<?php

use rest\allTables;
use rest\resourceRequestHoursTable;
use rest\rfsRecord;

$pipelineArchive = !empty($_REQUEST['pipelineLiveArchive']) ? trim($_REQUEST['pipelineLiveArchive']) : null;

$notArchivePredicate = 'ARCHIVE IS NULL ';
switch (strtolower($pipelineArchive)) {
    case 'live':
        $predicate = "RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' ";
    break;
    case 'pipeline':
        $predicate = "RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' ";
        break;    
    default:
        $predicate = null;
    break;
}

$resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$data = $resourceRequestHoursTable->returnHrsPerWeek($predicate.$notArchivePredicate);

$messages = ob_get_clean();

$success = empty($messages);

header('Content-Type: application/json');
echo json_encode(array('success'=>$success,'data'=>$data,'count'=>count($data),'messages'=>$messages));

