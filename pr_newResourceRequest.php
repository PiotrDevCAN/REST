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

<script type='text/javascript'>
$(document).ready(function() {

	$(".select").select2({
		  tags: true,
		  createTag: function(params) {
        return undefined;
      }
	});

	$('#ORGANISATION').on('select2:select', function(e){
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
});

$("form").on("reset", function () {
	$(".select").val('').trigger('change');
	$("#STATUS").val('New').trigger('change');
});

$(document).ready(function(){
	pickers = initPickers();
});

function initPickers() {
	  var startDate;
    var endDate;
   	
    this.updateStartDate = function() {
        startPicker.setStartRange(startDate);
        endPicker.setStartRange(startDate);
        endPicker.setMinDate(startDate);
    };
    this.updateEndDate = function() {
        startPicker.setEndRange(endDate);
        startPicker.setMaxDate(endDate);
        endPicker.setEndRange(endDate);
    };
    this.startPicker = new Pikaday({
      firstDay:1,
      field: document.getElementById('InputSTART_DATE'),
      format: 'D MMM YYYY',
      showTime: false,
      minDate: new Date(),
      onSelect: function() {
            var db2Value = this.getMoment().format('YYYY-MM-DD')
            jQuery('#START_DATE').val(db2Value);
          startDate = this.getDate();
            updateStartDate();
      }
    });
    this.endPicker = new Pikaday({
    	firstDay:1,
      field: document.getElementById('InputEND_DATE'),
      format: 'D MMM YYYY',
      showTime: false,
      minDate: new Date(),
      onSelect: function() {
          var db2Value = this.getMoment().format('YYYY-MM-DD')
          jQuery('#END_DATE').val(db2Value);
          endDate = this.getDate();
          updateEndDate();
      }
    });

    var _startDate = this.startPicker.getDate();
    var _endDate = this.endPicker.getDate();

    if (this._startDate) {
        this.startDate = this._startDate;
        this.updateStartDate();
    };

    if (this._endDate) {
        this.endDate = this._endDate;
        this.updateEndDate();
    };
};

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
          // do the following before the save is started
			  },
        success: function(response) {
          // do what ever you want with the server response if that response is "success"
          // $('.modal-body').html(JSON.parse(response));
          var responseObj = JSON.parse(response);
          var resourceRef =  "<p><b>Resource Ref: " + responseObj.resourceReference + "</b></p>";
          var savedResponse =  "<p><b>Saved: " + responseObj.saveResponse +  "</b></p>";
          var hoursResponse =  "<p>" + responseObj.hoursResponse +  "</p>";
          var messages =  "<p><b>" + responseObj.messages +  "</b></p>";
          $('.modal-body').html(resourceRef + savedResponse + hoursResponse + messages);
          $('#myModal').modal('show');
          $('#myModal').on('hidden.bs.modal', function () {
            // do something…
            if(responseObj.create==true){
              // reset form
              $('#resetResourceRequest').click();
              $(':submit').removeClass('spinning').attr('disabled',false);
            } else {
              // there must be an issue so show message and summary
              window.close();
              $(':submit').removeClass('spinning').attr('disabled',false);
            }
          })
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

$('#RFS').on('select2:select', function(e){
	var rfsSelected= $(e.params.data)[0].text;
	var maxEndDate = null;

  $.ajax({
      url: "ajax/endDateForRfs.php",
      type: 'POST',
      data: { rfs:rfsSelected },	                
      success: function(result){
        var resultObj = JSON.parse(result);
        if(resultObj.rfsEndDate!==null){
          maxEndDate = new Date(resultObj.rfsEndDate);
          endPicker.setDate(maxEndDate);
          endPicker.setMaxDate(maxEndDate);
          startPicker.setMaxDate(maxEndDate);
        }	
      }
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