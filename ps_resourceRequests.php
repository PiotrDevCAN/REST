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

$loader = new Loader();
$rfsPredicate = rfsTable::rfsPredicateFilterOnPipeline();
// $allRfs = $loader->load('RFS',allTables::$RESOURCE_REQUESTS,$rfsPredicate);
$allCtbService    =  $loader->load('ORGANISATION',allTables::$RESOURCE_REQUESTS);
$allBusinessUnits =  $loader->load('BUSINESS_UNIT',allTables::$RFS);
// $vbacEmployees = resourceRequestTable::getVbacActiveResourcesForSelect2();

$defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
$defaultForWithoutArchive = 'checked' ;
$canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;

$pipelineChecked     =  $_SESSION['isRfs'] ? ' checked ' : null;
$nonPipelineDisabled =  $_SESSION['isRfs'] ? ' disabled ' : null;
$defaultToLive       =  empty($pipelineChecked) ? 'checked' : null;

$predicate = " STATUS='" . staticOrganisationTable::ENABLED . "' ";
$allService = staticOrganisationTable::getAllOrganisationsAndServices($predicate);
JavaScript::buildSelectArray($allService, 'organisation');

?>
<style>

td.dataTables_empty{
    color:white;
}

</style>


<div class='container'>

<h3>Assign Resource to Requests - Selection</h3>

<form id='reportDates'>
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
	<div class='row'>
	   <div class='col-md-5'>  
       <div class='form-group'>
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
                         $selected = htmlspecialchars_decode($returnValue)==htmlspecialchars_decode($selectedOrg) ? 'selected' : null;
                         ?><option value='<?=$returnValue?>' <?=$selected;?> ><?=$displayValue?></option><?php
                    }
               ?>
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
	    </div>
	    
	    </div>

	    <div class='form-group'>	    
	    <label for='selectRfs' class='col-md-1 control-label text-right'>RFS</label>
        	<div class='col-md-3 text-left'>
              	<select class='form-control select' 
              			id='selectRfs'
                  	    name='selectRfs'
                  	    data-placeholder="Select RFS" 
                  	    data-allow-clear="true"
                ></select>
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
      <div class="form-group"  >
      <label for="businessUnit" class="col-md-3	control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Business Unit</label>
      <div class='col-md-9'><input type='text' class='form-control' id="businessUnit"  value="" disabled ></div>
      </div>
      
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
<div id="recordSavedModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Outcome of Record Update</h4>
      </div>
      <div class="modal-body" id='recordSaveDiv'>
      </div>
      <div class="modal-footer" >       
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="endEarlyModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Indicate Task Completed</h4>
      </div>
      <div class="modal-body" id='endEarlyDateDiv'>
     
      <form class='form-horizontal'>
        <div class='form-group'>
   		<label for="endEarlyRFS" class="col-sm-3 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="RFS">RFS</label>
   		<div class='col-sm-5'>
   		<input class="form-control" id="endEarlyRFS" name="endEarlyRFS" value="" placeholder="RFS" disabled />
   		</div>
		</div>

        <div class='form-group'>
  		<label for="endEarlyRR" class="col-sm-3 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Resource Reference">Resource Reference</label>
   		<div class='col-sm-5'>
   		<input class="form-control" id="endEarlyRR" name="endEarlyRR" value="" placeholder="Resource Reference" disabled />
   		</div>
   		</div>
   		
        <div class='form-group'>
  		<label for="endEarlyOrganisation" class="col-sm-3 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Organisation">Organisation</label>
   		<div class='col-sm-5'>
   		<input class="form-control" id="endEarlyOrganisation" name="endEarlyOrganisation" value="" placeholder="Organisation" disabled>
   		</div>
   		</div>

        <div class='form-group'>   		
   		<label for="endEarlyService" class="col-sm-3 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Service">Service</label>
   		<div class='col-sm-5'>
   		<input class="form-control" id="endEarlyService" name="endEarlyService" value="" placeholder="Service" disabled>
   		</div>
   		</div>

        <div class='form-group'>   		
   		<label for="endEarlyResource" class="col-md-3 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Resource">Resource</label>
   		<div class='col-sm-5'>
   		<input class="form-control" id="endEarlyResource" name="endEarlyResource" value="" placeholder="Resource" disabled>
		</div>
		</div>
           
        <div id='endEarlyEND_DATEFormGroup' class='form-group'>
        	<label for='endEarlyEND_DATE' class='col-sm-3 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title=''>End Date</label>
        	<div class='col-sm-5'>
        		<div id='endEarlyFormGroupEND_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='END_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
        		<input id='endEarlyInputEND_DATE' class='form-control' type='text' readonly value='' placeholder='Early End Date' required  />
        		<input type='hidden' id='endEarlyEND_DATE' name='endEarlyEND_DATE' value='' />
        		<span class='input-group-addon'><span id='endEarlyIconEND_DATE' class='glyphicon glyphicon-calendar'></span></span>
        		</div>
        	</div>
        	
        </div>
        <input id="endEarlyEndWas" name="endEarlyEndWas" value="" type='hidden' disabled>
        <input id='endEarlyStart_Date' value='' type='hidden'  />
        </form>
        </div>

      <div class="modal-footer" > 
        <button type="button" class="btn btn-primary" id='endEarlyConfirmed'>Confirm</button>      
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
        <p style='color:red;text-align:center'>Please be 100% sure this record should be <b>deleted</b><br/>as it's a time consuming process to recover it should later prove to be required.<br/>Consider Archiving if you just want the record removed.</p>
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
        <h4 class="modal-title">Edit Request</h4>
      </div>
      <div class="modal-body" id='editRequestModalBody'>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<!-- Modal -->
<div id="diaryModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Diary</h4>
      </div>
      <div class="modal-body">
      <form id='diaryForm'>
  		<div class="form-group " >
  		<div class='row'>
            <label for="rfs" class="col-md-3 control-label ceta-label-left required" data-toggle="tooltip" data-placement="top" title="" data-original-title="">RFS</label>
           <div class='col-md-8'>
           <input class="form-control" id="rfs" value="" placeholder="RFS Id"  type="text" maxlength="20" disabled>
           </div>
        </div>
  		<div class='row'>
            <label for="request" class="col-md-3 control-label ceta-label-left required" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Request</label>
           <div class='col-md-8'>
           <input class="form-control" id="request" value="" placeholder="Request"  type="text" maxlength="20" disabled>
           </div>
        </div>
        <div class='row'>
            <label for="organisation" class="col-md-3 control-label ceta-label-left required" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Organisation</label>
           <div class='col-md-8'>
           <input class="form-control" id="organisation" value="" placeholder="Organisation"  type="text" maxlength="20" disabled>
           </div>
        </div>
        </div>   
  		<div class="form-group " >
  		<div class='row'>
            <label for="newDiaryEntry" class="col-md-3	control-label ceta-label-left required" data-toggle="tooltip" data-placement="top" title="" data-original-title="">New Entry</label>
            <div class="col-md-8 diaryEntry" contenteditable='true' id='newDiaryEntry' data-placeholder='type new diary entry here'></div>
        </div>
        <div class='row'>       
             <div class="col-md-10 col-md-offset-1	 diary" id='diary'></div>
        </div>
        </div>    
        <input type='hidden' id='RESOURCE_REQUEST' value=''/>    
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='saveDiaryEntry' disabled >Save</button>       
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


</style>

<script type='text/javascript'>

var startPicker;


$(document).ready(function() {
	$('#pleaseWaitMessage').html('Please wait while resource list is fetched');
	var allowPast = true;
	$(".select").not('#selectRfs').select2();
	
	var resourceRequest = new ResourceRequest();
	resourceRequest.prepareRfsSelect();
//	resourceRequest.initialiseDateSelect(allowPast); This was causing the use of up and down arrows to change the Date Field on the form which we didn't want.
	resourceRequest.buildResourceReport();
	resourceRequest.populateDiaryWhenModalShown();
	resourceRequest.populateResourceDropDownWhenModalShown();
	resourceRequest.listenForEditResourceName();
	resourceRequest.listenForSaveResourceName();
	resourceRequest.listenForClearResourceName();
	resourceRequest.listenForEditHours();
	resourceRequest.listenForSlipStartDate();
	resourceRequest.listenForReinitialise();
// 	resourceRequest.listenForMoveEndDate();
// 	resourceRequest.listenForMoveStartDate();
	resourceRequest.listenForDuplicateResource();
 	resourceRequest.listenForConfirmedDuplication();
// 	resourceRequest.listenForChangingHours();
 	resourceRequest.listenForSaveDiaryEntry();
 	resourceRequest.listenForSaveAdjustedHours();
 	resourceRequest.listenForSaveAdjustedHoursWithDelta();
// 	resourceRequest.listenForSaveStatusChange();
	resourceRequest.listenForResetReport();
	resourceRequest.listenForUnallocated();
	resourceRequest.listenForCompleteable();
	resourceRequest.listenForPlannedOnly();
	resourceRequest.listenForActiveOnly();
	resourceRequest.listenForRemovePassed();
	resourceRequest.listenForEditRecord();
	resourceRequest.listenForEndEarly();
	resourceRequest.endEarlyModalShown();
	resourceRequest.endEarlyModalHidden();
	resourceRequest.listenForSaveEndEarly();
	resourceRequest.listenForResourceRequestEditShown();
	resourceRequest.listenForDeleteRecord();
	resourceRequest.listenForConfirmedDelete();
	resourceRequest.listenForChangeStatusCompleted();
	resourceRequest.listenForChangePipelineLiveArchive();
	resourceRequest.listenForSelectSpecificRfs();
	resourceRequest.listenForSelectOrganisation();
	resourceRequest.listenForSelectBusinessUnit();
	resourceRequest.listenForBtnDiaryEntry();
	$('[data-toggle="tooltip"]').tooltip();

});

$(document).ready(function(){

	$(document).on('keyup mouseup','#ModalTOTAL_HOURS',function(e){
		$('.hrsForWeek').prop('disabled',true);
		$('#reinitialise').attr('disabled',false);
		$('#saveAdjustedHours').attr('disabled',true);
		$('#saveAdjustedHoursWithDelta').attr('disabled',true);

		$.each($('.hrsForWeek'),function(key, element){
			$(element).val('').attr('placeholder','Re-Initialise');
		});

		
	});

	

	$(document).on('keyup mouseup','.hrsForWeek',function(e){
		$('#ModalTOTAL_HOURS').prop('disabled',true);
		$('#reinitialise').attr('disabled',true);
		$('#saveAdjustedHours').attr('disabled',false);
		
		
		var originalTotalHours = $('#originalTotalHours').val();		
		var totalHours = 0;		
		
		$.each($('.hrsForWeek'),function(key, element){
			totalHours = (parseFloat(totalHours) + parseFloat(element.value)).toFixed(2);
		});
	
		$('#ModalTOTAL_HOURS').val(totalHours);

		$('#saveAdjustedHours').attr('data-original-title','').tooltip('show').tooltip('hide');



console.log( totalHours + ":" + originalTotalHours ); 
console.log( parseFloat(totalHours) < parseFloat(originalTotalHours) );
		
		if( parseFloat(totalHours) < parseFloat(originalTotalHours) ) {
			$('#saveAdjustedHoursWithDelta').attr('disabled',false); // they can only Auto-Delta if they've hours to save somewhere else.
		} else {
			$('#saveAdjustedHoursWithDelta').attr('disabled',true);
		}

// 		if(totalHours > originalTotalHours) {
// 			$('#saveAdjustedHours').attr('disabled',true);
// 			$('.total-hours-group').addClass('has-error').removeClass('has-success has-warning') ;
// 			var deltaHours = parseFloat(totalHours - originalTotalHours).toFixed(2);
// 			$('#saveAdjustedHours').attr('data-original-title','Total Hours cannot exceed original Total Hours:' + originalTotalHours + '. Remove ' + deltaHours + ' hours');
// 		} else if(totalHours == originalTotalHours){
// 			$('#saveAdjustedHours').attr('disabled',false);
// 			$('.total-hours-group').addClass('has-success').removeClass('has-error has-warning');	
// 			$('#saveAdjustedHours').attr('data-original-title','Will save the adjusted hours/week, without changing the Start or End Dates.');		
// 		} else {
// 			$('#saveAdjustedHours').attr('disabled',false);
// 			var deltaHours = parseFloat(originalTotalHours - totalHours).toFixed(2);
// 			$('.total-hours-group').addClass('has-warning').removeClass('has-success has-error');	
// 			$('#saveAdjustedHours').attr('data-original-title','Saving now, would reduce  Total Hours by ' + deltaHours + ' from ' + originalTotalHours);
// 		}

		
	});
});


$('#editRequestModal').on('shown.bs.modal', function (e) {
	$("#ORGANISATION").select2();
	$("#SERVICE").select2();
	})


$(document).on('select2:select', '#ORGANISATION',  function(e){
	var serviceSelected= $(e.params.data)[0].text;
	var entry = organisation[0].indexOf(serviceSelected);
	var data = organisation[entry+1];
	if ($('#SERVICE').hasClass("select2-hidden-accessible")) {
	    // Select2 has been initialized
	    $('#SERVICE').val("").trigger("change");
		$('#SERVICE').empty().select2('destroy').attr('disabled',true);
	}
	$("#SERVICE").select2({
		  data: data
	}).attr('disabled',false).val('').trigger('change');


	if(data.length==2){
		$("#SERVICE").val(data[1].text).trigger('change');
    }
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