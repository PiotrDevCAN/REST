<?php
use itdq\Trace;
use rest\resourceRequestTable;

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();

error_log('opening ' . __FILE__);

$activeResources = resourceRequestTable::getVbacActiveResourcesForSelect2();

error_log('returned from resourceRequestTable::getVbacActiveResourcesForSelect2()');
error_log(count($activeResources) . " active resources");

$messages = ob_get_clean();
$success = empty($messages);
$response = array('data'=>$activeResources,'success'=> $success,'messages'=>$messages);
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);