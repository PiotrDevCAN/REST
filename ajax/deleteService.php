<?php
namespace ajax;

use rest\allTables;
use rest\staticServiceTable;

set_time_limit(0);

ob_start();

$service = !empty($_POST['SERVICE']) ? trim($_POST['SERVICE']) : null;

if (!empty($service)) {
    $table = new staticServiceTable(allTables::$STATIC_SERVICE);
    $table->deleteData(" SERVICE='" . htmlspecialchars($service) . "'" , true);        
}

$messages = ob_get_clean();

$response = array('service' => $_POST['SERVICE'], 'messages'=>$messages);

echo json_encode($response);