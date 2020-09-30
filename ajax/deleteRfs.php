<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;

set_time_limit(0);

ob_start();
$rfsTable = new rfsTable(allTables::$RFS);
$rfsTable->deleteData(" RFS_ID='" . db2_escape_string($_POST['RFS_ID']) . "'",true );
$messages = ob_get_clean();

$response = array('rfsId' => $_POST['RFS_ID'], 'Messages'=>$messages);

echo json_encode($response);