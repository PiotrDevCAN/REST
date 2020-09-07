<?php
use itdq\Trace;
use itdq\Loader;
use rest\allTables;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
$loader = new Loader();
$allRfs = $loader->load('RFS_ID',allTables::$RFS, " ARCHIVE is null ");
$allValueStream = $loader->load('VALUE_STREAM',allTables::$RFS, " ARCHIVE is null ");
$allBusinessUnits = $loader->load('BUSINESS_UNIT',allTables::$RFS, " ARCHIVE is null ");
$allRequestor = $loader->load('REQUESTOR_EMAIL',allTables::$RFS, " ARCHIVE is null ");

// $defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
// $canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;
?>
<div class='container-fluid'>
<h3>RFS Report</h3>

<div class='container'>

<form id='reportDates'>
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

<div id='rfsTableDiv'>
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
<script type='text/javascript'>


$(document).ready(function() {
	
	$(".select").select2();

    var rfs = new Rfs();
	rfs.buildRfsReport();
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
    console.log('edit rfs modal showing');
	var rfs = new Rfs();
	rfs.preventDuplicateRfsEntry();
	rfs.listenForSaveRfs();
	rfs.refreshReportOnRfsUpdate();
});

var startPickers = [];
var endPickers = [];


$(document).on('shown.bs.modal','#slipRfsModal',function(e){
    console.log('slip rfs modal showing');    
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

</script>

