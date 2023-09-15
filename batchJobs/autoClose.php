<?php


use itdq\Loader;
use rest\resourceRequestRecord;
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestDiaryTable;

$loader = new Loader();
$predicate = " END_DATE < CURRENT_TIMESTAMP and STATUS != '" . resourceRequestRecord::STATUS_COMPLETED . "' ";
$date = new DateTime();

$allOpenTicketsPassedEndDate = $loader->load('RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS,$predicate);

if($allOpenTicketsPassedEndDate){
    foreach ($allOpenTicketsPassedEndDate as $resourceReference) {
        resourceRequestDiaryTable::insertEntry("Auto-Closed " . $date->format('d-M-Y'), $resourceReference);
        resourceRequestTable::setRequestStatus($resourceReference,resourceRequestRecord::STATUS_COMPLETED);
        
    }
}