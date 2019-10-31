<?php
use itdq\Trace;
use rest\StaticOperatingCompanyRecord;
use itdq\FormClass;
use rest\StaticCountryMarketRecord;
use rest\StaticCountryMarketTable;
use rest\StaticCtbServiceRecord;
use rest\StaticCtbServiceTable;

?>
<div class='container'>
<h2>Define Country</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$ctbServiceRecord = new StaticCtbServiceRecord();
$ctbServiceRecord->displayForm(itdq\FormClass::$modeDEFINE);

?>

<div class='container'>
<h2>Manage CTB Services</h2>

<div style='width: 75%'>
<table id='ctbServiceTable' >
<thead>
<tr><th>Service</th><th>Sub Service</th><th>Status</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Service</th><th>Sub Service</th><th>Status</th></tr>
</tfoot>
</table>
</div>
</div>

<!-- Modal -->
<div id="saveResultModal" class="modal fade" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">Service Save Result</h4>
</div>
<div class="modal-body" >
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>

</div>
</div>
</div>

<script>

var ctbServiceTable;

function listenForSaveCtbService(){
	$(document).on('click','#saveService', function(e){
		e.preventDefault();
		$('#saveService').addClass('spinning').attr('disabled',true);
		console.log($('#saveService'));
		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $('#ctbServiceForm').serialize();
		$(disabledFields).attr('disabled',true);
		console.log(formData);
	    $.ajax({
	    	url: "ajax/saveCtbService.php",
	        type: 'POST',
	    	data: formData,
	    	success: function(result){
	    		var resultObj = JSON.parse(result);
	    		var success   = resultObj.success;
	    		var messages  = resultObj.messages;
	    		console.log(resultObj);
	    		if(!success){
		    		console.log(messages);
		    		$('#saveResultModal .modal-body').html(messages);
		    		$('#saveResultModal').modal('show');
	    		} else {
	    			$('#saveResultModal .modal-body').html('Save successful');
		    		$('#saveResultModal').modal('show');
	    		}
	    		$('#CTB_SERVICE').val('');
	    		$('#CTB_SUB_SERVICE').val('');
	    		$('#statusRadioDisabled').prop('checked',false);
	    		$('#statusRadioEnabled').prop('checked',true);
         		$('.spinning').removeClass('spinning').attr('disabled',false);
         		ctbServiceTable.ajax.reload();
	    	}
	    });
	});
}

function listenForToggleStatus(){
	$(document).on('change','input.toggle',function(e) {
		var status = $(this).data('status');
		var ctbService = $(this).data('ctbservice');
		var ctbSubService = $(this).data('ctbsubservice');
		$.ajax({
			url: "ajax/updateCtbServiceStatus.php",
		    type: 'POST',
		    data: {currentStatus:status,
		    	   CTB_SERVICE:ctbService,
		    	   CTB_SUB_SERVICE:ctbSubService},
		    success: function(result){
		    	var resultObj = JSON.parse(result);
		    	var success   = resultObj.success;
		    	var messages  = resultObj.messages;
		    	console.log(resultObj);
		    	if(!success){
					console.log(messages);
			    	$('#saveResultModal .modal-body').html(messages);
			    	$('#saveResultModal').modal('show');
			    	operatingCompaniesTable.ajax.reload();
		    	} else {
			    	$('#saveResultModal .modal-body').html('Status Update Successful');
					$('#saveResultModal').modal('show');
		    	}
		    }
		});
	});
}

function listenForResetForm(){
	$(document).on('click','#resetCtbService',function(){
		$("input[name=statusRadio][value=<?=StaticCtbServiceTable::ENABLED;?>]").prop('checked', true)
		$("input[name=statusRadio]").attr('disabled',false);
		$('#CTB_SERVICE').val('');
		$('#CTB_SUB_SERVICE').val('');
		$('#saveCtbService').val('Submit');
		$('#mode').val('<?=FormClass::$modeDEFINE;?>');
	});
}

var buttonCommon = {
	exportOptions: {
    	format: {
        	body: function ( data, row, column, node ) {
            	  return data ? data.replace( /<br\s*\/?>/ig, "\n").replace(/(&nbsp;|<([^>]+)>)/ig, "") : data ;
        	}
    	}
	}
}


function initialiseTable(){
	countryMarketTable = $('#ctbServiceTable').DataTable({
		autoWidth: false,
		processing: true,
		responsive: false,
		dom: 'Blfrtip',
    	buttons: [
            'colvis',
            $.extend( true, {}, buttonCommon, {
                extend: 'excelHtml5',
                exportOptions: {
                    orthogonal: 'sort',
                    stripHtml: true,
                    stripNewLines:false
                },
                 customize: function( xlsx ) {
                     var sheet = xlsx.xl.worksheets['sheet1.xml'];
                 }
        }),
        $.extend( true, {}, buttonCommon, {
            extend: 'csvHtml5',
            exportOptions: {
                orthogonal: 'sort',
                stripHtml: true,
                stripNewLines:false
            }
        }),
        $.extend( true, {}, buttonCommon, {
            extend: 'print',
            exportOptions: {
                orthogonal: 'sort',
                stripHtml: true,
                stripNewLines:false
            }
        })
          ],
        ajax: {
        	    "url":"/ajax/populateCtbServiceTable.php",
        	    "type": "GET",
        },
        drawCallback: function( row, data ) {
        	$("[data-toggle='toggle']").bootstrapToggle('destroy')
        	$("[data-toggle='toggle']").bootstrapToggle({
      	      on: 'Enabled',
    	      off: 'Disabled'
      	});
        },
        columns: [
            { data: "CTB_SERVICE" , "defaultContent": "" },
            { data: "CTB_SUB_SERVICE","defaultContent": "" },
            { data: "STATUS",
           	  render: { _:'display', sort:'sort' },
            }
            ]
	});
}


$(document).ready(function(){
	listenForResetForm();
	listenForToggleStatus();
	listenForSaveCtbService();
	initialiseTable();
});
</script>



<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);