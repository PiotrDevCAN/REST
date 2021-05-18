<?php
use itdq\Trace;
use itdq\FormClass;
use rest\staticOrganisationRecord;
use rest\staticOrganisationTable;

?>
<div class='container'>
<h2>Define Organisation/Service</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$organisationRecord = new staticOrganisationRecord();
$organisationRecord->displayForm(itdq\FormClass::$modeDEFINE);

?>

<div class='container'>
<h2>Manage Organisation</h2>

<div style='width: 75%'>
<table id='organisationTable' >
<thead>
<tr><th>Organisation</th><th>Service</th><th>Status</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Organisation</th><th>Service</th><th>Status</th></tr>
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
<h4 class="modal-title">Organistion Save Result</h4>
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

var organisationTable;

function listenForSaveOrganisation(){
	$(document).on('click','#saveService', function(e){
		e.preventDefault();
		$('#saveService').addClass('spinning').attr('disabled',true);
		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $('#organisationForm').serialize();
		$(disabledFields).attr('disabled',true);
	    $.ajax({
	    	url: "ajax/saveOrganisation.php",
	        type: 'POST',
	    	data: formData,
	    	success: function(result){
	    		var resultObj = JSON.parse(result);
	    		var success   = resultObj.success;
	    		var messages  = resultObj.messages;
	    		if(!success){
		    		$('#saveResultModal .modal-body').html(messages);
		    		$('#saveResultModal').modal('show');
	    		} else {
	    			$('#saveResultModal .modal-body').html('Save successful');
		    		$('#saveResultModal').modal('show');
	    		}
	    		$('#ORGANISATION').val('');
	    		$('#SERVICE').val('');
	    		$('#statusRadioDisabled').prop('checked',false);
	    		$('#statusRadioEnabled').prop('checked',true);
         		$('.spinning').removeClass('spinning').attr('disabled',false);
         		organisationTable.ajax.reload();
	    	}
	    });
	});
}

function listenForToggleStatus(){
	$(document).on('change','input.toggle',function(e) {
		var status = $(this).data('status');
		var organisation = $(this).data('organisation');
		var service = $(this).data('service');
		$.ajax({
			url: "ajax/updateOrganisationStatus.php",
		    type: 'POST',
		    data: {currentStatus:status,
		    	ORGANISATION:organisation,
		    	     SERVICE:service},
		    success: function(result){
		    	var resultObj = JSON.parse(result);
		    	var success   = resultObj.success;
		    	var messages  = resultObj.messages;
		    	if(!success){
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
	$(document).on('click','#resetOrganisation',function(){
		$("input[name=statusRadio][value=<?=staticOrganisationTable::ENABLED;?>]").prop('checked', true)
		$("input[name=statusRadio]").attr('disabled',false);
		$('#ORGANISATION').val('');
		$('#SERVICE').val('');
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
	countryMarketTable = $('#organisationTable').DataTable({
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
        	    "url":"/ajax/populateOrganisationTable.php",
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
            { data: "ORGANISATION" , "defaultContent": "" },
            { data: "SERVICE","defaultContent": "" },
            { data: "STATUS",
           	  render: { _:'display', sort:'sort' },
            }
            ]
	});
}


$(document).ready(function(){
	listenForResetForm();
	listenForToggleStatus();
	listenForSaveOrganisation();
	initialiseTable();
});
</script>



<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);