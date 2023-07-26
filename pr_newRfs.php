<?php
use itdq\Trace;
use itdq\FormClass;
use rest\rfsRecord;
use rest\rfsTable;
use rest\allTables;
set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>RFS Definition Form</h2>
<?php

if(isset($_REQUEST['rfs'])){
    $mode = FormClass::$modeEDIT;
    $rfsTable = new rfsTable(allTables::$RFS);
    $rfsRecord = new rfsRecord();
    $rfsRecord->set('RFS_ID', $_REQUEST['rfs']);
    $rfsData = $rfsTable->getRecord($rfsRecord);
    $rfsRecord->setFromArray($rfsData);

} else {
    $rfsRecord = new rfsRecord();
    $mode = FormClass::$modeDEFINE;
}
$rfsRecord->displayForm($mode);
?>
</div>

<?php
  include_once 'includes/modalSaveResultModal.html';
?>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);