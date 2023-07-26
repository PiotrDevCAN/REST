<?php
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use rest\allTables;
use rest\rfsRecord;
use itdq\Loader;

$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

$loader = new Loader();

$predicate = " RFS LIKE '%PIPE%' ";
$allRequestsHours   = $loader->loadIndexed('TOTAL_HOURS','RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS, $predicate, 'asc');
$allRequestsEnd     = $loader->loadIndexed('END_DATE'   ,'RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS, $predicate, 'asc');
$allRFS             = $loader->load('RFS'        ,allTables::$RESOURCE_REQUESTS, $predicate, 'asc');
$allHrsType         = $loader->loadIndexed('HOURS_TYPE','RESOURCE_REFERENCE' ,allTables::$RESOURCE_REQUESTS, $predicate, 'asc');

foreach ($allRequestsHours as $resourceReference => $hours) {
    $nextPossibleStartDate  = DateTime::createFromFormat('Y-m-d', '2021-01-01');
    $nextPossibleStartDateString = $nextPossibleStartDate->format('Y-m-d'); 
    
    
    echo $resourceReference . ":" . $nextPossibleStartDateString . ":" .  $allRequestsEnd[$resourceReference] . ":" . $hours;
    
    $resourceHoursTable->createResourceRequestHours($resourceReference, $nextPossibleStartDateString, $allRequestsEnd[$resourceReference], $hours, true, $allHrsType[$resourceReference]);    
    resourceRequestTable::setStartDate($resourceReference, $nextPossibleStartDateString);  
}

foreach ($allRFS as $rfsid) {
    $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS;
    $sql .= "  SET RFS_STATUS     = '" . rfsRecord::RFS_STATUS_PIPELINE . "' ";
    $sql .= " WHERE RFS_ID='" . db2_escape_string($rfsid) ."' ";
    
    
    echo $sql;
    
    
    $rs = db2_exec($GLOBALS['conn'], $sql);
    
    if(!$rs){
        DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        return false;
    }
}


