/**
 *
 */

var slippingCounter = 0;	

function Rfs() {

	var table;

	this.applySearch = function(){
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
	}

	this.listenForArchiveRfs = function(){
		$(document).on('click','.archiveRfs', function(e){
			var rfsId = $(this).data('rfsid');
			$('#archiveRfsModalBody').html("<h5>Please confirm you wish to ARCHIVE RFS:" + rfsId + " and all it's associated Resource</h5>" +
					"<form id='rfsForm'><input type='hidden' name='RFS_ID' value='" + rfsId + "' /></form>");
			$('#archiveRfsModal').modal('show');
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
		    		var resultObj = JSON.parse(result);		    		
		    		$('#editRfsModalBody').html(resultObj.form);
		    		$('#editRfsModal').modal('show');
		    	}
		    });			
			
		});
	},
	
	this.listenForSlipRfs = function(){
		$(document).on('click','.slipRfs', function(e){			
			$(this).addClass('spinning').attr('disabled',true);			
			$(this).prev('td.details-control').trigger('click');	
	
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
			});
	},

	this.listenForConfirmDeleteRfs = function(){
		$(document).on('click','#deleteConmfirmedRfs', function(e){
			$(this).addClass('spinning').attr('disabled');
			var formData = $('#rfsForm').serialize();
		    $.ajax({
		    	url: "ajax/deleteRfs.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
					$('.spinning').removeClass('spinning').attr('enabled');
		    		var resultObj = JSON.parse(result);
					$('#deleteRfsModalBody').html(resultObj.messages);
					Rfs.table.ajax.reload();
					setTimeout(function(){ $('#deleteRfsModal').modal('hide'); }, 3000);

		    		}
		    });
		});
	},

	this.listenForConfirmGoLiveRfs = function(){
		$(document).on('submit','#goLiveRfsForm', function(e){
			$('#confirmGoLiveRfs').addClass('spinning').attr('disabled',true);
			var rfsid          = $('#goLiveRfsId').val();
			var requestorName  = $('#plREQUESTOR_NAME').val();
			var requestorEmail = $('#plREQUESTOR_EMAIL').val();
		    $.ajax({
		    	url: "ajax/goLiveRfs.php",
		        type: 'POST',
		    	data: {
					rfsid:rfsid,
					requestorName:requestorName,
					requestorEmail:requestorEmail
				},
		    	success: function(result){
		    		var resultObj = JSON.parse(result);
					Rfs.table.ajax.reload();
					$('#goLiveRfsId').val('');
					$('#plREQUESTOR_NAME').val('');
					$('#plREQUESTOR_EMAIL').val('');
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#goLiveRfsModal').modal('hide');
				}
		    });
		});
	},
	
	this.listenForGoLiveRfs = function(){
		$(document).on('click','.goLiveRfs', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			$('#confirmGoLiveRfs').attr('disabled',false)
			$('#goLiveRfsId').val($(this).data('rfsid'));
			$('#REQUESTOR_NAME').val('');
			$('#REQUESTOR_EMAIL').val('');
			$('#goLiveRfsModal').modal('show');
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
	                d.pipelineLiveArchive = $('input[name="pipelineLiveArchive"]:checked').val();
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

	this.buildRfsReport =  function(getColumnsFromAjax){
		var rfs = new Rfs();

		if(getColumnsFromAjax == null){
			var formData = $('form').serialize();
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
		} else {
			rfs.initialiseDataTable();
		}
	},
	
	this.initialiseClaimTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#claimTable_id tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    Rfs.table = $('#claimTable_id').DataTable({
	    	language: {
	    	      emptyTable: "Please select one or more of :  RFS, Value Stream, Business Unit, Requestor from above"
	    	},
	    	ajax: {
	            url: 'ajax/populateClaimHTMLTable.php',
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

	        columns: [ 
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
	            { defaultContent: "", visible:true,  render: { _:'display', sort:'sort' }, },
	            { defaultContent: "", visible:false, render: { _:'display', sort:'sort' }, },
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
		        null,
				null,
				null,
				null,
				null,
	        ],
	    });
	    Rfs.table.columns([1,2,3,4,5,8,9,10,19,20,21]).visible(false,false);
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

	this.buildClaimReport =  function(getColumnsFromAjax){
		var rfs = new Rfs();
	    
		if(getColumnsFromAjax == null){
			var formData = $('form').serialize();		
			$.ajax({
				url: "ajax/createClaimHTMLTable.php",
				type: 'POST',
				data: formData,
				before: function(){
					$('#claimTableDiv').html('<h2>Table being built</h2>');
				},
				success: function(result){
					$('#claimTable_id').DataTable().destroy();
					$("#claimTableDiv").html(result);
					rfs.initialiseClaimTable();
				}
			});
		} else {
			rfs.initialiseClaimTable();
		}
	},

	this.buildNoneActiveReport =  function(getColumnsFromAjax){
		var rfs = new Rfs();

		var formData = $('form').serialize();
		$.ajax({
			url: "ajax/createNoneActiveHTMLTable.php",
			type: 'POST',
			data: formData,
			before: function(){
				$('#noneActiveTableDiv').html('<h2>Table being built</h2>');
			},
			success: function(result){
				$('#noneActiveTable_id').DataTable().destroy();
				$("#noneActiveTableDiv").html(result);
				rfs.initialiseNoneActiveTable();
				}
		});
	},

	this.initialiseNoneActiveTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#noneActiveTable_id tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    Rfs.table = $('#noneActiveTable_id').DataTable({
	    	language: {
	    	      //emptyTable: "Please select one or more of :  RFS, Value Stream, Business Unit, Requestor from above"
	    	},
	    	ajax: {
	            url: 'ajax/populateNoneActiveHTMLTable.php',
	            type: 'POST',
	            data: function ( d ) {
	            //     d.rfsid = $('#selectRfs option:selected').val();
	            //     d.valuestream = $('#selectValueStream option:selected').val();
	            //     d.businessunit = $('#selectBusinessUnit option:selected').val();
	            //     d.requestor = $('#selectRequestor option:selected').val();
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

	        columns: [ 
		        { name: "RFS_ID", data: "RFS_ID", defaultContent: "", visible:true },
				{ name: "PRN", data: "PRN", defaultContent: "", visible:true },
				{ name: "PROJECT_TITLE", data: "PROJECT_TITLE", defaultContent: "", visible:true },
				{ name: "PROJECT_CODE", data: "PROJECT_CODE", defaultContent: "", visible:true },
				{ name: "REQUESTOR_NAME", data: "REQUESTOR_NAME", defaultContent: "", visible:false },
				{ name: "REQUESTOR_EMAIL", data: "REQUESTOR_EMAIL", defaultContent: "", visible:false },
				{ name: "VALUE_STREAM", data: "VALUE_STREAM", defaultContent: "", visible:false },
				{ name: "BUSINESS_UNIT", data: "BUSINESS_UNIT", defaultContent: "", visible:true },
				{ name: "LINK_TO_PGMP", data: "LINK_TO_PGMP", defaultContent: "", visible:false },
				{ name: "RFS_CREATOR", data: "RFS_CREATOR", defaultContent: "", visible:false },
				{ name: "RFS_CREATED", data: "RFS_CREATED", defaultContent: "", visible:false },
				{ name: "RR.RESOURCE_REFERENCE", data: "RESOURCE_REFERENCE", defaultContent: "", visible:true },
				{ name: "ORGANISATION", data: "ORGANISATION", defaultContent: "", visible:false },
				{ name: "SERVICE", data: "SERVICE", defaultContent: "", visible:true },
				{ name: "DESCRIPTION", data: "DESCRIPTION", defaultContent: "", visible:false },
				{ name: "START_DATE",  data: "START_DATE", defaultContent: "", visible:false,  render: { _:'display', sort:'sort' }, },
				{ name: "END_DATE",  data: "END_DATE", defaultContent: "", visible:false, render: { _:'display', sort:'sort' }, },
				{ name: "TOTAL_HOURS", data: "TOTAL_HOURS", defaultContent: "", visible:false },
				{ name: "RESOURCE_NAME", data: "RESOURCE_NAME", defaultContent: "", visible:true },
				{ name: "REQUEST_CREATOR", data: "REQUEST_CREATOR", defaultContent: "", visible:false },
				{ name: "REQUEST_CREATED", data: "REQUEST_CREATED", defaultContent: "", visible:false },
				{ name: "CLONED_FROM", data: "CLONED_FROM", defaultContent: "", visible:false },
				{ name: "STATUS", data: "STATUS", defaultContent: "", visible:false },
				{ name: "RATE_TYPE", data: "RATE_TYPE", defaultContent: "", visible:false },
				{ name: "HOURS_TYPE", data: "HOURS_TYPE", defaultContent: "", visible:false },
				{ name: "RFS_STATUS", data: "RFS_STATUS", defaultContent: "", visible:false },
				{ name: "RFS_TYPE", data: "RFS_TYPE", defaultContent: "", visible:false },
				// workaround needed
				{ name: "MAY_21", data: "MAY_21", defaultContent: "", visible:false },
				{ name: "JUN_21", data: "JUN_21", defaultContent: "", visible:false },
				{ name: "JUL_21", data: "JUL_21", defaultContent: "", visible:false },
				{ name: "AUG_21", data: "AUG_21", defaultContent: "", visible:false },
				{ name: "SEP_21", data: "SEP_21", defaultContent: "", visible:false },
				{ name: "OCT_21", data: "OCT_21", defaultContent: "", visible:false },
	        ],
		});
		// Rfs.table.columns([1,2,3,4,5,8,9,10,19,20,21]).visible(false,false);
		// Rfs.table.columns.adjust().draw(false);
		this.applySearch();

		// set rows roles
	    $('#noneActiveTable_id tbody tr').each( function () {
			$(this).attr( 'role', 'row' );
			$(this).prop( 'role', 'row' );
	    } );
	},

	this.buildPipelineReport =  function(){
		var formData = $('form').serialize();
		var rfs = new Rfs();

		if(getColumnsFromAjax == null){
			var formData = $('form').serialize();
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
		} else {
			rfs.initialisePipelineDataTable();
		}
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
					$('.spinning').removeClass('spinning');
					$('#editRfsModal').modal('hide');
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
					$('#myModal .modal-body').html(rfsIdTxt + savedResponseTxt + messages);
					
					$('#myModal').modal('show');
					$('#myModal').on('hidden.bs.modal', function () {
						// do something…
						if(responseObj.update==true){
							window.close();
						} else {
							$('#resetRfs').click();
							$('.spinning').removeClass('spinning');
							$('#RFS_ID').css("background-color","#ffffff");
						}
					});
	          	},
		      	fail: function(response){						
					$('#myModal .modal-body').html("<h2>Json call to save record Failed.Tell Piotr</h2>");
					$('#myModal').modal('show');
				},
		      	error: function(error){
		            //	handle errors here. What errors	            :-)!
					console.log('Ajax error' );
					console.log(error.statusText);
					$('.modal-body').html("<h2>Json call to save record Errored " + error.statusText + " Tell Piotr</h2>");
					$('#myModal').modal('show');
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
		var db2DateElementId = '#START_DATE_' + reference; 
		
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
	
	this.prepareEndDateOnModal = function(element){
		
		var reference = $(element).data('reference');		
		var db2DateElementId = '#END_DATE_' + reference; 
	
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

	this.listenForSaveSlippedRfsDates = function(){
		$( "#saveSlippedRfsDates" ).on('click',function( event ) {
			$(this).addClass('spinning').attr('disabled',true);			
			$('.startDate2').each(function(index, element){
				var reference = $(element).data('reference');
				var startDate = $(element).val();				
			    $.ajax({
			    	url: "ajax/slipResourceHours.php",
		        	type: 'POST',
		    		data: {ModalSTART_DATE       :startDate,
 					   	   ModalResourceReference:reference},
					beforeSend: function(){
						slippingCounter++;
						},		
		    		success: function(result){		
						if(--slippingCounter<=0){
				    		$('#editResourceHours').html('');
							$('#resourceHoursModal').modal('hide');
    						$('.spinning').removeClass('spinning').attr('disabled',false);
							$('#slipRfsModal').modal('hide');
							Rfs.table.ajax.reload();				
						} 
					}
		    	});
			});	
		});
	}
}