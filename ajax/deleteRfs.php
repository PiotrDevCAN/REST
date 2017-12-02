<?php

use itdq\DbTable;
use rest\allTables;
use rest\rfsRecord;
use rest\rfsTable;

session_start();

set_time_limit(0);

include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';
include_once '../itdq/Date.php';

include_once '../rest/rfsTable.php';
include_once '../rest/rfsRecord.php';
include_once '../rest/allTables.php';

ob_start();

include_once '../connect.php';

$rfsTable = new rfsTable(allTables::$RFS);
$rfsTable->deleteData(" RFS_ID='" . db2_escape_string($_POST['RFS_ID']) . "'",true );

$messages = ob_get_clean();

$response = array('rfsId' => $_POST['RFS_ID'], 'Messages'=>$messages);

ob_clean();
echo json_encode($response);