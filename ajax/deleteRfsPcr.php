<?php
namespace ajax;
use rest\allTables;
use rest\rfsPcrTable;

set_time_limit(0);

ob_start();

$rfsPcrTable = new rfsPcrTable(allTables::$RFS_PCR);

$rfsPcrTable->deleteData(" PCR_ID='" . htmlspecialchars($_POST['ID']) . "'",true );

$messages = ob_get_clean();

$response = array('pcrId' => $_POST['ID'], 'messages'=>$messages);

echo json_encode($response);