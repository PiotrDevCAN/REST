<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestDiaryTable;
ob_start();
set_time_limit(0);

$rrTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$rrTable->deleteData(" RESOURCE_REFERENCE='" . db2_escape_string($_POST['RESOURCE_REFERENCE']) . "'",true );

$diaryEntry = !empty($_POST['RESOURCE_NAME']) ? $_POST['RESOURCE_NAME'] . " deleted " : "Unallocated request " . $_POST['RESOURCE_REFERENCE'] . " deleted";
resourceRequestDiaryTable::insertEntry($diaryEntry, $_POST['RESOURCE_REFERENCE']);

$messages = ob_get_clean();
ob_start();

$response = array('rrId' => $_POST['RESOURCE_REFERENCE'], 'Messages'=>$messages);

ob_clean();
echo json_encode($response);