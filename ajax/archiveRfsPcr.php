<?php
use rest\allTables;
use rest\rfsPcrTable;

set_time_limit(0);
ob_start();

$rfsPcrTable = new rfsPcrTable(allTables::$RFS_PCR);
$rfsPcrTable->archivePcr($_POST['PCR_ID']);

$messages = ob_get_clean();
ob_start();

$response = array('pcrId' => $_POST['PCR_ID'], 'messages'=>$messages);

ob_clean();
echo json_encode($response);