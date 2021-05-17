<?php

use rest\resourceRequestTable;

set_time_limit(0);
ob_start();

$startDate = !empty($_POST['START_DATE']) ? $_POST['START_DATE'] : null;
$endDate = !empty($_POST['END_DATE']) ? $_POST['END_DATE'] : null;

resourceRequestTable::buildHTMLTable($startDate, $endDate);