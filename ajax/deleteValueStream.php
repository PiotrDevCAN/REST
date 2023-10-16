<?php
namespace ajax;

use rest\allTables;
use rest\staticPSBandTable;

set_time_limit(0);

ob_start();

$valueStream = !empty($_POST['VALUE_STREAM']) ? trim($_POST['VALUE_STREAM']) : null;
$businessUnit = !empty($_POST['BUSINESS_UNIT']) ? trim($_POST['BUSINESS_UNIT']) : null;

if (!empty($valueStream) && !empty($businessUnit)) {
    $table = new staticPSBandTable(allTables::$STATIC_VALUE_STREAM);
    $table->deleteData(" VALUE_STREAM='" . htmlspecialchars($valueStream) . "' AND BUSINESS_UNIT='" . htmlspecialchars($businessUnit) . "'" , true);    
}

$messages = ob_get_clean();

$response = array('value_stream' => $_POST['VALUE_STREAM'], 'business_unit' => $_POST['BUSINESS_UNIT'], 'messages'=>$messages);

echo json_encode($response);