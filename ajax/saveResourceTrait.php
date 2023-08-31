<?php
use itdq\Trace;
use itdq\Loader;
use rest\allTables;
use itdq\FormClass;
use rest\staticResourceTraitsRecord;
use rest\staticResourceTraitsTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$table  = new staticResourceTraitsTable(allTables::$RESOURCE_TRAITS);
$record = new staticResourceTraitsRecord();
$record->setFromArray(
   array(
      'ID'=>$_POST['ID'],
      'RESOURCE_NAME'=>$_POST['RESOURCE_NAME'],
      'RESOURCE_TYPE_ID'=>$_POST['RESOURCE_TYPE_ID'],
      'PS_BAND_ID'=>$_POST['PS_BAND_ID'],
      'PS_BAND_OVERRIDE'=>$_POST['PS_BAND_OVERRIDE'],
   )
);

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $loader = new Loader();
   $responseName = !empty($_POST['RESOURCE_NAME']) ? trim($_POST['RESOURCE_NAME']) : null;

   $resourceTypeIds = $loader->load('ID', allTables::$RESOURCE_TRAITS, "RESOURCE_NAME='$responseName'");
   $resourceTypeId = '';
   foreach ($resourceTypeIds as $typeId) {
      $resourceTypeId = trim($typeId);
   }

   if (empty($resourceTypeId)) {
      $db2result = $table->insert($record);
   } else {
      echo 'The individual has Resource Traits already assigned.';
      $db2result = true;
   }
} else {
   $db2result = $table->update($record);
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