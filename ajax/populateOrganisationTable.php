<?php


use rest\StaticOrganisationTable;
use rest\allTables;

set_time_limit(0);
ob_start();


$staticOrganisation = new StaticOrganisationTable(allTables::$STATIC_ORGANISATION);
$data = $staticOrganisation->returnForDataTables();

$messages = ob_get_clean();
ob_start();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);

