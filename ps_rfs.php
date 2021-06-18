<?php
use itdq\Trace;
use itdq\Loader;
use itdq\DateClass;
use rest\allTables;
use rest\rfsTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
$loader = new Loader();
$allRfs = $loader->load('RFS_ID',allTables::$RFS, " ARCHIVE is null ");
$allValueStream = $loader->load('VALUE_STREAM',allTables::$RFS, " ARCHIVE is null ");
$allBusinessUnits = $loader->load('BUSINESS_UNIT',allTables::$RFS, " ARCHIVE is null ");
$allRequestor = $loader->load('REQUESTOR_EMAIL',allTables::$RFS, " ARCHIVE is null ");
$rfsTable = new rfsTable(allTables::$RFS);

// $defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
// $canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;
?>
<?php $pipelineChecked = isset($_COOKIE['pipelineChecked']) ? $_COOKIE['pipelineChecked'] : null;?>
<?php $liveChecked     = isset($_COOKIE['liveChecked']) ? $_COOKIE['liveChecked'] : null;?>
<?php $bothChecked     = isset($_COOKIE['bothChecked']) ? $_COOKIE['bothChecked'] : null;?>
<?php $archiveChecked  = isset($_COOKIE['archiveChecked']) ? $_COOKIE['archiveChecked'] : null;?>
<?php $defaultToLive   = (empty($pipelineChecked) && empty($liveChecked) && empty($archiveChecked)) ? ' checked ' : null?>

<?php $pipelineDisabled = (!($_SESSION['isAdmin'])) && !$_SESSION['isRfs'] ? 'disabled' : null;?>
<?php $liveDisabled     = (!($_SESSION['isAdmin'])) &&  $_SESSION['isRfs'] ? 'disabled' : null;?>
<?php $bothDisabled     = (!($_SESSION['isAdmin'])) && !$_SESSION['isRfs'] ? 'disabled' : null;?>

<?php $archiveDisabled  = 'disabled' ?>


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
  <input type='hidden' id="pipelineLiveArchive" name="pipelineLiveArchive" value="live" >
	<div class='form-group text-right' >
    <label for='selectRfs' class='col-md-1 control-label text-right'>RFS</label>
    <div class='col-md-2 text-left'>
      <select class='form-control select' id='selectRfs'
        name='selectRfs'
        data-placeholder="Select RFS" data-allow-clear="true"
        >
        <option value=''>Select RFS</option>
        <option value='All'>All</option>
      <?php
          foreach ($allRfs as $value) {
                $displayValue = trim($value);
                $returnValue  = trim($value);
                $selectedRFs = isset($_COOKIE['selectedRfs']) ? $_COOKIE['selectedRfs'] : null;
                $selected = $returnValue==$selectedRFs ? 'selected' : null;
                ?><option value='<?=$returnValue?>' <?=$selected;?> ><?=$displayValue?></option><?php
          }
      ?>
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
        <?php
            foreach ($allValueStream as $value) {
                  $displayValue = trim($value);
                  $returnValue  = trim($value);
                  $selectedValueStream = isset($_COOKIE['selectedValueStream']) ? $_COOKIE['selectedValueStream'] : null;
                  $selected = htmlspecialchars_decode($returnValue)==htmlspecialchars_decode($selectedValueStream) ? 'selected' : null;
                  ?><option value='<?=$returnValue?>' <?=$selected;?> ><?=$displayValue?></option><?php
            }
        ?>
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
        <?php
            foreach ($allBusinessUnits as $value) {
                  $displayValue = trim($value);
                  $returnValue  = trim($value);
                  $selectedBusinessUnit = isset($_COOKIE['selectedBusinessUnit']) ? $_COOKIE['selectedBusinessUnit'] : null;
                  $selected = htmlspecialchars_decode($returnValue)==htmlspecialchars_decode($selectedBusinessUnit) ? 'selected' : null;
                  ?><option value='<?=$returnValue?>' <?=$selected;?> ><?=$displayValue?></option><?php
            }
        ?>
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
        <?php
        foreach ($allRequestor as $value) {
                  $displayValue = trim($value);
                  $returnValue  = trim($value);
                  $selectedRequestor = isset($_COOKIE['selectedRequestor']) ? $_COOKIE['selectedRequestor'] : null;
                  $selectedRequestor = $returnValue==$selectedRequestor ? 'selected' : null;
                  ?><option value='<?=$returnValue?>' <?=$selected;?> ><?=$displayValue?></option><?php
            }
        ?>
        </select>
      </div>
     </div>
</form>
</div>

<hr/>

<div class='container-fluid'>
<div id='rfsTableDiv'>
</div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">RFS Edit Result</h4>
      </div>
      <div class="modal-body" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="deleteRfsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete RFS</h4>
      </div>
      <div class="modal-body">
          <div class="panel panel-danger">
      		<div class="panel-heading">Caution</div>
      		<div class="panel-body" id='deleteRfsModalBody'></div>
    		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='deleteConmfirmedRfs'>Confirm</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Retain</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="archiveRfsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Archive RFS</h4>
      </div>
      <div class="modal-body" >
            <div class="panel panel-warning">
      		<div class="panel-heading">Caution</div>
      		<div class="panel-body" id='archiveRfsModalBody'></div>
    		</div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary glyphicon	" id='archiveConfirmedRfs'>Confirm</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Retain</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="editRfsModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit RFS</h4>
      </div>
      <div class="modal-body" id='editRfsModalBody'>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<!-- Modal -->
<div id="slipRfsModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Amend Dates for RFS</h4>
      </div>
      <div class="modal-body" id='slipRfsModalBody'>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='saveSlippedRfsDates'>Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="goLiveRfsModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Pipeline to Live</h4>
      </div>
      <div class="modal-body" id='goLiveRfsModalBody'>    
      <form id='goLiveRfsForm' onsubmit='return false'>
        <div class='row' style='padding-top: 5px;'>
      		<div class="form-group required " id="REQUESTOR_NAMEFormGroup">
				<label for="plREQUESTOR_NAME"
					class="col-md-3 control-label ceta-label-left" data-toggle="tooltip"
					data-placement="top" title="">Requestor Name</label>
				<div class="col-md-5">
					<input class="form-control" id="plREQUESTOR_NAME" name="plREQUESTOR_NAME"
						value=""
						placeholder="Enter Project Mgr Name" required="required" type="text"
					maxlength="<?=$rfsTable->getColumnLength('REQUESTOR_NAME');?>">
			
				</div>
			</div>		
		</div>
		<div class='row' style='padding-top: 5px;'>
			<div class="form-group required " id="REQUESTOR_NAMEFormGroup">
				<label for="plREQUESTOR_EMAIL"
					class="col-md-3 control-label ceta-label-left" data-toggle="tooltip"
					data-placement="top" title="">Requestor Email</label>
				<div class="col-md-5">
					<input class="form-control" id="plREQUESTOR_EMAIL"
						name="plREQUESTOR_EMAIL" value=""
						placeholder="Enter Project Mgr IBM Email" required="required" type="email"
						maxlength="<?=$rfsTable->getColumnLength('REQUESTOR_EMAIL');?>"
						>
			
				</div>
			</div>
		</div>	
		<div class='row' style='padding-top: 5px; padding-left: 17px;'>	
			<input name='RfsId' id='goLiveRfsId' type='hidden' >		
			<input type='submit' class="btn btn-primary col-md-1 col-md-offset-3" name='confirmGoLiveRfs' id='confirmGoLiveRfs' enabled value='Confirm' >			
		</div>
		</form>
		
	</div> 

      <div class="modal-footer">        
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<style>
td.dataTables_empty{
    color:white;
}

.dataTables_wrapper .dataTables_processing {
background-color:#006699;
}
td.dataTables_empty {
	text-align: center;
	font-size: 20px;
	background-color:#006699;
}


</style>
<script type='text/javascript'>


$(document).ready(function() {
	
	$(".select").select2();

  var rfs = new Rfs();
	rfs.buildRfsReport();
	rfs.listenForGoLiveRfs();
	rfs.listenForConfirmGoLiveRfs();
	rfs.listenForDeleteRfs();
	rfs.listenForConfirmDeleteRfs();
	rfs.listenForEditRfs();
	rfs.listenForSlipRfs();
	rfs.listenForSaveSlippedRfsDates();
	rfs.listenForArchiveRfs();
	rfs.listenForConfirmArchiveRfs();
	rfs.listenForSelectRequestor();
	rfs.listenForSelectValueStream();
	rfs.listenForSelectBusinessUnit();
	rfs.listenForSelectRfs();
});


$(document).on('shown.bs.modal','#editRfsModal',function(e){   
    $("input[type='radio'][name='RFS_STATUS']").attr('disabled',true);   
	var rfs = new Rfs();
	rfs.preventDuplicateRfsEntry();
	rfs.listenForSaveRfs();
	rfs.refreshReportOnRfsUpdate();
});

var startPickers = [];
var endPickers = [];


$(document).on('shown.bs.modal','#slipRfsModal',function(e){        
    $('.startDate').each(function(index,element){
    	var reference = $(element).data('reference');
    	var rfs = new Rfs();
    	startPickers[index] = rfs.prepareStartDateOnModal(element);    	    
    });
    
    $('.endDate').each(function(index,element){
    	var reference = $(element).data('reference');
    	var rfs = new Rfs();
    	endPickers[index] = rfs.prepareEndDateOnModal(element);    	    
    });       

});

$(document).on('hide.bs.modal','#goLiveRfsModal',function(e){	
	$('.spinning').removeClass('spinning').attr('disabled',false);
});

$(document).on('keyup','#plREQUESTOR_EMAIL',function(){
	var regex = RegExp('ibm.com$');
	var email = $('#plREQUESTOR_EMAIL').val().trim().toLowerCase();
	var ibmEmailAddress = regex.test(email);
	ibmEmailAddress ? $("#confirmGoLiveRfs").attr('disabled',false)    : $("#confirmGoLiveRfs").attr('disabled',true);
	ibmEmailAddress ? $("#plREQUESTOR_EMAIL").css('color','DARKGREEN') : $('#plREQUESTOR_EMAIL').css('color','CRIMSON');
});

$(document).on('keyup','#REQUESTOR_EMAIL',function(){
	var regex = RegExp('ibm.com$');
	var email = $('#REQUESTOR_EMAIL').val().trim().toLowerCase();
	var ibmEmailAddress = regex.test(email);
	ibmEmailAddress ? $("input[name='Submit']").attr('disabled',false) : $('input[name="Submit"]').attr('disabled',true);
	ibmEmailAddress ? $("#REQUESTOR_EMAIL").css('color','DARKGREEN') : $('#REQUESTOR_EMAIL').css('color','CRIMSON');
});



</script>


<style>

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


