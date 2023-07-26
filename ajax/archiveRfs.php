<?php
use rest\allTables;
use rest\rfsTable;

set_time_limit(0);
ob_start();

$rfsTable = new rfsTable(allTables::$RFS);
$rfsTable->archiveRfs($_POST['RFS_ID']);

$messages = ob_get_clean();
ob_start();

$response = array('rfsId' => $_POST['RFS_ID'], 'messages'=>$messages);

ob_clean();
echo json_encode($response);