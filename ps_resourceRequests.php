<?php
use itdq\Trace;
use rest\allTables;
use rest\resourceRequestRecord;
use itdq\Loader;
use rest\resourceRequestTable;
use itdq\DateClass;
use rest\rfsTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);

$loader = new Loader();
$rfsPredicate = rfsTable::rfsPredicateFilterOnPipeline();
// $allRfs = $loader->load('RFS',allTables::$RESOURCE_REQUESTS,$rfsPredicate);
$allCtbService =  $loader->load('ORGANISATION',allTables::$RESOURCE_REQUESTS);
// $vbacEmployees = resourceRequestTable::getVbacActiveResourcesForSelect2();

$defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
$defaultForWithoutArchive = 'checked' ;
$canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;

?>
<style>

td.dataTables_empty{
    color:white;
}

</style>


<div class='container'>

<h3>Report Selection </h3>

<form id='reportDates'>
	<div class='form-group text-right' >
	    <div class='col-md-7'>
		<div class='row''>
      	<div class='col-md-9 col-md-offset-3 text-left' >
 			<label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" data-toggle="button" value='pipeline'>Pipeline</label>
  			<label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" data-toggle="button" value='live' checked >Live</label>
  			<label class='radio-inline control-label '><input type="radio" name="pipelineLiveArchive" data-toggle="button" value='archive' >Archive</label>
	    </div>
	    </div>
	    

	    <div class='row'>
        <label for='selectOrganisation' class='col-md-3 control-label text-right'>Organisation</label>
        	<div class='col-md-9 text-left'>
              	<select class='form-control select' id='selectOrganisation'
                  	          name='organisation'
                  	          data-placeholder="Select Organisation" data-allow-clear="true"
                  	          >
            	<option value=''>Select Organisation</option>
            	<option value='All'>All</option>
                <?php
                    foreach ($allCtbService as $value) {
                         $displayValue = trim($value);
                         $returnValue  = trim($value);
                         $selectedOrg = isset($_COOKIE['selectedOrganisation']) ? $_COOKIE['selectedOrganisation'] : null;
                         $selected = $returnValue==$selectedOrg ? 'selected' : null;
                         ?><option value='<?=$returnValue?>' <?=$selected;?> ><?=$displayValue?></option><?php
                    }
               ?>
               </select>
            </div>

	    
	    
	    </div>

	    <div class='row'>
	    
	    <label for='selectRfs' class='col-md-3 control-label text-right'>RFS</label>
        	<div class='col-md-9 text-left'>
              	<select class='form-control select' 
              			id='selectRfs'
                  	    name='selectRfs'
                  	    data-placeholder="Select RFS" 
                  	    data-allow-clear="true"
                ></select>
            </div>
	    
	    
	    </div>
	    
	    
	    </div>








    </div>
</form>
</div>

<hr/>

<div class='container-fluid'>
<h3>Resource Request Report</h3>
<button id='ddDetails' class='btn btn-primary btn-sm'>DD Details</button>
<button id='resetReport' class='btn btn-primary btn-sm'>Reset</button>
<input type='hidden' id='bwo' value='' />
<div id='messageArea'></div>
<div id='resourceTableDiv'>
</div>
</div>




<!-- Modal -->
<div id="resourceNameModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Allocate Resource Name</h4>
      </div>
      <form id='resourceNameForm'>
      <div class="modal-body">
  		<div class="form-group required" id="PROJECT_TITLEFormGroup" >
  		<div class='row'>
            <label for="RESOURCE_NAME" class="col-md-3	control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Resource Name</label>
              <div class="col-md-9">
              	<select class='form-control select' id='RESOURCE_NAME'
                  	          name='RESOURCE_NAME'
                  	          required='required'
                  	          data-placeholder="Select Resource"
                  	          data-allow-clear="true"
                  	          disabled="true"
                  	           >
            	<option value=''>Select Resource<option>
               	</select>
               	<p id='pleaseWaitMessage'></p>
                  <input type='hidden' id="RESOURCE_REFERENCE" name="RESOURCE_REFERENCE" value="" >
                  <input type='hidden' id="parent" name="parent" value="" >
                  <input type='hidden' id="currentResourceName"  value="" >
              </div>
        </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='saveResourceName'>Save</button>
        <button type="button" class="btn btn-primary" id='clearResourceName'>Clear Resource Name</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
     </form>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="resourceHoursModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Amend Hours</h4>
      </div>
      <div class="modal-body" id='editResourceHours'>
      </div>
      <div class="modal-footer" id='editResourceHoursFooter'>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="confirmDuplicationModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ConfirmDuplication</h4>
      </div>
      <div class="modal-body" id='confirmDuplication'>
      <p><b>RFS :</b><span id='confirmDuplicateRFS'></span></p>
      <p><b>Service :</b><span id='confirmDuplicateType'></span></p>
      <p><b>Start Date :</b><span id='confirmDuplicateStart'></span></p>
      <p><b>Resource Reference :</b><span id='confirmDuplicateRR'></span></p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='duplicationConfirmed'>Confirm</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="errorMessageModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Error Message</h4>
      </div>
      <div class="modal-body" >
         <div class="panel panel-danger">
      		<div class="panel-heading">Error</div>
      		<div class="panel-body" id='errorMessageBody'></div>
    		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="confirmDeleteModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm Delete</h4>
      </div>
      <div class="modal-body" id='deleteMessageBody'>
      </div>
      <form id='confirmDeleteModalForm'>
      <input type='hidden' id='deleteResourceRef' name='RESOURCE_REFERENCE' val=''>
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirmDeleteResource'>Confirm</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


 <!-- Modal for Status -->
<div id="statusModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" >
        <h4 class="modal-title">Status</h4>
      </div>
      <div class="modal-body" >

		<form id='statusChangeForm' class='form-horizontal' >
		<div class="form-group">
		    <label for="statusChangeRR" class='col-sm-2' >Reference</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangeRR"  name="statusChangeRR" disabled>
	    	</div>
  		</div>
  		<div class="form-group">
		    <label for="statusChangeRfs" class='col-sm-2' >RFS</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangeRfs" name="statusChangeRfs" disabled>
	    	</div>
  		</div>
  		<div class="form-group" >
		    <label for="statusChangePhase" class='col-sm-2'>Phase</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangePhase" name="statusChangePhase" disabled>
	    	</div>
  		</div>
		<div class="form-group">
		    <label for="statusChangeService" class='col-sm-2' >Organisation</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangeService" name="statusChangeService" disabled>
	    	</div>
  		</div>
  		<div class="form-group">
		    <label for="statusChangeSub" class='col-sm-2' >Service</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangeSub" name="statusChangeSub" disabled>
	    	</div>
  		</div>

		<div class="form-group">
		    <label for="statusChangeStart" class='col-sm-2' >Start</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangeStart" name="statusChangeStart" disabled>
	    	</div>
  		</div>

  		<div class="form-group">
  		<label class='col-sm-2'>Status</label>

  		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_ASSIGNED ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_ASSIGNED)?>' ><?=resourceRequestRecord::STATUS_ASSIGNED ?></label>
		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_COMPLETED ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_COMPLETED)?>'><?=resourceRequestRecord::STATUS_COMPLETED ?></label>
		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_NEW ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_NEW)?>'><?=resourceRequestRecord::STATUS_NEW ?></label>
		</div>
        </form>

	</div>
    <div class="modal-footer" >
       	<button type="button" class="btn btn-primary" id='saveStatusChange'>Save</button>
    </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div id="editRequestModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit RFS</h4>
      </div>
      <div class="modal-body" id='editRequestModalBody'>
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
.dataTables_wrapper .dataTables_processing {
background-color:#006699;
}
td.dataTables_empty {
	text-align: center;
	font-size: 20px;
	background-color:#006699;
}


</style>



<script>

var startPicker;


$(document).ready(function() {
	console.log('ready now');
	$('#pleaseWaitMessage').html('Please wait while resource list is fetched');
	var allowPast = true;
	$(".select").not('#selectRfs').select2();
	
	console.log('setup all the listeners');
	var resourceRequest = new ResourceRequest();
	resourceRequest.prepareRfsSelect();
	resourceRequest.initialiseDateSelect(allowPast);
	resourceRequest.buildResourceReport();
	resourceRequest.populateResourceDropDownWhenModalShown();
	resourceRequest.listenForEditResourceName();
	resourceRequest.listenForSaveResourceName();
	resourceRequest.listenForClearResourceName();
	resourceRequest.listenForEditHours();
	resourceRequest.listenForSlipStartDate();
	resourceRequest.listenForReinitialise();
	resourceRequest.listenForMoveEndDate();
	resourceRequest.listenForDuplicateResource();
 	resourceRequest.listenForConfirmedDuplication();
 	resourceRequest.listenForChangingHours();
 	resourceRequest.listenForSaveAdjustedHours();
 	resourceRequest.listenForSaveAdjustedHoursWithDelta();
 	resourceRequest.listenForSaveStatusChange();
	resourceRequest.listenForResetReport();
	resourceRequest.listenForDdDetails();
	resourceRequest.listenForEditRecord();
	resourceRequest.listenForDeleteRecord();
	resourceRequest.listenForConfirmedDelete();
	resourceRequest.listenForChangeStatus();
	resourceRequest.listenForChangePipelineLiveArchive();
	resourceRequest.listenForSelectSpecificRfs();
	resourceRequest.listenForSelectOrganisation();
	$('[data-toggle="tooltip"]').tooltip();

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