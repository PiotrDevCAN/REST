<?php
use itdq\Trace;
use itdq\TraceList;
use itdq\AllItdqTables;
use itdq\TraceControlRecord;
set_time_limit(0);

include_once 'connect.php';

do_auth($_SESSION['adminBg']);

Trace::pageOpening($_SERVER['PHP_SELF']);
?>

<div class='container'>

<h4>Instructions</h4>
<p>Drag supported table into the DropZone below, and wait. The upload is a two stage process, the first stage copies the file up onto the server.
The progress bar reflects this. The second stage is to read the file and copy the records into the database. This takes a lot longer, <p><b>Typically several minutes</b> and there is no Progress Bar. When it has completed
a Modal will appear to inform you.</p>
<p> Do not navigate aways from this screen until that screen appears</p>


<form action="ajax/upload.php"
      class="dropzone"
      id="my-awesome-dropzone">
</form>

<button id='requestOverwriteBaseline' class='btn btn-danger'>Overwrite Baseline</button>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">REST Table Update Completed</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="modalConfirmOverwrite" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Overwrite Confirmation</h4>
      </div>
      <div class="modal-body">
        <p class='bg-danger'>Please confirm you wish to proceed with an overwrite of the current Inflights Baseline Data from the current Inflights Project Data.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="confirmOverwriteBaseline">Confirm</button>
        <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>


<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);

?>
<script type="text/javascript">
Dropzone.options.myAwesomeDropzone = {
	  paramName: "uploadingFile", // The name that will be used to transfer the file
	  maxFilesize: 28, // MB
	  timeout:240000, // 240 seconds timeout
	  accept: function(file, done) {
	    if (file.name == "Inflight Projects") {
	      done("Unsupported Filename");
	    }
	    else { done(); }
	  },
	  init: function() {
      this.on("success", function (file,response) {
    	  $('#myModal .modal-body').html(JSON.parse(response));
    	  $('#myModal').modal('show');

      	});
	  }
};

$(document).on('click','#confirmOverwriteBaseline', function(e){
    $.ajax({
    	url: "ajax/overwriteBaseline.php",
        type: 'POST',
    	success: function(result){
    		$('#modalConfirmOverwrite').modal('hide')
    		}
    });
});

$(document).on('click','#requestOverwriteBaseline', function(e){
	$('#modalConfirmOverwrite').modal('show');
});



</script>
