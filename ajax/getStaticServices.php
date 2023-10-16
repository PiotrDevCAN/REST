<?php

use rest\staticOrganisationTable;

set_time_limit(0);
ob_start();

$predicate=null;

$predicate = " STATUS='" . staticOrganisationTable::ENABLED . "' ";
$data = staticOrganisationTable::getAllOrganisationsAndServices($predicate);

$messages = ob_get_clean();
$response = array('data'=>$data,'messages'=>$messages,'count'=>count($data));

ob_clean();
echo json_encode($response);