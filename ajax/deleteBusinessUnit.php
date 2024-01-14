<?php
namespace ajax;

use rest\allTables;
use rest\staticBusinessUnitTable;

set_time_limit(0);

ob_start();

$valueStream = !empty($_POST['BUSINESS_UNIT']) ? trim($_POST['BUSINESS_UNIT']) : null;

if (!empty($valueStream)) {
    $table = new staticBusinessUnitTable(allTables::$STATIC_BUSINESS_UNIT);
    $table->deleteData(" BUSINESS_UNIT='" . htmlspecialchars($valueStream) . "'" , true);
}

$messages = ob_get_clean();

$response = array('business_unit' => $_POST['BUSINESS_UNIT'], 'messages'=>$messages);

echo json_encode($response);