<?php

use rest\resourceRequestTable;

set_time_limit(0);
// ob_start();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

$startDate = isset($_POST['START_DATE']) ? trim($_POST['START_DATE']) : null;
$endDate = isset($_POST['END_DATE']) ? trim($_POST['END_DATE']) : null;

resourceRequestTable::buildHTMLTable('resourceRequests', $startDate, $endDate);