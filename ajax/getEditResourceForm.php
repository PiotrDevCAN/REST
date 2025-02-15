<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use itdq\DbTable;
use rest\rfsTable;
use rest\rfsRecord;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;


Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();
echo "<h2>Resource Request Definition Form</h2>";

if(isset($_REQUEST['resourceReference'])){
    $mode = FormClass::$modeEDIT;
    $resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceRequestRecord = new resourceRequestRecord();
    $resourceRequestRecord->set('RESOURCE_REFERENCE', $_REQUEST['resourceReference']);
    $resourceRequestData = $resourceRequestTable->getRecord($resourceRequestRecord);
    $resourceRequestRecord->setFromArray($resourceRequestData);
} else {
    $resourceRequestRecord = new resourceRequestRecord();
    $mode = FormClass::$modeDEFINE;
}

$resourceRequestRecord->displayForm($mode);

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