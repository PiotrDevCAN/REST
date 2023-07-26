<?php
namespace ajax;

use rest\allTables;
use rest\staticBandTable;

set_time_limit(0);

ob_start();

$id = !empty($_POST['ID']) ? trim($_POST['ID']) : null;

if (!empty($id)) {
    $table = new staticBandTable(allTables::$STATIC_BAND);
    $table->deleteData(" BAND_ID='" . db2_escape_string($id) . "'",true );    
}

$messages = ob_get_clean();

$response = array('id' => $_POST['ID'], 'messages'=>$messages);

echo json_encode($response);