<?php
use itdq\Trace;
use itdq\FormClass;
use rest\rfsRecord;
use rest\rfsTable;
use rest\allTables;
set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
rfsTable::loadKnownRfsToJs();
?>
<div class='container'>
<h2>RFS Definition Form</h2>
<?php

if(isset($_REQUEST['rfs'])){
    $mode = FormClass::$modeEDIT;
    $rfsTable = new rfsTable(allTables::$RFS);
    $rfsRecord = new rfsRecord();
    $rfsRecord->set('RFS_ID', $_REQUEST['rfs']);
    $rfsData = $rfsTable->getRecord($rfsRecord);
    $rfsRecord->setFromArray($rfsData);

} else {
    $rfsRecord = new rfsRecord();
    $mode = FormClass::$modeDEFINE;
}
$rfsRecord->displayForm($mode);
?>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">RFS Save Result</h4>
      </div>
      <div class="modal-body" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script type='text/javascript'>
$(document).ready(function() {
	$('#LINK_TO_PGMP').attr('required',false);
});


$("form").on("reset", function () {
	$(".select").val('').trigger('change');
});


$('#RFS_ID').on('focusout',function(e){
	var newRfsId = $(this).val().trim();
	var allreadyExists = ($.inArray(newRfsId, knownRfs) >= 0 );
	if(allreadyExists){ // comes back with Position in array(true) or false is it's NOT in the array.
		$('#saveRfs').attr('disabled',true);
		$(this).css("background-color","LightPink");
		alert('RFS already defined');
	} else {
		var selectOptionVal = '';	
		var valueStreamObj = $('#VALUE_STREAM');
		var rfsId = $('#RFS_ID').val().trim().toUpperCase().substr(0,4);
		
		$('#VALUE_STREAM > option').each(function() {			
			if ( rfsId == $(this).text().substr(0,4)){
				if(selectOptionVal==''){
					selectOptionVal = $(this).val(); // we've found a match, lets save it and check it's unique.
				} else {
					selectOptionVal = '';  // We found a 2nd match, so can't pre-select
					return false;
				}
			};
		});
		
		if(selectOptionVal!=''){
			$('#VALUE_STREAM').val(selectOptionVal).trigger('change');
		}	
			
		$(this).css("background-color","LightGreen");
		$('#saveRfs').attr('disabled',false);
	};
});



$(document).ready(function(){

	$('#VALUE_STREAM').select2();

	$( "#rfsForm" ).submit(function( event ) {
		$(':submit').addClass('spinning').attr('disabled',true);
		var url = 'ajax/saveRfsRecord.php';
		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#rfsForm").serialize();
		$(disabledFields).attr('disabled',true);
		jQuery.ajax({
			type:'post',
		  	url: url,
		  	data:formData,
		  	context: document.body,
 	      	beforeSend: function(data) {
				// do the following before the save is started
			},
	      	success: function(response) {
	            // do what ever you want with the server response if that response is "success"
				// $('.modal-body').html(JSON.parse(response));
				var responseObj = JSON.parse(response);
				var rfsIdTxt =  "<p><b>RFS ID:</b>" + responseObj.rfsId + "</p>";
				var savedResponse =  responseObj.saveResponse;
				if(savedResponse){
					var scan = "<scan>";
				} else {
					var scan = "<scan style='color:red'>";
				}
				var savedResponseTxt =  "<p>" + scan + " <b>Record Saved:</b>" + savedResponse +  "</scan></p>";
				if(responseObj.messages != null){
					var messages =  "<p>" + responseObj.messages +  "</p>";
				}
				var messages =  "<p>" + responseObj.messages +  "</p>";
				$('.modal-body').html(rfsIdTxt + savedResponseTxt + messages);
				$('#myModal').modal('show');
				$('#myModal').on('hidden.bs.modal', function () {
					// do something…
					if(responseObj.update==true){
						window.close();
					} else {
							$('#resetRfs').click();
							$(':submit').removeClass('spinning').attr('disabled',false);
					}
				})
				knownRfs.push(responseObj.rfsId);
				$('#RFS_ID').css("background-color","#ffffff");
          	},
	      	fail: function(response){
				$('.modal-body').html("<h2>Json call to save record Failed.Tell Rob</h2>");
				$('#myModal').modal('show');
			},
	      	error: function(error){
	            //	handle errors here. What errors	            :-)!
				$('.modal-body').html("<h2>Json call to save record Errored " + error.statusText + " Tell Rob</h2>");
				$('#myModal').modal('show');
			},
	      	always: function(){
	      	}
		});
	event.preventDefault();
	});
});


$(document).ready(function(){
	$('#REQUESTOR_EMAIL').keyup(function(){
		var regex = RegExp('ibm.com$');
		var email = $('#REQUESTOR_EMAIL').val().trim().toLowerCase();
		var ibmEmailAddress = regex.test(email);
		ibmEmailAddress ? $("input[name='Submit']").attr('disabled',false) : $('input[name="Submit"]').attr('disabled',true);
		ibmEmailAddress ? $("#REQUESTOR_EMAIL").css('color','DARKGREEN') : $('#REQUESTOR_EMAIL').css('color','CRIMSON');
		});
});



</script>



<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);