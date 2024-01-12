<?php
namespace ajax;

use rest\allTables;
use rest\staticOrganisationServiceTable;

set_time_limit(0);

ob_start();

$organisation = !empty($_POST['ORGANISATION']) ? trim($_POST['ORGANISATION']) : null;
$service = !empty($_POST['SERVICE']) ? trim($_POST['SERVICE']) : null;

if (!empty($organisation) && !empty($service)) {
    $table = new staticOrganisationServiceTable(allTables::$STATIC_ORGANISATION);
    $table->deleteData(" ORGANISATION='" . htmlspecialchars($organisation) . "' AND SERVICE='" . htmlspecialchars($service) . "'" , true);        
}

$messages = ob_get_clean();

$response = array('organisation' => $_POST['ORGANISATION'], 'service' => $_POST['SERVICE'], 'messages'=>$messages);

echo json_encode($response);