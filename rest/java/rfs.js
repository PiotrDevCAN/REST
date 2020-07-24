/**
 *
 */


function Rfs() {

	var table;

	this.listenForArchiveRfs = function(){
		$(document).on('click','.archiveRfs', function(e){
			var rfsId = $(this).data('rfsid');
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
			$(this).addClass('spinning').attr('disabled',true);			
			$(this).prev('td.details-control').trigger('click');	
			
			console.log($(this));
			console.log($(this).prev('td.details-control'));
			
			var rfsId = $(this).data('rfsid');
//			var URL = "pd_newRfs.php?rfs=" + rfsId;
//			var child = window.open(URL, "_blank");
//			child.onunload = function(){ console.log('Child window closed'); Rfs.table.ajax.reload(); };
			
		    $.ajax({
		    	url: "ajax/getEditRfsForm.php",
		        type: 'POST',
		    	data: {rfsId:rfsId},
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		console.log(result);
		    		var resultObj = JSON.parse(result);		    		
		    		$('#editRfsModalBody').html(resultObj.form);
		    		$('#editRfsModal').modal('show');
		    	}
		    });			
			
		});
	},
	
	this.listenForSlipRfs = function(){
		console.log('set slip listener');
		$(document).on('click','.slipRfs', function(e){			
			$(this).addClass('spinning').attr('disabled',true);			
			$(this).prev('td.details-control').trigger('click');	
			
			console.log($(this));
			console.log($(this).prev('td.details-control'));
			
			var rfsId = $(this).data('rfsid');
	
		    $.ajax({
		    	url: "ajax/getSlipRfsForm.php",
		        type: 'POST',
		    	data: {rfsId:rfsId},
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		var resultObj = JSON.parse(result);		    		
		    		$('#slipRfsModalBody').html(resultObj.form);
		    		$('#slipRfsModal').modal('show');
		    	}
		    });			
			
		});
	},


	this.listenForDeleteRfs = function(){
		$(document).on('click','.deleteRfs', function(e){
			var rfsId = $(this).data('rfsid');
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
	
	
	this.listenForGoLiveRfs = function(){
		$(document).on('click','.goLiveRfs', function(e){
			console.log('go live');
			$(this).addClass('spinning').attr('disabled');
			var rfsid = $(this).data('rfsid');
		    $.ajax({
		    	url: "ajax/goLiveRfs.php",
		        type: 'POST',
		    	data: {rfsid:rfsid},
		    	success: function(result){
		    		console.log(result);
		    		var resultObj = JSON.parse(result);
					Rfs.table.ajax.reload();
	    		}
		    });
		});
	},
	
	this.listenForSelectRfs = function(){
		$(document).on('change','#selectRfs',function(){
			var rfs = $('#selectRfs option:selected').val();			
			document.cookie = "selectedRfs=" + rfs + ";" + "path=/;max-age=604800;samesite=lax;"; 					
			Rfs.table.ajax.reload();
		});		
	},

	this.listenForSelectValueStream = function(){
		$(document).on('change','#selectValueStream',function(){	
			var valuestream = $('#selectValueStream option:selected').val();			
			document.cookie = "selectedValueStream=" + valuestream + ";" + "path=/;max-age=604800;samesite=lax;"; 			
			Rfs.table.ajax.reload();
		});		
	},
	
	this.listenForSelectBusinessUnit = function(){
		$(document).on('change','#selectBusinessUnit',function(){	
			var businessunit = $('#selectBusinessUnit option:selected').val();			
			document.cookie = "selectedBusinessUnit=" + businessunit + ";" + "path=/;max-age=604800;samesite=lax;"; 			
			Rfs.table.ajax.reload();
		});		
	},
	

	this.listenForSelectRequestor = function(){
		$(document).on('change','#selectRequestor',function(){	
			var requestor = $('#selectRequestor option:selected').val();			
			document.cookie = "selectedRequestor=" + requestor + ";" + "path=/;max-age=604800;samesite=lax;"; 			
			Rfs.table.ajax.reload();
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
	    	language: {
	    	      emptyTable: "Please select one or more of :  RFS, Value Stream, Business Unit, Requestor from above"
	    	},
	    	ajax: {
	            url: 'ajax/populateRfsHTMLTable.php',
	            type: 'POST',
	            data: function ( d ) {
	                d.rfsid = $('#selectRfs option:selected').val();
	                d.valuestream = $('#selectValueStream option:selected').val();
	                d.businessunit = $('#selectBusinessUnit option:selected').val();
	                d.requestor = $('#selectRequestor option:selected').val();
	            },
	        }	,
	    	autoWidth: true,
	    	deferRender: true,
	    	responsive: true,
	    	processing: true,
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
	
	this.buildPipelineReport =  function(){
		var formData = $('form').serialize();
		var rfs = new Rfs();
	    $.ajax({
	    	url: "ajax/createPipelineHTMLTable.php",
	        type: 'POST',
	    	data: formData,
	        before: function(){
	        	$('#rfsTableDiv').html('<h2>Table being built</h2>');
	        },
	    	success: function(result){
	    		$('#rfsTable_id').DataTable().destroy();
	        	$("#rfsTableDiv").html(result);
	        	rfs.initialisePipelineDataTable();
	    		}
	    });
	},
	
	this.initialisePipelineDataTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#rfsTable_id tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    Rfs.table = $('#rfsTable_id').DataTable({
	    	ajax: {
	            url: 'ajax/populatePipelineRfsHTMLTable.php',
	            type: 'POST',
	        }	,
	    	autoWidth:  true,
	    	responsive: true,
	    	processing: true,
	    	colReorder: true,
	    	dom: 'Blfrtip',
	        buttons: [
	                  'colvis',
	                  'excelHtml5',
	                  'csvHtml5',
	                  'print'
	              ],
	    });
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

	
	
	

	this.destroyRfsReport = function(){
		$('#rfsTable_id').DataTable().destroy();
	}
	
	
	this.listenForSaveRfs = function(){
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
		        	//	do the following before the save is started
		        	},
		      	success: function(response) {
		            // 	do what ever you want with the server response if that response is "success"
		            	console.log(response);
		            	console.log(JSON.parse(response));
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
		               if(responseObj.Messages != null){
		            	   var messages =  "<p>" + responseObj.Messages +  "</p>";
		               }
		               var messages =  "<p>" + responseObj.Messages +  "</p>";
		               $('.modal-body').html(rfsIdTxt + savedResponseTxt + messages);
		               $('#myModal').modal('show');
		               $('#myModal').on('hidden.bs.modal', function () {
		                	  // do something…
			                if(responseObj.Update==true){
		    	            	window.close();
		        	        } else {
		            	    	$('#resetRfs').click();
		            	    	$(':submit').removeClass('spinning');
		            	    	$('#RFS_ID').css("background-color","#ffffff");
		                	}
	                	});
	          	},
		      	fail: function(response){
						console.log('Failed');
						console.log(response);
		                $('.modal-body').html("<h2>Json call to save record Failed.Tell Rob</h2>");
		                $('#myModal').modal('show');
					},
		      	error: function(error){
		            //	handle errors here. What errors	            :-)!
		        		console.log('Ajax error' );
		        		console.log(error.statusText);
		                $('.modal-body').html("<h2>Json call to save record Errored " + error.statusText + " Tell Rob</h2>");
		        	},
		      	always: function(){
		        		console.log('--- saved resource request ---');

		      	}
			});
		event.preventDefault();
		});
	},
	
	this.preventDuplicateRfsEntry = function(){
		$('#RFS_ID').on('keyup',function(e){
			var newRfsId = $(this).val().trim();
			var allreadyExists = ($.inArray(newRfsId, knownRfs) >= 0 );
			if(allreadyExists){ // comes back with Position in array(true) or false is it's NOT in the array.
				$(':submit').attr('disabled',true);
				$(this).css("background-color","LightPink");
				$('#RFS_ID').focus();
				alert('RFS: ' + newRfsId + ' you have specified has already been defined, please enter a unique RFS ID');
			} else {
				$(this).css("background-color","LightGreen");
				$(':submit').attr('disabled',false);
			};
		});
	}	
	
	this.refreshReportOnRfsUpdate = function(){
		$(document).on('hide.bs.modal',function(e){		
			Rfs.table.ajax.reload();
		});
	}
	
	this.prepareStartDateOnModal = function(element){
		var reference = $(element).data('reference');
		var id = element.id;
		var db2DateElementId = '#END_DATE' + reference; 
		
		startPicker = new Pikaday({
    			firstDay:1,
        		field: element,
        		format: 'D MMM YYYY',
        		showTime: false,
        		minDate: new Date(),
        		onSelect: function() {            	
            		var db2Value = this.getMoment().format('YYYY-MM-DD')
            		$(db2DateElementId).val(db2Value);
            		$(startPickers).each(function(index,element){
						var sDate = element.getDate();
						startPickers[index].setStartRange(sDate);
        				endPickers[index].setStartRange(sDate);
        				endPickers[index].setMinDate(sDate);		
					})	
			}
    	});	
		return startPicker;
	}	
	
	
	this.prepareEndDateOnModal = function(element, startPicker){
		
		var reference = $(element).data('reference');		
		var db2DateElementId = '#END_DATE' + reference; 
	
		var endPicker = new Pikaday({
    		firstDay:1,
        	field: element,
        	format: 'D MMM YYYY',
	        showTime: false,
    	    minDate: new Date(),
        	onSelect: function() {
            	var db2Value = this.getMoment().format('YYYY-MM-DD')
               	$(db2DateElementId).val(db2Value);
           		$(endPickers).each(function(index,element){
					var eDate = element.getDate();
					startPickers[index].setEndRange(eDate);
        			startPickers[index].setMaxDate(eDate);
        			endPickers[index].setEndRange(eDate);							
				})
			}
    	})
		return endPicker;
	}
}