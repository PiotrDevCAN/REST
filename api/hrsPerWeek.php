<?php

use rest\allTables;
use rest\resourceRequestHoursTable;
use rest\rfsRecord;

set_time_limit(0);
ini_set('memory_limit','1024M');

// $GLOBALS['Db2Schema'] = 'REST_UT';
// $GLOBALS['Db2Schema'] = 'REST';

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

$pipelineArchive = !empty($_REQUEST['pipelineLiveArchive']) ? trim($_REQUEST['pipelineLiveArchive']) : null;
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

$onlyActiveStr = !empty($_GET['onlyactive']) ? $_GET['onlyactive'] : 'true';
$onlyActiveBool = $onlyActiveStr=='true';

// parameter for query - 12 months
$onlyActiveInTimeBool = false;

$onlyActiveIn12MonthsStr = !empty($_GET['onlyactive12mths']) ? $_GET['onlyactive12mths'] : 'false';
$onlyActiveIn12MonthsBool = $onlyActiveIn12MonthsStr=='true';

if ($onlyActiveIn12MonthsBool) {

    // override the onlyactive parameter
    $onlyActiveBool = false;
    
    // parameter for query - 6 or 12 months
    $onlyActiveInTimeBool = true;

    if ($onlyActiveIn12MonthsBool) {
        $months = 12;
    }
    // $today = new \DateTime();
    $archivedDate = new \DateTime();
    $xMonths = new \DateInterval('P'.$months.'M');
    $archivedDate = $archivedDate->sub($xMonths);
    $days = $archivedDate->format("d");
    $subxDays = new \DateInterval('P'.$days.'D');
    $archivedDate->sub($subxDays);
}

if ($onlyActiveBool) {
    $notArchivePredicate = "ARCHIVE IS NULL ";
} elseif ($onlyActiveInTimeBool) {
    $notArchivePredicate = "ARCHIVE > '" . $archivedDate->format('Y-m-d') . "' ";
} else {
    $notArchivePredicate = '';
}

$separator = '';
if ($predicate !== null) {
    $separator = " AND ";
}

$resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$data = $resourceRequestHoursTable->returnHrsPerWeek($predicate.$separator.$notArchivePredicate);

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
    'count'=>count($data),
    'messages'=>$messages)
);