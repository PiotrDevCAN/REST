<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;

session_start();
set_time_limit(0);

$rfsTable = new rfsTable(allTables::$RFS);
$rfsTable->deleteData(" RFS_ID='" . db2_escape_string($_POST['RFS_ID']) . "'",true );

$messages = ob_get_clean();

$response = array('rfsId' => $_POST['RFS_ID'], 'Messages'=>$messages);

ob_clean();
echo json_encode($response);