<?php
use itdq\Trace;
use rest\allTables;
use itdq\FormClass;
use rest\staticResourceTypeTable;
use rest\staticResourceTypeRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$table  = new staticResourceTypeTable(allTables::$STATIC_RESOURCE_TYPE);
$record = new staticResourceTypeRecord();
$record->setFromArray(
   array(
      'RESOURCE_TYPE_ID'=>$_POST['RESOURCE_TYPE_ID'],
      'RESOURCE_TYPE'=>$_POST['RESOURCE_TYPE'],
      'HRS_PER_DAY'=>$_POST['HRS_PER_DAY']
   )
);

$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
if($mode==FormClass::$modeDEFINE){
   $db2result = $table->insert($record);
} else {
   $db2result = $table->update($record);
}

if(!$db2result){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'mode'=>$_POST['mode'],'parms'=>print_r($_POST,true));

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);