<?php

use itdq\Trace;
use rest\allTables;
use rest\resourceRequestRecord;
use itdq\Loader;
use rest\resourceRequestTable;
use itdq\DateClass;
use rest\rfsTable;
use rest\staticOrganisationTable;
use itdq\JavaScript;

set_time_limit(0);
Trace::pageOpening($_SERVER['PHP_SELF']);

// $loader = new Loader();
// $rfsPredicate = rfsTable::rfsPredicateFilterOnPipeline();
// $allRfs = $loader->load('RFS',allTables::$RESOURCE_REQUESTS,$rfsPredicate);
// $allCtbService    =  $loader->load('ORGANISATION',allTables::$RESOURCE_REQUESTS);
// $allBusinessUnits =  $loader->load('BUSINESS_UNIT',allTables::$RFS);

// $defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
// $defaultForWithoutArchive = 'checked' ;
// $canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;

// $pipelineChecked     =  $_SESSION['isRfs'] ? ' checked ' : null;
// $nonPipelineDisabled =  $_SESSION['isRfs'] ? ' disabled ' : null;
// $defaultToLive       =  empty($pipelineChecked) ? 'checked' : null;

// $predicate = " STATUS='" . staticOrganisationTable::ENABLED . "' ";
// $allService = staticOrganisationTable::getAllOrganisationsAndServices($predicate);
// JavaScript::buildSelectArray($allService, 'organisation');

?>

<div class='container'>

<h3>Assign Resource to Requests - Selection</h3>

<form id='reportDates' class='form-horizontal'>
<?php
/*
	<div class='row'>
    <div class='col-md-5'> 
      <div class='form-group' >
        <label for='pipelineLiveArchive' class='col-md-3 control-label text-right'>RFS Status</label>
        <div class='col-md-9  text-left' >
          <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" data-toggle="button" value='pipeline' disabled >Pipeline</label>
          <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" checked  data-toggle="button" value='live' >Live</label>
          <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" data-toggle="button" value='both' disabled >Both</label>
          <label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" data-toggle="button" value='archive' disabled >Archive</label>
        </div>
      </div>
    </div>
	</div>
*/
?>
  <input type='hidden' id="pipelineLiveArchive" name="pipelineLiveArchive" value="live" >
	<div class='row'>
	   <div class='col-md-6'>  
       <div class='form-group'>
        <label for='selectOrganisation' class='col-md-3 control-label text-right'>Organisation</label>
        <div class='col-md-9 text-left'>
            <select class='form-control select' id='selectOrganisation'
              name='organisation'
              data-placeholder="Select Organisation" data-allow-clear="true"
              >
              <option value=''>Select Organisation</option>
              <option value='All'>All</option>
            </select>
          </div>
	    </div>
	    
	    <div class='form-group'>
        <label for='selectBusinessUnit' class='col-md-3 control-label text-right'>Business Unit</label>
        <div class='col-md-9 text-left'>
          <select class='form-control select' id='selectBusinessUnit'
            name='businessUnit'
            data-placeholder="Select Business Unit" data-allow-clear="true"
            >
            <option value=''>Select Business Unit</option>
            <option value='All'>All</option>
          </select>
        </div>
	    </div>
	    
    </div>
    <div class='col-md-6'>
      <div class='form-group'>	    
        <label for='selectRfs' class='col-md-1 control-label text-right'>RFS</label>
        <div class='col-md-9 text-left'>
          <select class='form-control select' id='selectRfs'
            name='selectRfs'
            data-placeholder="Select RFS" 
            data-allow-clear="true"
            >
            <option value=''>Select RFS</option>
            <option value='All'>All</option>
          </select>
        </div>
      </div>
    </div>  
  </div>
	    	    
</form>
</div>

<hr/>

<div class='container-fluid'>
<h3>Assign Resource to Request - Report</h3>
<button id='unallocated' class='btn btn-primary btn-sm'>Unallocated/New</button>
<button id='completeable' class='btn btn-primary btn-sm'>Assigned & past end date</button>
<button id='plannedOnly' class='btn btn-primary btn-sm'>Planned Only</button>
<button id='activeOnly' class='btn btn-primary btn-sm'>Active Only</button>
<button id='removePassed' class='btn btn-primary btn-sm'>Remove past end date</button>
<button id='resetReport' class='btn btn-primary btn-sm'>Reset</button>
<input type='hidden' id='bwo' value='' />
<div id='messageArea'></div>
<div id='resourceTableDiv'>
<?php
  $startDate = isset($_POST['START_DATE']) ? trim($_POST['START_DATE']) : null;
  $endDate = isset($_POST['END_DATE']) ? trim($_POST['END_DATE']) : null;
  resourceRequestTable::buildHTMLTable('resourceRequests', $startDate, $endDate);
?>
</div>
</div>

<?php
  include_once 'includes/modalResourceNameModal.html';
  include_once 'includes/modalResourceHoursModal.html';
  include_once 'includes/modalRecordSavedModal.html';
  include_once 'includes/modalEndEarlyModal.html';
  include_once 'includes/modalConfirmDuplicationModal.html';
  include_once 'includes/modalConfirmArchiveModal.html';
  include_once 'includes/modalConfirmDeleteModal.html';
  include_once 'includes/modalEditRequestModal.html';
  include_once 'includes/modalDiaryModal.html';
  include_once 'includes/modalStatusModal.html';
  include_once 'includes/modalOverrideBespokeRateModal.html';
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
  div.diaryEntry { 
    height: 55px;
    overflow-y: scroll;
    border: 2px solid lightgray;
  }
  div.diary {
    padding-top: 1em;
    background-color: whitesmoke;  
    height: 200px;
    overflow-y: scroll;
    border: 2px solid lightgray;
  }

  [contentEditable=true]:empty:not(:focus):before{
      content:attr(data-placeholder);
      color:#aea79f;
  }

  ul.select2-results li:nth-child(even){
    background-color: #ddd;
    color: black;
  }
  ul.select2-results li:nth-child(even):hover{
    background-color: #333;
    color: white;
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