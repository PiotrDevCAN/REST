<?php
use rest\allTables;
use rest\inflightProjectsTable;

set_time_limit(0);
ob_start();

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