/**
 *
 */


function Rfs() {

	var table;

	this.init = function(){
		console.log('+++ Function +++ RFS.init');
		console.log('--- Function --- RFS.init');

	},

	this.listenForArchiveRfs = function(){
		$(document).on('click','.archiveRfs', function(e){
			var rfsId = $(e.target).data('rfsid');
			$('#archiveRfsModalBody').html("<h5>Please confirm you wish to ARCHIVE RFS:" + rfsId + " and all it's associated Resource</h5>" +
					"<form id='rfsForm'><input type='hidden' name='RFS_ID' value='" + rfsId + "' /></form>");
			$('#archiveRfsModal').modal('show');
			console.log(rfsId);
			});
	},

	this.listenForConfirmArchiveRfs = function(){
		$(document).on('click','#archiveConfirmedRfs', function(e){
			$('#archiveConfirmedRfs').addClass('spinning');
			var formData = $('#rfsForm').serialize();
		    $.ajax({
		    	url: "ajax/archiveRfs.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
					Rfs.table.ajax.reload();
		    		var resultObj = JSON.parse(result);
					$('#archiveRfsModalBody').html(resultObj.messages);
					setTimeout(function(){ $('#archiveRfsModal').modal('hide'); }, 2000);
					$('#archiveConfirmedRfs').removeClass('spinning');
		    		}
		    });
		});
	},





	this.listenForEditRfs = function(){
		$(document).on('click','.editRfs', function(e){
			var rfsId = $(e.target).data('rfsid');
			var URL = "pa_newRfs.php?rfs=" + rfsId;
			var child = window.open(URL, "_blank");
			child.onunload = function(){ console.log('Child window closed'); Rfs.table.ajax.reload(); };
		});
	},


	this.listenForDeleteRfs = function(){
		$(document).on('click','.deleteRfs', function(e){
			var rfsId = $(e.target).data('rfsid');
			$('#deleteRfsModalBody').html("<h5>Please confirm you wish to delete RFS:" + rfsId + " and all it's associated Resource</h5>" +
					"<form id='rfsForm'><input type='hidden' name='RFS_ID' value='" + rfsId + "' /></form>");
			$('#deleteRfsModal').modal('show');
			console.log(rfsId);
			});
	},




	this.listenForConfirmDeleteRfs = function(){
		$(document).on('click','#deleteConmfirmedRfs', function(e){
			var formData = $('#rfsForm').serialize();
		    $.ajax({
		    	url: "ajax/deleteRfs.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		var resultObj = JSON.parse(result);
					$('#deleteRfsModalBody').html(resultObj.messages);
					Rfs.table.ajax.reload();
					setTimeout(function(){ $('#deleteRfsModal').modal('hide'); }, 3000);

		    		}
		    });
		});
	},

	this.initialiseDataTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#rfsTable_id tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    Rfs.table = $('#rfsTable_id').DataTable({
	    	ajax: {
	            url: 'ajax/populateRfsHTMLTable.php',
	            type: 'POST',
	        }	,
	    	autoWidth: false,
	    	deferRender: true,
	    	responsive: false,
	    	// scrollX: true,
	    	processing: true,
	    	responsive: true,
	    	colReorder: true,
	    	dom: 'Blfrtip',
	        buttons: [
	                  'colvis',
	                  'excelHtml5',
	                  'csvHtml5',
	                  'print'
	              ],
	    });

	    Rfs.table.columns([10]).visible(false,false);

	    Rfs.table.columns.adjust().draw(false);




	    // Apply the search
	    Rfs.table.columns().every( function () {
	        var that = this;

	        $( 'input', this.footer() ).on( 'keyup change', function () {
	            if ( that.search() !== this.value ) {
	                that
	                    .search( this.value )
	                    .draw();
	            }
	        } );
	    } );
	},

	this.buildRfsReport =  function(){
		var formData = $('form').serialize();
		var rfs = new Rfs();

	    $.ajax({
	    	url: "ajax/createRfsHTMLTable.php",
	        type: 'POST',
	    	data: formData,
	        before: function(){
	        	$('#rfsTableDiv').html('<h2>Table being built</h2>');
	        },
	    	success: function(result){
	    		$('#rfsTable_id').DataTable().destroy();
	        	$("#rfsTableDiv").html(result);
	        	rfs.initialiseDataTable();
	    		}
	    });
	},

	this.destroyRfsReport = function(){
		$('#rfsTable_id').DataTable().destroy();
	}



}


$( document ).ready(function() {
	var rfs = new Rfs();
    rfs.init();
});