<?php
use itdq\Trace;
use rest\resourceRequestDiaryTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$diary = resourceRequestDiaryTable::getFormattedDiaryForResourceRequest($_POST['resourceReference']);
$messages = ob_get_clean();
ob_start();
$response = array('diary'=>$diary,'messages'=>$messages);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);