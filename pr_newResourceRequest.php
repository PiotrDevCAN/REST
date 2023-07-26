<?php
use itdq\Trace;
use rest\resourceRequestRecord;
use itdq\FormClass;
use rest\resourceRequestTable;
use rest\allTables;
use itdq\DateClass;

set_time_limit(0);
Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<div class='container'>
<h2>Resource Request Form</h2>
<?php
if(isset($_REQUEST['resource'])){
    $mode = FormClass::$modeEDIT;
    $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceRecord = new resourceRequestRecord();
    $resourceRecord->set('RESOURCE_REFERENCE', $_REQUEST['resource']);
    $resourceData = $resourceTable->getRecord($resourceRecord);
    $resourceRecord->setFromArray($resourceData);
} else {
    $resourceRecord = new resourceRequestRecord();
    $mode = FormClass::$modeDEFINE;
}

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