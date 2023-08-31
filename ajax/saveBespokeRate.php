<?php
use itdq\Trace;
use itdq\Loader;
use rest\allTables;
use itdq\FormClass;
use rest\staticBespokeRateTable;
use rest\staticBespokeRateRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$table  = new staticBespokeRateTable(allTables::$BESPOKE_RATES);
$record = new staticBespokeRateRecord();
$record->setFromArray(
   array(
      'BESPOKE_RATE_ID'=>$_POST['BESPOKE_RATE_ID'],
      'RFS_ID'=>$_POST['RFS_ID'],
      'RESOURCE_REFERENCE'=>$_POST['RESOURCE_REFERENCE'],
      'RESOURCE_TYPE_ID'=>$_POST['RESOURCE_TYPE_ID'],
      'PS_BAND_ID'=>$_POST['PS_BAND_ID']
   )
);

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $loader = new Loader();

   $rfsId = !empty($_POST['RFS_ID']) ? trim($_POST['RFS_ID']) : null;
   $resourceReference = !empty($_POST['RESOURCE_REFERENCE']) ? trim($_POST['RESOURCE_REFERENCE']) : null;
   
   $bespokeRateIds = $loader->load('BESPOKE_RATE_ID', allTables::$BESPOKE_RATES, "RFS_ID='$rfsId' AND RESOURCE_REFERENCE='$resourceReference'");
   $bespokeRateId = '';
   foreach ($bespokeRateIds as $id) {
      $bespokeRateId = trim($id);
   }

   if (empty($bespokeRateId)) {
      $db2result = $table->insert($record);
   } else {
      echo 'The request has Bespoke Rate already assigned.';
      $db2result = true;
   }
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