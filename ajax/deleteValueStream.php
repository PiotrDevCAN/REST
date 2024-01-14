<?php
namespace ajax;

use rest\allTables;
use rest\staticValueStreamTable;

set_time_limit(0);

ob_start();

$valueStream = !empty($_POST['VALUE_STREAM']) ? trim($_POST['VALUE_STREAM']) : null;

if (!empty($valueStream)) {
    $table = new staticValueStreamTable(allTables::$STATIC_VALUE_STREAM);
    $table->deleteData(" VALUE_STREAM='" . htmlspecialchars($valueStream) . "'" , true);
}

$messages = ob_get_clean();

$response = array('value_stream' => $_POST['VALUE_STREAM'], 'messages'=>$messages);

echo json_encode($response);