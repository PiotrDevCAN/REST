<?php


use itdq\Loader;
use rest\resourceRequestRecord;
use rest\allTables;
use rest\resourceRequestTable;

$loader = new Loader();
$predicate = " END_DATE < CURRENT DATE and STATUS != '" . resourceRequestRecord::STATUS_COMPLETED . "' ";

$allOpenTicketsPassedEndDate = $loader->load('RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS,$predicate);

if($allOpenTicketsPassedEndDate){
    foreach ($allOpenTicketsPassedEndDate as $resourceReference) {
        resourceRequestTable::setRequestStatus($resourceReference,resourceRequestRecord::STATUS_COMPLETED);
    }
}