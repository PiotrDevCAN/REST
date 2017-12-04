<?php
use itdq\Trace;
use rest\inflightProjectsTable;
use rest\allTables;
use rest\inflightProjectsRecord;
use rest\uploadLogRecord;
use rest\uploadLogTable;
use itdq\AllItdqTables;

set_time_limit(0);

include_once 'connect.php';

//do_auth($_SESSION['userBg']);

Trace::pageOpening($_SERVER['PHP_SELF']);

$uploadLogTable = new uploadLogTable(allTables::$UPLOAD_LOG);
$detailsOfLastLoad = $uploadLogTable->wasLastLoadSuccesssful();

if($detailsOfLastLoad){
    $lastCompletedLogRecord = $detailsOfLastLoad['lastCompletedLogRecord'];
    $lastLoadAttempted      = $detailsOfLastLoad['lastLoadLogRecord'];
    $lastLoadSuccessful     = $detailsOfLastLoad['Successful'];
}

?>

<div class='container'>
<div class="panel panel-info">
<div class="panel-heading">Report Source Details</div>
  <div class="panel-body">
  <div class='row'>
<div class='col-md-1'><b>Table:</b></div>
<div class='col-md-10' id='tableName'><?=$lastCompletedLogRecord->UPLOAD_TABLENAME?></div>
</div>
<div class='row'>
<div class='col-md-1'><b>Filename:</b></div>
<div class='col-md-10' id='tableLoadedFrom'><?=$lastCompletedLogRecord->UPLOAD_FILENAME?></div>
</div>
<div class='row'>
<div class='col-md-1'><b>Loader:</b></div>
<div class='col-md-6' id='tableLoadedBy'><?=$lastCompletedLogRecord->UPLOAD_INTRANET?></div>
</div>
<div class='row'>
<div class='col-md-1'><b>Timestamp:</b></div>
<div class='col-md-6' id='tableLoadedAt'><?=$lastCompletedLogRecord->UPLOAD_TIMESTAMP?></div>
<div class='col-md-5'></div>
<div class='col-md-1'><button id='toggleSourceButton' class='btn btn-primary'>Toggle Source</button></div>
</div>
<?php
if(!$lastLoadSuccessful){
    ?>
    <p class='bg-warning'>
    It appears a more recent attempt to load data has failed. Please contact <?=$lastLoadAttempted->UPLOAD_INTRANET?><br/>
    Who attempted to load <?=$lastLoadAttempted->UPLOAD_FILENAME?><br/>
    at <?=$lastLoadAttempted->UPLOAD_TIMESTAMP?><br/>
    </p>
    <?php
}
?>
</div>
</div>
</div>
<?php


//$inflightProjectsTable = new inflightProjectsTable(allTables::$INFLIGHT_PROJECTS);
//$inflightProjectsTable->buildHTMLTable();

?>

<table id='inflightProjectsTable_id' class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
<?=inflightProjectsRecord::htmlHeaderRow();?>
</thead>
<tbody>
</tbody>
<tfoot>
<?=inflightProjectsRecord::htmlHeaderRow();?>
</tfoot>

</table>


</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<script>
$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#inflightProjectsTable_id tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

    // DataTable
    var tableName = $('#tableName').text();
    console.log(tableName);

    var table = $('#inflightProjectsTable_id').DataTable({
    	ajax: {
            url: 'ajax/populateInflightTable.php',
            type: 'POST',
            beforeSend: function(){
            	tableName = $('#tableName').text();
            	console.log("BeforeSend:" + tableName);
            	},
            data: function ( d ) {
                    d.tableName = $("#tableName").text()
        	},
    	},
    	responsive: true,
    	processing: true,
    	deferRender:true,
    	colReorder: true,
    	dom: 'Blfrtip',
        buttons: [
                  'colvis',
                  'excelHtml5',
                  'csvHtml5',
                  'print'
              ]
    });

    // Apply the search
    table.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );


    $(document).on('click','#toggleSourceButton',function(){
    	var currentTable = $('#tableName').text().trim();
    	var toggleTables = ['<?=allTables::$INFLIGHT_BASELINE;?>','<?=allTables::$INFLIGHT_PROJECTS;?>'];
    	console.log('toggle from ' + currentTable + "...");

    	if(currentTable == toggleTables[0]){
    		var toggleTo = toggleTables[1];
    	} else {
    		var toggleTo = toggleTables[0];
    		$('#tableLoadedFrom').text('');
    		$('#tableLoadedBy').text('');
    		$('#tableLoadedAt').text('');
    	}
    	console.log('toggle to ' + toggleTo + "...");
		$('#tableName').text(toggleTo);
		$('#tableLoadedFrom').text('');
		$('#tableLoadedBy').text('');
		$('#tableLoadedAt').text('');
		table.ajax.reload();
    });




} );
// $(document).ready( function () {
//     $('#inflightProjectsTable_id').DataTable({
//     	   responsive: true
//     });
// } );

</script>
