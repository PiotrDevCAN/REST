<?php

use itdq\Trace;
use rest\resourceRequestDiaryTable;

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$lastId = resourceRequestDiaryTable::insertEntry($_POST['newDiaryEntry'], $_POST['resourceReference']);

$success = (!$lastId==false);

$response = array('success'=>$success, 'diaryEntry'=>$_POST['newDiaryEntry'],'ref'=>$_POST['resourceReference']);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);