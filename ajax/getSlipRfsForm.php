<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use itdq\DbTable;
use rest\rfsTable;
use rest\rfsRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();
echo "<h2>Amend Dates for RFS/Resource Requests</h2>";
echo "<hr/>";

if(isset($_REQUEST['rfsId'])){
    $mode = FormClass::$modeEDIT;
    $rfsTable = new rfsTable(allTables::$RFS);
    $rfsRecord = new rfsRecord();
    $rfsRecord->set('RFS_ID', $_REQUEST['rfsId']);
    $rfsData = $rfsTable->getRecord($rfsRecord);
    $rfsRecord->setFromArray($rfsData);

} else {
    throw new Exception('No RFS Id Supplied to getSlipRfsForm');
}

$rfsRecord->displaySlipRfs();

$form = ob_get_clean();
ob_start();

$response = array('form'=>$form);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

header('Content-Type: application/json');
echo json_encode($response);