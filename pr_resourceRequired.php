<?php
use itdq\Trace;
use rest\allTables;
use rest\resourceRequestRecord;
use itdq\Loader;

set_time_limit(0);
do_auth($_SESSION['userBg']);

Trace::pageOpening($_SERVER['PHP_SELF']);

$loader = new Loader();

$allPlatform = $loader->load('CTB_SERVICE',allTables::$STATIC_CTB_SERVICE);
$allResourceType = $loader->load('RESOURCE_TYPE',allTables::$STATIC_RESOURCE_TYPE);
unset($allPlatform[resourceRequestRecord::$bulkWorkOrder]);
unset($allResourceType[resourceRequestRecord::$bulkWorkOrder]);
?>
<div class='container'>


<h3>Select Dates for Report</h3>
<form id='reportDates'>
	<div class='form-group' >
       <div id='START_DATE" . "FormGroup' >
       <label for='START_DATE' class='col-md-1 control-label ' data-toggle='tooltip' data-placement='top' title=''>From</label>
       <div class='col-md-2'>
       <div id='calendarFormGroupSTART_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='START_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
       <input id='InputSTART_DATE' class='form-control' type='text' readonly value='' placeholder='Select From' required />
       <input type='hidden' id='START_DATE' name='START_DATE' value='' />
       <span class='input-group-addon'><span id='calendarIconSTART_DATE' class='glyphicon glyphicon-calendar'></span></span>
       </div>
       </div>
       </div>

       <div id='END_DATE" . "FormGroup'>
       <label for='END_DATE' class='col-md-1 control-label ' data-toggle='tooltip' data-placement='top' title=''>To</label>
       <div class='col-md-2'>
       <div id='calendarFormGroupEND_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='END_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
       <input id='InputEND_DATE' class='form-control' type='text' readonly value='' placeholder='Select To' required />
       <input type='hidden' id='END_DATE' name='END_DATE' value='' />
       <span class='input-group-addon'><span id='calendarIconEND_DATE' class='glyphicon glyphicon-calendar'></span></span>
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
      <div class="modal-body">
      	<div class="container col-md-12">
        <form id='resourceNameForm'>
  		<div class="form-group required" id="PROJECT_TITLEFormGroup" >
  		<div class='row'>
            <label for="RESOURCE_NAME" class="col-md-3	control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Resource Name</label>
              <div class="col-md-9">
                  <input class="form-control " id="RESOURCE_NAME" name="RESOURCE_NAME" value="" placeholder="Enter Resource Name" required="required" type="text">
                  <input type='hidden' id="RESOURCE_REFERENCE" name="RESOURCE_REFERENCE" value="" >
                  <input type='hidden' id="parent" name="parent" value="" >
              </div>
        </div>
        </div>
      </form>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='saveResourceName'>Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


 <!-- Modal -->
<div id="PlatformTypePrnCodeModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Define Platform etc.</h4>
      </div>
      <div class="modal-body">
      	<div class="container col-md-12">
        <form id='platformTypePrnCodeForm'>
        <div class='form-group required' id='PlatformFormGroup'>
        <input type='hidden' id="ptpcRESOURCE_REFERENCE" name="ptpcRESOURCE_REFERENCE" value="" >
        <div class='row'>
	       	<label for='CTB_SERVICE' class='col-md-3 control-label ceta-label-left'>Current Platform</label>
    	       	<div class='col-md-9'>
                <select class='form-control select'
                		id='CTB_SERVICE'
                        name='CTB_SERVICE'
                        data-tags="true" data-placeholder="Select current platform" data-allow-clear="true">
                <option value=''>Select Current Platform<option>
                <?php
                    foreach ($allPlatform as $key => $value) {
                        $displayValue = trim($value);
                        $returnValue  = trim($value);
                ?>
                <option value='<?=$returnValue?>'><?=$displayValue?></option>
                <?php }?>
                </select>
                </div>
          </div>
          </div>
          <div class='form-group required' id='ResourceTypeFormGroup'>
		  <div class='row'>
          <label for='RESOURCE_TYPE' class='col-md-3 control-label ceta-label-left'>Resource Type</label>
               <div class='col-md-9'>
               <select class='form-control select' id='RESOURCE_TYPE'
                       name='RESOURCE_TYPE'
                       required='required'
                       data-tags="true" data-placeholder="Select Resource Type" data-allow-clear="true">
              <option value=''>Select Resource Type<option>
              <?php
              foreach ($allResourceType as $key => $value) {
                  $displayValue = trim($value);
                  $returnValue  = trim($value);
                  ?><option value='<?=$returnValue?>'><?=$displayValue?></option>
              <?php } ?>
              </select>
              </div>
        </div>
        </div>
        <div class='form-group required' id='PrnProjectCodeGroup'>
		  <div class='row'>
            <label for="PRN" class="col-md-3 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Drawn Down for PRN</label>
            <div class="col-md-3">
            <input class="form-control" id="PRN" name="PRN" value="" placeholder="PRN" type="text">
            </div>
            <label for="PROJECT_CODE" class="col-md-3	 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Drawn down for Project Code</label>
            <div class="col-md-3">
            <input class="form-control " id="PROJECT_CODE" name="PROJECT_CODE" value="" placeholder="Enter Code" required="required" type="text">
            </div>
         </div>
         </div>
        </form>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='savePlatformTypePrnCode'>Save</button>
      </div>
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
      <p><b>Resource Type :</b><span id='confirmDuplicateType'></span></p>
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
      <div class="modal-body" id='errorMessageBody'>
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
		    <label for="statusChangePlatform" class='col-sm-2' >Platform</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangePlatform" name="statusChangePlatform" disabled>
	    	</div>
  		</div>
		<div class="form-group">
		    <label for="statusChangeStart" class='col-sm-2' >Start</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangeStart" name="statusChangeStart" disabled>
	    	</div>
  		</div>
  		<div class="form-group">
		    <label for="statusChangeType" class='col-sm-2' >Type</label>
		    <div class='col-sm-8'>
	    	<input type="text" class="form-control" id="statusChangeType" name="statusChangeType" disabled>
	    	</div>
  		</div>

  		<div class="form-group">
  		<label class='col-sm-2'>Status</label>

  		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_ASSIGNED ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_ASSIGNED)?>' ><?=resourceRequestRecord::STATUS_ASSIGNED ?></label>
		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_COMPLETED ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_COMPLETED)?>'><?=resourceRequestRecord::STATUS_COMPLETED ?></label>
		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_NEW ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_NEW)?>'><?=resourceRequestRecord::STATUS_NEW ?></label>
		</div>

		<div class="form-group">
		 <label class='col-sm-2'></label>
		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_PLATFORM ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_PLATFORM)?>'><?=resourceRequestRecord::STATUS_PLATFORM ?></label>
		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_REDIRECTED ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_REDIRECTED)?>'><?=resourceRequestRecord::STATUS_REDIRECTED ?></label>
		 <label class="radio-inline col-sm-3"><input type="radio" name="statusRadio" value='<?=resourceRequestRecord::STATUS_REQUESTOR ?>' id='statusRadio<?=str_replace(' ', '_', resourceRequestRecord::STATUS_REQUESTOR)?>'><?=resourceRequestRecord::STATUS_REQUESTOR ?></label>
		 </div>
        </form>

	</div>
    <div class="modal-footer" >
       	<button type="button" class="btn btn-primary" id='saveStatusChange'>Save</button>
    </div>
    </div>
  </div>
</div>




<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<script>
$(document).ready(function() {
	console.log('setup all the listeners');
	var resourceRequest = new ResourceRequest();
	resourceRequest.initialiseDateSelect();
	resourceRequest.buildResourceReport();
	resourceRequest.listenForAddPlatformTypePrnCode();
	resourceRequest.listenForSavePlatformTypePrnCode();
	resourceRequest.listenForEditResourceName();
	resourceRequest.listenForSaveResourceName();
	resourceRequest.listenForEditHours();
	resourceRequest.listenForSlipStartDate();
	resourceRequest.listenForReinitialise();
	resourceRequest.listenForMoveEndDate();
	resourceRequest.listenForDuplicateResource();
 	resourceRequest.listenForConfirmedDuplication();
 	resourceRequest.listenForSaveAdjustedHours();
 	resourceRequest.listenForSaveAdjustedHoursWithDelta();
 	resourceRequest.listenForSaveAdjustedHoursWithDrawDown();
 	resourceRequest.listenForSaveStatusChange();
	resourceRequest.listenForResetReport();
	resourceRequest.listenForDdDetails();
	resourceRequest.listenForSeekBwo();
	resourceRequest.listenForEditRecord();
	resourceRequest.listenForDeleteRecord();
	resourceRequest.listenForConfirmedDelete();
	resourceRequest.listenForChangeStatus();

	$('[data-toggle="tooltip"]').tooltip();
});

</script>