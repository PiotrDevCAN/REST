<?php

use itdq\AllItdqTables;
use itdq\DbRecord;
use rest\allTables;
use rest\archived\archivedRfsTable;
use rest\archived\archivedResourceRequestTable;
use rest\archived\archivedResourceRequestHoursTable;
use rest\archived\archivedResourceRequestDiaryTable;
use rest\archived\archivedDiaryTable;
use rest\rfsTable;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryTable;
use rest\diaryTable;
use rest\archived\archivedRfsRecord;
use rest\archived\archivedResourceRequestRecord;
use rest\archived\archivedResourceRequestHoursRecord;
use rest\archived\archivedResourceRequestDiaryRecord;
use rest\archived\archivedDiaryRecord;

// set_time_limit(0);
// ob_start();

$autoCommit = db2_autocommit($GLOBALS['conn']);
db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

$messages = '';
$rfsRecordsArchived = 0;
$rrRecordsArchived = 0;
$rrHoursRecordsArchived = 0;
$rrDiaryRecordsArchived = 0;
$diaryRecordsArchived = 0;

$success = false;

try {
    // prepare archieved tables for insert
    $archivedRfsTable = new archivedRfsTable(allTables::$ARCHIVED_RFS);
    $archivedResReqTable = new archivedResourceRequestTable(allTables::$ARCHIVED_RESOURCE_REQUESTS);
    $archivedResReqHoursTable = new archivedResourceRequestHoursTable(allTables::$ARCHIVED_RESOURCE_REQUEST_HOURS);
    $archivedResReqDiaryTable = new archivedResourceRequestDiaryTable(allTables::$ARCHIVED_RESOURCE_REQUEST_DIARY);
    $archivedDiaryTable = new archivedDiaryTable(allTables::$ARCHIVED_DIARY);

    $archivedRfsTable->clear(false);
    $archivedResReqTable->clear(false);
    $archivedResReqHoursTable->clear(false);
    $archivedResReqDiaryTable->clear(false);
    $archivedDiaryTable->clear(false);

    $rfsTable = new rfsTable(allTables::$RFS);
    $resReqTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resReqHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    $resReqDiaryTable = new resourceRequestDiaryTable(allTables::$RESOURCE_REQUEST_DIARY);
    $diaryTable = new diaryTable(AllItdqTables::$DIARY);

    $rfsRecord = new archivedRfsRecord();
    $resourceRequestRecord = new archivedResourceRequestRecord();
    $resourceRequestHoursRecord = new archivedResourceRequestHoursRecord();
    $resourceRequestDiaryRecord = new archivedResourceRequestDiaryRecord();
    $diaryRecord = new archivedDiaryRecord();

    $date = new \DateTime();
    $currentDate = $date->format('Y-m-d');

    // prepare all keys available in target table
    $insertRfsArrayKeys = $archivedRfsTable->getColumns();
    $insertResReqArrayKeys = $archivedResReqTable->getColumns();
    $insertResReqHoursArrayKeys = $archivedResReqHoursTable->getColumns();
    $insertResReqDiaryArrayKeys = $archivedResReqDiaryTable->getColumns();
    $insertDiaryArrayKeys = $archivedDiaryTable->getColumns();

    $archivedRfsRs = $rfsTable->getArchieved();
    while(($rowRFSData=db2_fetch_assoc($archivedRfsRs))==true){
        // get record data
        $rfsRecord->setFromArray($rowRFSData);
        // $rfsRecord->iterateVisible();

            // walk around due to differences in tables definitions between DEV and UT
            $mappedDbRecord = new DbRecord();
            foreach($insertRfsArrayKeys as $key => $value) {
                if (property_exists($rfsRecord, $key)) {
                    $valueFromRecord = $rfsRecord->getValue($key);
                    $mappedDbRecord->$key = $valueFromRecord;
                }
            }

        // move RFS record from live to archive table
        $insertResponse = $archivedRfsTable->insert($mappedDbRecord);
        // $insertResponse = $archivedRfsTable->insert($rfsRecord);
        if($insertResponse){
            $rfsTable->deleteRecord($rfsRecord);               
        } else {
            throw new \Exception("Can't insert RFS record to " . allTables::$ARCHIVED_RFS . " table");
        }

        // get RFS_ID
        $currentRfsId = $rfsRecord->get('RFS_ID');

        $archievedResourceRequestsRs = $resReqTable->getArchieved($currentRfsId);
        while(($rowRRData=db2_fetch_assoc($archievedResourceRequestsRs))==true){
            // get record data
            $resourceRequestRecord->setFromArray($rowRRData);

            $additionalFields = array();
            $additionalFields['SYS_START'] = $currentDate;
            $additionalFields['SYS_END'] = $currentDate;
            if (!empty($additionalFields)) {
                $resourceRequestRecord->setFromArray($additionalFields);
            }
            // $resourceRequestRecord->iterateVisible();

            // walk around due to differences in tables definitions between DEV and UT
            $mappedDbRecord = new DbRecord();
            foreach($insertResReqArrayKeys as $key => $value) {
                if (property_exists($resourceRequestRecord, $key)) {
                    $valueFromRecord = $resourceRequestRecord->getValue($key);
                    $mappedDbRecord->$key = $valueFromRecord;
                }
            }

            // move RR record from live to archive table
            $insertResponse = $archivedResReqTable->insert($mappedDbRecord);
            // $insertResponse = $archivedResReqTable->insert($mappedDbRecord);
            if($insertResponse){        
               $resReqTable->deleteRecord($resourceRequestRecord);           
            } else {
                throw new \Exception("Can't insert RR record to " . allTables::$ARCHIVED_RESOURCE_REQUESTS . " table");
            }

            // get RESOURCE_REFERENCE
            $currentResourceReference = $resourceRequestRecord->get('RESOURCE_REFERENCE');

            $archievedResourceRequestsHoursRs = $resReqHoursTable->getArchieved($currentResourceReference);
            while(($rowRRHData=db2_fetch_assoc($archievedResourceRequestsHoursRs))==true){
                // get record data
                $resourceRequestHoursRecord->setFromArray($rowRRHData);
                // $resourceRequestHoursRecord->iterateVisible();

                // walk around due to differences in tables definitions between DEV and UT
                $mappedDbRecord = new DbRecord();
                foreach($insertResReqArrayKeys as $key => $value) {
                    if (property_exists($resourceRequestHoursRecord, $key)) {
                        $valueFromRecord = $resourceRequestHoursRecord->getValue($key);
                        $mappedDbRecord->$key = $valueFromRecord;
                    }
                }

                // move RR hours record from live to archive table
                $insertResponse = $archivedResReqHoursTable->insert($mappedDbRecord);
                // $insertResponse = $archivedResReqHoursTable->insert($resourceRequestHoursRecord);
                if($insertResponse){            
                    $resReqHoursTable->deleteRecord($resourceRequestHoursRecord);       
                } else {
                    throw new \Exception("Can't insert RR Hours record to " . allTables::$ARCHIVED_RESOURCE_REQUEST_HOURS . " table");
                }

                $rrHoursRecordsArchived++;
            }

            // cleanup RESOURCE_REQUEST_DIARY table
            $archivedResReqDiaryRs = $resReqDiaryTable->getArchieved($currentResourceReference);
            while(($rowRRDData=db2_fetch_assoc($archivedResReqDiaryRs))==true){
                // get record data
                $resourceRequestDiaryRecord->setFromArray($rowRRDData);
                // $resourceRequestDiaryRecord->iterateVisible();

                // walk around due to differences in tables definitions between DEV and UT
                $mappedDbRecord = new DbRecord();
                foreach($insertResReqArrayKeys as $key => $value) {
                    if (property_exists($resourceRequestDiaryRecord, $key)) {
                        $valueFromRecord = $resourceRequestDiaryRecord->getValue($key);
                        $mappedDbRecord->$key = $valueFromRecord;
                    }
                }

                // move RR diary record from live to archive table
                $insertResponse = $archivedResReqDiaryTable->insert($mappedDbRecord);
                // $insertResponse = $archivedResReqDiaryTable->insert($resourceRequestDiaryRecord);
                if($insertResponse){
                    $archivedResReqDiaryTable->deleteRecord($resourceRequestDiaryRecord);        
                } else {
                    throw new \Exception("Can't insert RR Diary record to " . allTables::$ARCHIVED_RESOURCE_REQUEST_DIARY . " table");
                }
                
                // get DIARY_REFERENCE
                $currentDiaryReference = $resourceRequestDiaryRecord->get('DIARY_REFERENCE');

                // cleanup DIARY
                $archievedDiaryRs = $diaryTable->getArchieved($currentDiaryReference);
                while(($rowADData=db2_fetch_assoc($archievedDiaryRs))==true){
                    // get record data
                    $diaryRecord->setFromArray($rowADData);
                    // $diaryRecord->iterateVisible();

                    // walk around due to differences in tables definitions between DEV and UT
                    $mappedDbRecord = new DbRecord();
                    foreach($insertResReqArrayKeys as $key => $value) {
                        if (property_exists($diaryRecord, $key)) {
                            $valueFromRecord = $diaryRecord->getValue($key);
                            $mappedDbRecord->$key = $valueFromRecord;
                        }
                    }

                    // move DIARY record from live to archive table
                    $insertResponse = $archivedDiaryTable->insert($mappedDbRecord);
                    // $insertResponse = $archivedDiaryTable->insert($diaryRecord);
                    if($insertResponse){
                        $diaryTable->deleteRecord($diaryRecord);
                    } else {
                        throw new \Exception("Can't insert Diary record to " . allTables::$ARCHIVED_DIARY . " table");
                    }

                    $diaryRecordsArchived++;
                }

                $rrDiaryRecordsArchived++;
            }

            $rrRecordsArchived++;
        }

        $rfsRecordsArchived++;
    }

    // confirm that we can approve inserts or deletions
    $success = true;

} catch (Exception $e) {
    $messages = $e->getMessage();
}

if($success){
    db2_commit($GLOBALS['conn']);
} else {
    db2_rollback($GLOBALS['conn']);
}

db2_autocommit($GLOBALS['conn'],$autoCommit);

ob_start();

$response = array(
    'messages'=>$messages,
    'rfsRecordsArchived'=>$rfsRecordsArchived,
    'rrRecordsArchived'=>$rrRecordsArchived,
    'rrHoursRecordsArchived'=>$rrHoursRecordsArchived,
    'rrDiaryRecordsArchived'=>$rrDiaryRecordsArchived,
    'diaryRecordsArchived'=>$diaryRecordsArchived
);

ob_clean();
echo json_encode($response);