<?php
use itdq\Trace;
use itdq\FormClass;
use itdq\Loader;
use rest\rfsRecord;
use rest\rfsTable;
use rest\allTables;
set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>RFS Definition Form</h2>
<?php

if (isset($_REQUEST['rfs']) && !empty($_REQUEST['rfs'])) {
    $rfsId = htmlspecialchars(trim($_REQUEST['rfs']));
} else {
    $rfsId = '';
}

$loader = new Loader();

if (!empty($rfsId)) {
    
    // RFS record exists check
    $recordExists = false;
    $exists = $loader->load('RFS_ID', AllTables::$RFS, "RFS_ID='$rfsId'");
    foreach ($exists as $value) {
        if (trim($value) == $rfsId) {
            $recordExists = true;
        }
    }
    
    // not existing RR record
    if ($recordExists == false) {
        // header('Location:index.html');
        echo 'RFS with provided id does not exist';
        exit;
    }
    
} else {
    // header('Location:index.html');
    echo 'RFS Id not provided';
    exit;
}

$mode = FormClass::$modeDISPLAY;
$rfsTable = new rfsTable(allTables::$RFS);
$rfsRecord = new rfsRecord();
$rfsRecord->set('RFS_ID', $_REQUEST['rfs']);
$rfsData = $rfsTable->getRecord($rfsRecord);
$rfsRecord->setFromArray($rfsData);

$rfsRecord->displayForm($mode);
?>
</div>

<?php
  include_once 'includes/modalSaveResultModal.html';
?>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);