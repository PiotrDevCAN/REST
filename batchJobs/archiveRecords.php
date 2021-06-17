<?php

use itdq\AllItdqTables;
use itdq\DbRecord;
use itdq\Loader;
use rest\allTables;
use rest\diaryRecord;
use rest\diaryTable;
use rest\rfsRecord;
use rest\rfsTable;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;
use rest\resourceRequestDiaryRecord;
use rest\resourceRequestDiaryTable;

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

try {
    // prepare archieved tables for insert
    $archivedRfsTable = new rfsTable(allTables::$ARCHIVED_RFS);
    $archivedResReqTable = new resourceRequestTable(allTables::$ARCHIVED_RESOURCE_REQUESTS);
    $archivedResReqHoursTable = new resourceRequestHoursTable(allTables::$ARCHIVED_RESOURCE_REQUEST_HOURS);
    $archivedResReqDiaryTable = new resourceRequestDiaryTable(allTables::$ARCHIVED_RESOURCE_REQUEST_DIARY);
    $archivedDiaryTable = new diaryTable(allTables::$ARCHIVED_DIARY);

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

    $rfsRecord = new rfsRecord();
    $resourceRequestRecord = new resourceRequestRecord();
    $resourceRequestHoursRecord = new resourceRequestHoursRecord();
    $resourceRequestDiaryRecord = new resourceRequestDiaryRecord();
    $diaryRecord = new diaryRecord();

    $date = new \DateTime();
    $currentDate = $date->format('Y-m-d');

    $archivedRfsRs = $rfsTable->getArchieved();
    while(($rowRFSData=db2_fetch_assoc($archivedRfsRs))==true){
        // get record data
        $rfsRecord->setFromArray($rowRFSData);
        // $rfsRecord->iterateVisible();

        // move RFS record from live to archive table
        $archivedRfsTable->insert($rfsRecord);
        // $rfsTable->deleteRecord($rfsRecord);

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

            // move RR record from live to archive table
            $archivedResReqTable->insert($resourceRequestRecord);
            // $resReqTable->deleteRecord($resourceRequestRecord);

            // get RESOURCE_REFERENCE
            $currentResourceReference = $resourceRequestRecord->get('RESOURCE_REFERENCE');

            $archievedResourceRequestsHoursRs = $resReqHoursTable->getArchieved($currentResourceReference);
            while(($rowRRHData=db2_fetch_assoc($archievedResourceRequestsHoursRs))==true){
                // get record data
                $resourceRequestHoursRecord->setFromArray($rowRRHData);
                // $resourceRequestHoursRecord->iterateVisible();

                // move RR hours record from live to archive table
                $archivedResReqHoursTable->insert($resourceRequestHoursRecord);
                // $resReqHoursTable->deleteRecord($resourceRequestHoursRecord);

                $rrHoursRecordsArchived++;
            }

            // cleanup RESOURCE_REQUEST_DIARY table
            $archivedResReqDiaryRs = $resReqDiaryTable->getArchieved($currentResourceReference);
            while(($rowRRDData=db2_fetch_assoc($archivedResReqDiaryRs))==true){
                // get record data
                $resourceRequestDiaryRecord->setFromArray($rowRRDData);
                // $resourceRequestDiaryRecord->iterateVisible();

                // move RR diary record from live to archive table
                $archivedResReqDiaryTable->insert($resourceRequestDiaryRecord);
                // $archivedResReqDiaryTable->deleteRecord($resourceRequestDiaryRecord);

                // get DIARY_REFERENCE
                $currentDiaryReference = $resourceRequestDiaryRecord->get('DIARY_REFERENCE');

                // cleanup DIARY
                $archievedDiaryRs = $diaryTable->getArchieved($currentDiaryReference);
                while(($rowADData=db2_fetch_assoc($archievedDiaryRs))==true){
                    // get record data
                    $diaryRecord->setFromArray($rowADData);
                    // $diaryRecord->iterateVisible();

                    // move DIARY record from live to archive table
                    $archivedDiaryTable->insert($diaryRecord);
                    // $diaryTable->deleteRecord($diaryRecord);

                    $diaryRecordsArchived++;
                }

                $rrDiaryRecordsArchived++;
            }

            $rrRecordsArchived++;
        }

        $rfsRecordsArchived++;
    }

    db2_commit($GLOBALS['conn']);
    // db2_rollback($GLOBALS['conn']);

} catch (Exception $e) {
    $messages = $e->getMessage();
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