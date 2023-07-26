<?php
use itdq\Trace;
use itdq\DateClass;
use rest\rfsPcrTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);

?>

<div class='container'>

<h3>RFS PCR Report</h3>

<form id='reportDates' class='form-horizontal'>
	<div class='form-group text-right' >
    <label for='selectRfs' class='col-md-1 control-label text-right'>RFS</label>
    <div class='col-md-2 text-left'>
      <select class='form-control select' id='selectRfs'
        name='selectRfs'
        data-placeholder="Select RFS" data-allow-clear="true"
        >
        <option value=''>Select RFS</option>
        <option value='All'>All</option>
      </select>
    </div>
  </div>
</form>
</div>

<hr/>

<div class='container-fluid'>
<div id='rfsPcrTableDiv'>
<?php
  rfsPcrTable::buildHTMLTable();
?>
</div>
</div>

<?php
  include_once 'includes/modalSaveResultModal.html';
  include_once 'includes/modalEditPcrModal.html';
  include_once 'includes/modalArchiveRfsPcrModal.html';
  include_once 'includes/modalDeleteResultModal.html';
  include_once 'includes/modalDeleteAssignment.html';
?>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<style type="text/css">
.dataTables_wrapper .dataTables_processing {
    background-color:#006699;
    height: 60px;
}
td.dataTables_empty {
  color:white;
	text-align: center;
	font-size: 20px;
	background-color:#006699;
}

<?php
$date = new DateTime();
$currentYear = $date->format('Y');
echo $currentYear;

for($year=$currentYear-1;$year<=$currentYear+1;$year++){
    for($month=1;$month<=12;$month++){
        $date = '01-' . substr('00' . $month,2) . "-" . $year;
        $claimCutoff = DateClass::claimMonth($date);
        ?>[data-pika-year="<?=$year;?>"][data-pika-month="<?=$month-1;?>"][data-pika-day="<?=$claimCutoff->format('d');?>"] {background-color: white; color:red; outline:solid; outline-color:grey;outline-width:thin; content='claim'}<?php
    }
}
?>
</style>