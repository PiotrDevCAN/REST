<?php
use rest\allTables;
use rest\resourceRequestTable;

session_start();
set_time_limit(0);

$rrTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$rrTable->deleteData(" RESOURCE_REFERENCE='" . db2_escape_string($_POST['RESOURCE_REFERENCE']) . "'",true );

$messages = ob_get_clean();

$response = array('rrId' => $_POST['RESOURCE_REFERENCE'], 'Messages'=>$messages);

ob_clean();
echo json_encode($response);