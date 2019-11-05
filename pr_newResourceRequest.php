<?php
use itdq\Trace;
use rest\resourceRequestRecord;
use itdq\FormClass;
use rest\resourceRequestTable;
use rest\allTables;
use itdq\DateClass;

set_time_limit(0);
Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<div class='container'>
<h2>Resource Request Form</h2>
<?php
if(isset($_REQUEST['resource'])){
    $mode = FormClass::$modeEDIT;
    $resourceTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceRecord = new resourceRequestRecord();
    $resourceRecord->set('RESOURCE_REFERENCE', $_REQUEST['resource']);
    $resourceData = $resourceTable->getRecord($resourceRecord);
    $resourceRecord->setFromArray($resourceData);
} else {
    $resourceRecord = new resourceRequestRecord();
    $mode = FormClass::$modeDEFINE;
}

$resourceRecord->displayForm($mode);
?>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Resource Request Save Result</h4>
      </div>
      <div class="modal-body" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<script>
$(document).ready(function() {
	$(".select").select2();

	$('#CTB_SERVICE').on('select2:select', function(e){
		var serviceSelected= $(e.params.data)[0].text;
    	var entry = ctbService[0].indexOf(serviceSelected);
    	var data = ctbService[entry];

    	if ($('#CTB_SUB_SERVICE').hasClass("select2-hidden-accessible")) {
    	    // Select2 has been initialized
    	    $('#CTB_SUB_SERVICE').val("").trigger("change");
    		$('#CTB_SUB_SERVICE').empty().select2('destroy').attr('disabled',true);
    	}
    	$("#CTB_SUB_SERVICE").select2({
    		  data: data
    	}).attr('disabled',false).val('').trigger('change');


    	if(data.length==2){
    		$("#CTB_SUB_SERVICE").val(data[1].text).trigger('change');
        }

	});





});


$("form").on("reset", function () {
	$(".select").val('').trigger('change');
	console.log($('.select'));

});


$(document).ready(function(){

	var startDate,
    	endDate,
    updateStartDate = function() {
		console.log('updatedStartDate');
        startPicker.setStartRange(startDate);
        endPicker.setStartRange(startDate);
        endPicker.setMinDate(startDate);
    },
    updateEndDate = function() {
		console.log('updatedEndDate');
        startPicker.setEndRange(endDate);
        startPicker.setMaxDate(endDate);
        endPicker.setEndRange(endDate);
    },
    startPicker = new Pikaday({
    	firstDay:1,
// 		disableDayFn: function(date){
// 		    // Disable weekend
// 		    return date.getDay() === 0 || date.getDay() === 6;
// 		},
        field: document.getElementById('InputSTART_DATE'),
        format: 'D MMM YYYY',
        showTime: false,
        onSelect: function() {
            console.log(this.getMoment().format('Do MMMM YYYY'));
            var db2Value = this.getMoment().format('YYYY-MM-DD')
            console.log(db2Value);
            jQuery('#START_DATE').val(db2Value);
            startDate = this.getDate();
            console.log(startDate);
            updateStartDate();
        }
    }),
    endPicker = new Pikaday({
    	firstDay:1,
// 		disableDayFn: function(date){
// 		    // Disable weekend
// 		    return date.getDay() === 0 || date.getDay() === 6;
// 		},
        field: document.getElementById('InputEND_DATE'),
        format: 'D MMM YYYY',
        showTime: false,
        onSelect: function() {
            console.log(this.getMoment().format('Do MMMM YYYY'));
            var db2Value = this.getMoment().format('YYYY-MM-DD')
            console.log(db2Value);
            jQuery('#END_DATE').val(db2Value);
            endDate = this.getDate();
            updateEndDate();
        }
    }),
    _startDate = startPicker.getDate(),
    _endDate = endPicker.getDate();

    if (_startDate) {
        startDate = _startDate;
        updateStartDate();
    }

    if (_endDate) {
        endDate = _endDate;
        updateEndDate();
    }
});

// $(document).ready(function() {
//     var text_max = 1500;
//     $('#textarea_feedbackadditional_comments').html(text_max + ' characters remaining');

//     $('#additional_comments').keyup(function() {
//         var text_length = $('#additional_comments').val().length;
//         var text_remaining = text_max - text_length;

//         $('#textarea_feedbackadditional_comments').html(text_remaining + ' characters remaining');
//         if(text_remaining<=0){
//             $("#additional_commentsFormGroup").addClass("has-error");
//             $("#textarea_feedbackadditional_comments").addClass("textarea-full");
//         } else {
//             $("#additional_commentsFormGroup").removeClass("has-error");
//             $("#textarea_feedbackadditional_comments").removeClass("textarea-full");
//         }
//     });
// });


$(document).ready(function(){

	$( "#resourceRequestForm" ).submit(function( event ) {
		$(':submit').addClass('spinning').attr('disabled',true);
		var url = 'ajax/saveResourceRecord.php';
		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#resourceRequestForm").serialize();
		$(disabledFields).attr('disabled',true);
		jQuery.ajax({
			type:'post',
		  	url: url,
		  	data:formData,
		  	context: document.body,
 	      	beforeSend: function(data) {
	        	//	do the following before the save is started
	        	},
	      	success: function(response) {
	            // 	do what ever you want with the server response if that response is "success"
	            	console.log(response);
	            	console.log(JSON.parse(response));
	               // $('.modal-body').html(JSON.parse(response));
	               var responseObj = JSON.parse(response);
	               var resourceRef =  "<p>Resource Ref:" + responseObj.resourceReference + "</p>";
	               var savedResponse =  "<p>Saved:" + responseObj.saveResponse +  "</p>";
	               var hoursResponse =  "<p>" + responseObj.hoursResponse +  "</p>";
	               var messages =  "<p>" + responseObj.Messages +  "</p>";
	                $('.modal-body').html(resourceRef + savedResponse + hoursResponse + messages);
	                $('#myModal').modal('show');
	                $('#myModal').on('hidden.bs.modal', function () {
	                	  // do something…
		                if(responseObj.Update==true){
	    	            	window.close();
	        	        } else {
	            	    	$('#resetResourceRequest').click();
	            	    	$(':submit').removeClass('spinning').attr('disabled',false);
	                	}
              	})
          		},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
// 					FormClass.displayAjaxError('<p>ActionPlanRecord.initialiseForm</p><p>Ajax call has failed.<br/>HTTP Code ' + xmlDoc.status + '<br/>HTTP Text:' +xmlDoc.statusText + '<br/>Response:' + xmlDoc.responseText + '</p>');
// 					console.log(xmlDoc.responseXML);
// 					console.log(xmlDoc.responseText);
// 					jQuery('.slaSave').prop('disable',true );
	                $('.modal-body').html("<h2>Json call to save record Failed.Tell Rob</h2>");
	                $('#myModal').modal('show');
				},
	      	error: function(error){
	            //	handle errors here. What errors	            :-)!
	        		console.log('Ajax error' );
	        		console.log(error.statusText);
	        		FormClass.displayAjaxError('<p>Ajax call has errored.</p><p>URL:"' + url + '"</p><p>Error Status:"' + error.statusText + '"</p>');
	        		jQuery('.slaSave').html('Save').prop('disable',true );
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');

	      	}
		});
	event.preventDefault();
	});
});


</script>
<style>

<?php
$date = new DateTime();
$currentYear = $date->format('Y');

for($year=$currentYear-1;$year<=$currentYear+1;$year++){
    for($month=1;$month<=12;$month++){
        $date = '01-' . substr('00' . $month,2) . "-" . $year;
        $claimCutoff = DateClass::claimMonth($date);
         ?>[data-pika-year="<?=$year;?>"][data-pika-month="<?=$month-1;?>"][data-pika-day="<?=$claimCutoff->format('d');?>"] {background-color: white; color:red; outline:solid; outline-color:grey;outline-width:thin; content='claim'}<?php
    }
}
?>
</style>



<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);