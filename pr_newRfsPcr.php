<?php
use itdq\Trace;
use itdq\FormClass;
use rest\rfsRecord;
use rest\rfsTable;
use rest\allTables;
use rest\rfsPcrRecord;
use rest\rfsPcrTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>RFS PCR Definition Form</h2>
<?php

if(isset($_REQUEST['pcr'])){
    $mode = FormClass::$modeEDIT;
    $rfsPcrTable = new rfsPcrTable(allTables::$RFS_PCR);
    $rfsPcrRecord = new rfsPcrRecord();
    $rfsPcrRecord->set('PCR_ID', $_REQUEST['pcr']);
    $rfsData = $rfsPcrTable->getRecord($rfsPcrRecord);
    $rfsPcrRecord->setFromArray($rfsData);

} else {
    $rfsPcrRecord = new rfsPcrRecord();
    $mode = FormClass::$modeDEFINE;
}
$rfsPcrRecord->displayForm($mode);
?>
</div>

<?php
  include_once 'includes/modalSaveResultModal.html';
?>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);