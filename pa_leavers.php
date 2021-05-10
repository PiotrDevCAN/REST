<?php
use itdq\Trace;

Trace::pageOpening($_SERVER['PHP_SELF']);

?>

<div class='container'>
<h2>List of IBM Leavers</h2>

<div style='width: 75%'>
<table id='leaverTable' >
<thead>
<tr><th>Email Address</th><th>Notes ID</th><th>PES Status</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Email Address</th><th>Notes ID</th><th>PES Status</th></tr>
</tfoot>
</table>
</div>
</div>

<script>

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
	countryMarketTable = $('#leaverTable').DataTable({
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
        	    "url":"/ajax/populateLeaversTable.php",
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
			{ data: "EMAIL_ADDRESS","defaultContent": "" },
            { data: "NOTES_ID","defaultContent": "" },
            { data: "PES_STATUS","defaultContent": "" }
            ]
	});
}

$(document).ready(function(){
	initialiseTable();
});
</script>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);