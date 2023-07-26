<?php
use itdq\Trace;
use itdq\Loader;
use itdq\DateClass;
use rest\allTables;
use rest\rfsTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);

// $loader = new Loader();
// $allRfs = $loader->load('RFS_ID',allTables::$RFS, " ARCHIVE is null ");
// $allValueStream = $loader->load('VALUE_STREAM',allTables::$RFS, " ARCHIVE is null ");
// $allBusinessUnits = $loader->load('BUSINESS_UNIT',allTables::$RFS, " ARCHIVE is null ");
// $allRequestor = $loader->load('REQUESTOR_EMAIL',allTables::$RFS, " ARCHIVE is null ");

// $defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
// $canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;

$liveChecked = null;
$archiveChecked = null;
$bothChecked = null;
$defaultToLive = null;
if (isset($_COOKIE['pipelineLiveArchiveChecked'])) {
  $checkedPipeline = $_COOKIE['pipelineLiveArchiveChecked'];
  switch($checkedPipeline) {
    case 'live':
      $liveChecked = 'checked';
      break;
    case 'archive':
      $archiveChecked = 'checked';
      break;
    case 'both':
      $bothChecked = 'checked';
      break;
    default:
      break;
  }
} else {
  $defaultToLive = 'checked';
}

$liveDisabled     = (!($_SESSION['isAdmin'])) &&  $_SESSION['isRfs'] ? 'disabled' : null;
$archiveDisabled  = (!($_SESSION['isAdmin'])) && !$_SESSION['isRfs'] ? 'disabled' : null;
$bothDisabled     = (!($_SESSION['isAdmin'])) && !$_SESSION['isRfs'] ? 'disabled' : null;
?>

<div class='container'>

<h3>RFS Report</h3>
<form id='reportDates' class='form-horizontal'>
<?php
/*
	<div class='row'>
  <div class='col-md-5'> 
	<div class='form-group text-right' >
	 <label for='pipelineLiveArchive' class='col-md-3 control-label text-right'>RFS Status</label>
      	<div class='col-md-9  text-left' >
 			  <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" <?=$pipelineChecked?>  data-toggle="button" value='pipeline' <?=$pipelineDisabled?>>Pipeline</label>
  			<label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" <?=$liveChecked?> <?=$defaultToLive?> data-toggle="button" value='live' <?=$liveDisabled?> >Live</label>
        <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" <?=$bothChecked?> data-toggle="button" value='both' <?=$bothDisabled?> >Both</label>
  			<label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" <?=$archiveChecked?> data-toggle="button" value='archive'  <?=$archiveDisabled?>>Archive</label>
	    </div>
	</div>
  </div>
	</div>
*/
?>
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
    <label for='selectValueStream' class='col-md-1 control-label text-right'>Value Stream</label>
    <div class='col-md-2 text-left'>
      <select class='form-control select' id='selectValueStream'
        name='selectValueStream'
        data-placeholder="select Value Stream" data-allow-clear="true"
        >
        <option value=''>Select Value Stream</option>
        <option value='All'>All</option>
      </select>
    </div>
    <label for='selectBusinessUnit' class='col-md-1 control-label text-right'>Business Unit</label>
    <div class='col-md-2 text-left'>
      <select class='form-control select' id='selectBusinessUnit'
        name='selectValueStream'
        data-placeholder="select Business Unit" data-allow-clear="true"
        >
        <option value=''>Select Business Unit</option>
        <option value='All'>All</option>
      </select>
    </div>  
    <label for='selectRequestor' class='col-md-1 control-label text-right'>Requestor</label>
    <div class='col-md-2 text-left'>
      <select class='form-control select' id='selectRequestor'
        name='selectRequestor'
        data-placeholder="Select Requestor" data-allow-clear="true"
        >
        <option value=''>Select Requestor</option>
        <option value='All'>All</option>
      </select>
    </div>
  </div>
  <div class='row'>
    <div class='col-md-5'> 
      <div class='form-group text-right' >
        <label for='pipelineLiveArchive' class='col-md-3 control-label text-right'>RFS Status</label>
          <div class='col-md-9  text-left' >
            <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" <?=$liveChecked?> <?=$defaultToLive?> data-toggle="button" value='live' <?=$liveDisabled?> >Live</label>
            <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" <?=$archiveChecked?> data-toggle="button" value='archive'  <?=$archiveDisabled?>>Archive</label>
            <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" <?=$bothChecked?> data-toggle="button" value='both' <?=$bothDisabled?> >Both</label>
          </div>
      </div>
    </div>
	</div>
</form>
</div>

<hr/>

<div class='container-fluid'>
<div id='rfsTableDiv'>
<?php
  rfsTable::buildHTMLTable();
?>
</div>
</div>

<?php
  include_once 'includes/modalSaveResultModal.html';
  include_once 'includes/modalDeleteRfsModal.html';
  include_once 'includes/modalArchiveRfsModal.html';
  include_once 'includes/modalEditPcrModal.html';
  include_once 'includes/modalEditRfsModal.html';
  include_once 'includes/modalSlipRfsModal.html';
  include_once 'includes/modalGoLiveRfsModal.html';
  include_once 'includes/modalSwitchRfsIdModal.html';
  include_once 'includes/modalExtendRfsModal.html';
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