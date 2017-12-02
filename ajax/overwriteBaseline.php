<?php
use rest\allTables;
use rest\inflightProjectsTable;

set_time_limit(0);

include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';

include_once '../rest/inflightProjectsTable.php';
include_once '../rest/inflightProjectsRecord.php';
include_once '../rest/allTables.php';

include_once '../rest/allTables.php';

session_start();

ob_start();

include_once '../connect.php';

$autocommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

$baselineTable = new inflightProjectsTable(allTables::$INFLIGHT_BASELINE);
$inflightTable = new inflightProjectsTable(allTables::$INFLIGHT_PROJECTS);

$baselineTable->deleteData(null,true);
db2_commit($_SESSION['conn']);

$sql = " insert into " . $_SESSION['Db2Schema'] . "." . allTables::$INFLIGHT_BASELINE;
$sql .="            ( select * from " . $_SESSION['Db2Schema'] . "." . allTables::$INFLIGHT_PROJECTS . ")";

$inflightTable->execute($sql);

db2_commit($_SESSION['conn']);
db2_autocommit($_SESSION['conn'],$autocommit);

echo "<br/>Current Inflights, written to Baseline";

$response = ob_get_clean();

ob_clean();
echo json_encode($response);