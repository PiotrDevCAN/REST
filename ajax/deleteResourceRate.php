<?php
namespace ajax;

use rest\allTables;
use rest\staticResourceRateTable;

set_time_limit(0);

ob_start();

$id = !empty($_POST['ID']) ? trim($_POST['ID']) : null;

if (!empty($id)) {
    $table = new staticResourceRateTable(allTables::$RESOURCE_TYPE_RATES);
    $table->deleteData(" ID='" . htmlspecialchars($id) . "'",true );    
}

$messages = ob_get_clean();

$response = array('id' => $_POST['ID'], 'messages'=>$messages);

echo json_encode($response);