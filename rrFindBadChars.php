<?php
use rest\resourceRequestTable;
use rest\allTables;

set_time_limit(0);
ob_start();
$resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);

$resourceRequestTable->findBadData();
