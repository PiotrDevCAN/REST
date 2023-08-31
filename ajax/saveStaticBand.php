<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use rest\staticBandTable;
use rest\staticBandRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$bandTable  = new staticBandTable(allTables::$STATIC_BAND);
$bandTableRecord = new staticBandRecord();
$bandTableRecord->setFromArray(
   array(
      'BAND_ID'=>$_POST['BAND_ID'],
      'BAND'=>$_POST['BAND']
   )
);

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $db2result = $bandTable->insert($bandTableRecord);
} else {
   $db2result = $bandTable->update($bandTableRecord);
}

if(!$db2result){
    echo json_encode(sqlsrv_errors());
    echo json_encode(sqlsrv_errors());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'mode'=>$_POST['mode'],'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);