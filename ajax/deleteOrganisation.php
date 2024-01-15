<?php
namespace ajax;

use rest\allTables;
use rest\staticOrganisationTable;

set_time_limit(0);

ob_start();

$organisation = !empty($_POST['ORGANISATION']) ? trim($_POST['ORGANISATION']) : null;

if (!empty($organisation)) {
    $table = new staticOrganisationTable(allTables::$STATIC_ORGANISATION);
    $table->deleteData(" ORGANISATION='" . htmlspecialchars($organisation) . "'", true);        
}

$messages = ob_get_clean();

$response = array('organisation' => $_POST['ORGANISATION'], 'messages'=>$messages);

echo json_encode($response);