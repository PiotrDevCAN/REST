<?php

use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use itdq\Loader;

set_time_limit(0);
ob_start();

$autoCommit = db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);


$resourceRecord = new resourceRequestRecord();
$resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
$resourceData = $resourceTable->getWithPredicate(" RESOURCE_REFERENCE='" . $_POST['resourceReference'] . "' ");
$resourceRecord->setFromArray($resourceData);

$resourceRecord->set('RESOURCE_REFERENCE', null); // So we get a new record when we insert.
$resourceRecord->set('RR_CREATED_TIMESTAMP', null); // so we know when the clone was created.
$currentResource =$resourceRecord->get('RESOURCE_NAME');

$resourceNamePrefix = $_POST['delta']=='true' ? resourceRequestTable::DELTA : resourceRequestTable::DUPLICATE;

echo $_POST['delta']=='true' ? "delta is true" : "delta is not true";

!empty($currentResource) ? $resourceRecord->set('RESOURCE_NAME', $resourceNamePrefix . $resourceRecord->get('RESOURCE_NAME')) : null;

$resourceRecord->set('CLONED_FROM',$_POST['resourceReference']);

$saveResponse  = $resourceTable->insert($resourceRecord);

$resourceReference = $resourceTable->lastId();

$startDate = $resourceRecord->get('START_DATE');
$endDate = $resourceRecord->get('END_DATE');
$hours   = $resourceRecord->get('TOTAL_HOURS');

if($saveResponse){
    try {
        $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference,$startDate,$endDate,$hours );
        $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";
        
        $loader = new Loader();
        $predicate = " RESOURCE_REFERENCE='" . db2_escape_string($_POST['resourceReference']). "'";
        $currentHoursPerWef = $loader->loadIndexed('HOURS','WEEK_ENDING_FRIDAY',allTables::$RESOURCE_REQUEST_HOURS,$predicate);
        foreach ($currentHoursPerWef as $currentWef => $currentHours) {
            $resourceHoursTable->setHoursForWef($resourceReference, $currentWef, $currentHours);
        }
        db2_commit($GLOBALS['conn']);
    } catch (Exception $e) {
        $hoursResponse = $e->getMessage();
        db2_rollback($GLOBALS['conn']);
    }

}

db2_autocommit($GLOBALS['conn'],$autoCommit);

$messages = ob_get_clean();
ob_start();

$response = array('resourceReference'=>$resourceReference, 'saveResponse' => $saveResponse, 'hoursResponse'=>$hoursResponse,
                  'Messages'=>$messages, 'POST'=>print_r($_POST,true),'resourceNamePrefix',$resourceNamePrefix);

ob_clean();
echo json_encode($response);