<?php
use itdq\Trace;
use itdq\FormClass;
use itdq\DateClass;
use itdq\Loader;
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;

set_time_limit(0);
Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<div class='container'>
<h2>Resource Assignment</h2>
<?php

if (isset($_REQUEST['resource']) && !empty($_REQUEST['resource'])) {
    $rrId = db2_escape_string(trim($_REQUEST['resource']));
} else {
    $rrId = '';
}

$loader = new Loader();

if (!empty($rrId)) {
    
    // Resource Request record exists check
    $recordExists = false;
    $exists = $loader->load('RESOURCE_REFERENCE', AllTables::$RESOURCE_REQUESTS, "RESOURCE_REFERENCE='$rrId'");
    foreach ($exists as $value) {
        if (trim($value) == $rrId) {
            $recordExists = true;
        }
    }
    
    // not existing RR record
    if ($recordExists == false) {
        // header('Location:index.html');
        echo 'Resource with provided id does not exist';
        exit;
    }
    
} else {
    // header('Location:index.html');
    echo 'Resource Reference Id not provided';
    exit;
}

$mode = FormClass::$modeDISPLAY;
$resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
$resourceRecord = new resourceRequestRecord();
$resourceRecord->set('RESOURCE_REFERENCE', $_REQUEST['resource']);
$resourceData = $resourceTable->getRecord($resourceRecord);
$resourceRecord->setFromArray($resourceData);

$resourceRecord->displayForm($mode);
?>
</div>

<?php
  include_once 'includes/modalSaveResultModal.html';
?>

<style type="text/css">
<?php
$date = new DateTime();
$currentYear = $date->format('Y');

for($year=$currentYear-1;$year<=$currentYear+1;$year++){
    for($month=1;$month<=12;$month++){
        $date = '01-' . substr('00' . $month,2) . "-" . $year;
        $claimCutoff = DateClass::claimMonth($date);
        ?>[data-pika-year="<?=$year;?>"][data-pika-month="<?=$month-1;?>"][data-pika-day="<?=$claimCutoff->format('d');?>"] {background-color: white; color:red; outline:solid; outline-color:grey;outline-width:thin; content='claim'}<?php
    }
}
?>
</style>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);