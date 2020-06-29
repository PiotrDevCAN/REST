<?php
use itdq\Trace;
use itdq\Loader;
use rest\allTables;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
$loader = new Loader();


$allRfs = $loader->load('RFS',allTables::$RESOURCE_REQUESTS);
$allCio = $loader->load('CIO',allTables::$RFS);
$allRequestor = $loader->load('REQUESTOR_EMAIL',allTables::$RFS);

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
         <label for='selectCio' class='col-md-1 control-label text-right'>CIO</label>
        	<div class='col-md-2 text-left'>
              	<select class='form-control select' id='selectCio'
                  	          name='selectCio'
                  	          data-placeholder="Select Cio" data-allow-clear="true"
                  	          >
            	<option value=''>Select CIO</option>
            	<option value='All'>All</option>
                <?php
                    foreach ($allCio as $value) {
                         $displayValue = trim($value);
                         $returnValue  = trim($value);
                         $selectedCio = isset($_COOKIE['selectedCio']) ? $_COOKIE['selectedCio'] : null;
                         $selected = $returnValue==$selectedCio ? 'selected' : null;
                         ?><option value='<?=$returnValue?>' <?=$selected;?> ><?=$displayValue?></option><?php
                    }
                ?>
               </select>
            </div>
         <label for='selectRequestor' class='col-md-1 control-label text-right'>Requestor</label>
        	<div class='col-md-3 text-left'>
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

$("[data-toggle='toggle']").bootstrapToggle('destroy')
$(".pipelineLive").bootstrapToggle({
    on: 'Live',
  off: 'Pipeline'
});


$(document).ready(function() {
	var rfs = new Rfs();
	rfs.buildRfsReport();
	rfs.listenForDeleteRfs();
	rfs.listenForConfirmDeleteRfs();
	rfs.listenForEditRfs();
	rfs.listenForArchiveRfs();
	rfs.listenForConfirmArchiveRfs();
	rfs.listenForSelectRequestor();
	rfs.listenForSelectCio();
	rfs.listenForSelectRfs();
});


$(document).on('shown.bs.modal',function(e){
	var rfs = new Rfs();
	rfs.preventDuplicateRfsEntry();
	rfs.listenForSaveRfs();
	rfs.refreshReportOnRfsUpdate();
});



</script>