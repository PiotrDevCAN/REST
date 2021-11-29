<?php
use itdq\Trace;
use rest\rfsTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container-fluid'>
<h3>Pipeline Report</h3>
<div id='rfsTableDiv'>
<?php
  rfsTable::buildHTMLPipelineTable();
?>
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

<script type='text/javascript'>
$(document).ready(function() {
	var rfs = new Rfs();
	rfs.buildPipelineReport(false);
	rfs.listenForDeleteRfs();
	rfs.listenForGoLiveRfs();
	rfs.listenForConfirmDeleteRfs();
	rfs.listenForEditRfs();
	rfs.listenForArchiveRfs();
	rfs.listenForConfirmArchiveRfs();
});

$(document).on('shown.bs.modal',function(e){
	var rfs = new Rfs();
	rfs.preventDuplicateRfsEntry();
	rfs.listenForSaveRfs();
	rfs.refreshReportOnRfsUpdate();
});



</script>