<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use rest\staticResourceRateRecord;
use rest\staticResourceRateTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$table  = new staticResourceRateTable(allTables::$RESOURCE_TYPE_RATES);
$record = new staticResourceRateRecord();
$record->setFromArray(
   array(
      'ID'=>$_POST['ID'],
      'RESOURCE_TYPE_ID'=>$_POST['RESOURCE_TYPE_ID'],
      'PS_BAND_ID'=>$_POST['PS_BAND_ID'],
      // 'BAND_ID'=>$_POST['BAND_ID'],
      'BAND_ID'=>null,  // managed externally by Jay
      'TIME_PERIOD_START'=>$_POST['TIME_PERIOD_START'],
      'TIME_PERIOD_END'=>$_POST['TIME_PERIOD_END'],
      'DAY_RATE'=>$_POST['DAY_RATE'],
      'HOURLY_RATE'=>$_POST['HOURLY_RATE'],
   )
);

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $db2result = $table->insert($record);
} else {
   $db2result = $table->update($record);
}

if(!$db2result){
    echo print_r(sqlsrv_errors());
    echo print_r(sqlsrv_errors());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'mode'=>$_POST['mode'],'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);