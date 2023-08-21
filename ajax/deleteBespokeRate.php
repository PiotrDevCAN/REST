<?php
namespace ajax;

use rest\allTables;
use rest\staticBespokeRateTable;

set_time_limit(0);

ob_start();

$id = !empty($_POST['ID']) ? trim($_POST['ID']) : null;

if (!empty($id)) {
    $table = new staticBespokeRateTable(allTables::$BESPOKE_RATES);
    $table->deleteData(" BESPOKE_RATE_ID='" . htmlspecialchars($id) . "'",true );    
}

$messages = ob_get_clean();

$response = array('id' => $_POST['ID'], 'messages'=>$messages);

echo json_encode($response);