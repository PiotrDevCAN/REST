<?php
use itdq\Trace;
use rest\resourceRequestTable;

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();

$activeResources = resourceRequestTable::getVbacActiveResourcesForSelect2();

$messages = ob_get_clean();
ob_start();

$success = empty($messages);

$response = array('data'=>$activeResources, 'success' => $success, 'messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);