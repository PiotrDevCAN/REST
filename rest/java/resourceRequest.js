/**
 *
 */


function ResourceRequest() {

	var table;

	this.init = function(){
		console.log('+++ Function +++ ResourceRequest.init');
		$('#resourceHoursModal').on('shown.bs.modal', function (e) {
			ModalstartPicker = new Pikaday({
				firstDay:1,
				disableDayFn: function(date){
				    // Disable all but Monday
				    return date.getDay() === 0 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 5 || date.getDay() === 6;
				},
				field: document.getElementById('InputModalSTART_DATE'),
				format: 'D MMM YYYY',
				showTime: false,
				onSelect: function() {
					var db2Value = this.getMoment().format('YYYY-MM-DD')
					jQuery('#ModalSTART_DATE').val(db2Value);
				}
			}),
			ModalendPicker = new Pikaday({
				firstDay:1,
				disableDayFn: function(date){
				    // Disable all but Friday
				    return date.getDay() === 0 || date.getDay() === 1 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 6;
				},
				field: document.getElementById('InputModalEND_DATE'),
				format: 'D MMM YYYY',
				showTime: false,
				onSelect: function() {
					var db2Value = this.getMoment().format('YYYY-MM-DD')
					jQuery('#ModalEND_DATE').val(db2Value);
				}
			}),
			console.log('hours modal shown');

			var initialDate = $('#ModalSTART_DATE').val();
			ModalstartPicker.setDate(initialDate);

			var initialEndDate = $('#ModalEND_DATE').val();
			ModalendPicker.setDate(initialEndDate);
		})
		console.log('--- Function --- ResourceRequest.init');

	},

	this.listenForSeekBwo = function(){
		$(document).on('click','.seekBwo', function(e){
			if(	$('#bwo').val()){
				$('#bwo').val('');
			} else {
				$('#bwo').val($(e.target).data('reference'));
			}
			ResourceRequest.table.draw();
		});
	},



	this.listenForAddPlatformTypePrnCode = function(){
		$(document).on('click','.setPlatformTypePrnCode', function(e){
			addPlatformTypePrnCode($(e.target).data('reference'),$(e.target).data('parent'));
		});
	},

	addPlatformTypePrnCode = function(resourceReference){
		console.log(resourceReference);
		$('#platformTypePrnCodeForm').find('#ptpcRESOURCE_REFERENCE').val(resourceReference);
		$('#PlatformTypePrnCodeModal').modal('show');
	},

	this.listenForSavePlatformTypePrnCode = function(){
		$(document).on('click','#savePlatformTypePrnCode', function(e){
			var formData = $('#platformTypePrnCodeForm').serialize();
			console.log(formData);
		    $.ajax({
		    	url: "ajax/savePlatformTypePrnCode.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		var resultObj = JSON.parse(result);
		    		console.log(resultObj);
					$('#platformTypePrnCodeForm').find('#pcptRESOURCE_REFERENCE').val("");
					$('#platformTypePrnCodeForm').find('#pcptRESOURCE_NAME').val("");
					$('#platformTypePrnCodeForm').find('#PRN').val("");
					$('#platformTypePrnCodeForm').find('#PROJECT_CODE').val("");
					$('#PlatformTypePrnCodeModal').modal('hide');
					ResourceRequest.table.ajax.reload();
					var errorMessageText = '';
					if(resultObj.Messages!=''){
						errorMessageText += "<h5>Message</h5><p>" + resultObj.Messages + "</p>";
					};
					if(resultObj.Exception!=''){
						errorMessageText += "<h4 class='text-warning'>Exception</h4><p class='text-warning'>" + resultObj.Exception + "</p>";
					};
					if(errorMessageText!=''){
						console.log()
						$('#errorMessageBody').html(errorMessageText);
						$('#errorMessageModal').modal('show');
					};
					console.log(errorMessageText);
		    	}
		    });
		});
	},

	this.listenForDeleteRecord = function(){
		$(document).on('click','.deleteRecord', function(e){
			var resourceReference = $(e.target).data('reference');
			var platform = $(e.target).data('platform');
			var type = $(e.target).data('type');


			var message = "<p>Please confirm you wish to delete Resource Reference : " + resourceReference + "</p>";
			message .= "<br/><b>Platform:</b>" + platform;
			message .= "<br/><b>Type:</b>"  +type;
			$('#deleteMessageBody').html(message);
			$('#confirmDeleteModal').modal('show');
		});
	},

	this.listenForConfirmedDelete = function(){
		$(document).on('click','#confirmDeleteResource', function(e){
			$('#confirmDeleteModal').modal('hide');
		});
	},

	this.listenForEditRecord = function(){
		$(document).on('click','.editRecord', function(e){
			var resourceReference = $(e.target).data('reference');
			var URL = "pa_newResourceRequest.php?resource=" + resourceReference;
			var child = window.open(URL, "_blank");
			child.onunload = function(){ ResourceRequest.table.ajax.reload(); };
		});
	},


	this.listenForEditResourceName = function(){
		$(document).on('click','.editResource', function(e){
			var resourceReference = $(e.target).data('reference');
			var parent            = $(e.target).data('parent');
			console.log(parent);
			$('#resourceNameForm').find('#RESOURCE_REFERENCE').val(resourceReference);
			$('#resourceNameModal').modal('show');
			console.log(resourceReference);
		});
	},

	this.listenForSaveResourceName = function(){
		$(document).on('click','#saveResourceName', function(e){
			var formData = $('#resourceNameForm').serialize();
		    $.ajax({
		    	url: "ajax/saveResourceName.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
					$('#resourceNameForm').find('#RESOURCE_REFERENCE').val("");
					$('#resourceNameForm').find('#RESOURCE_NAME').val("");
					$('#resourceNameModal').modal('hide');
					ResourceRequest.table.ajax.reload();
		    		}
		    });
		});
	},


	this.listenForEditHours = function(){
		$(document).on('click','.editHours', function(e){
			var resourceRequest = new ResourceRequest();
			var resourceReference = $(e.target).data('reference');
			var startDate = $(e.target).data('startDate');
			$('#resourceHoursForm').find('#RESOURCE_REFERENCE').val(resourceReference);
		    $.ajax({
		    	url: "ajax/contentsOfEditHoursModal.php",
		        type: 'POST',
		    	data: {	resourceReference: resourceReference
		    	},
		    	success: function(result){
		    		resultObj = JSON.parse(result);
		    		$('#editResourceHours').html(resultObj.editResourceHours);
		    		$('#editResourceHoursFooter').html(resultObj.editResourceHoursFooter);
					$('#resourceHoursModal').modal('show');
		    	}
		    	});
			});
	},

	this.listenForSlipStartDate = function(){
		console.log('listener being set');
		$(document).on('click','#slipStartDate', function(e){
			console.log('listener fired');
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/slipResourceHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
				    $('#editResourceHours').html('<p></p>');
					$('#resourceHoursModal').modal('hide');
		    		ResourceRequest.table.ajax.reload();
		    		}
		    });
		});
	},

	this.listenForReinitialise = function(){
		console.log('listener being set');
		$(document).on('click','#reinitialise', function(e){
			console.log('listener fired');
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/reinitialiseHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		ResourceRequest.table.ajax.reload();
		    		$('#editResourceHours').html('<p></p>');
				    $('#resourceHoursModal').modal('hide');
		    		}
		    });
		});
	},

	this.listenForDuplicateResource = function(){
		$(document).on('click','.requestDuplication', function(e){
			$('#confirmDuplicateRR').text($.trim($(e.target).data('reference')));
			$('#confirmDuplicateRFS').text($.trim($(e.target).data('rfs')));
			$('#confirmDuplicateType').text($.trim($(e.target).data('type')));
			$('#confirmDuplicateStart').text($.trim($(e.target).data('start')));
			$('#confirmDuplicationModal').modal('show');
		});
	},


	this.listenForConfirmedDuplication = function(){
		$(document).on('click','#duplicationConfirmed', function(e){
			var resourceReference = $('#confirmDuplicateRR').text();
		    $.ajax({
		    	url: "ajax/duplicateResource.php",
		        type: 'POST',
		    	data: { resourceReference : resourceReference,
		    			delta: false,
		    			drawDown: false},
		    	success: function(result){
					$('#confirmDuplicationModal').modal('hide');
		    		ResourceRequest.table.ajax.reload();
		    		console.log(result);
		    		}
		    	});
		});
	},

	this.listenForSaveAdjustedHours = function(){
		$(document).on('click','#saveAdjustedHours', function(e){
			console.log('save adjusted triggered');
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/saveAdjustedHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		ResourceRequest.table.ajax.reload();
					$('#resourceHoursModal').modal('hide');
		    		}
		    	});
		});
	},

	this.listenForSaveAdjustedHoursWithDelta = function(){
		$(document).on('click','#saveAdjustedHoursWithDelta', function(e){
			// First create a duplicate Record.
			var resourceReference = $('#ModalResourceReference').val();
			var formData = $('#resourceHoursForm').serialize();
			console.log(resourceReference);
		    $.ajax({
		    	url: "ajax/duplicateResource.php",
		        type: 'POST',
		    	data: { resourceReference : resourceReference,
                        delta: true,
                        drawDown: false},
		    	success: function(result){
					$('#confirmDuplicationModal').modal('hide');
					var resultObj = JSON.parse(result);
		    		var deltaResourceReference = resultObj.resourceReference;
		    		console.log(resultObj);
					console.log('Delta is' + deltaResourceReference);

				    $.ajax({
				    	url: "ajax/saveAdjustedHoursWithDelta.php",
				        type: 'POST',
				    	data: {deltaResourceRef  : deltaResourceReference,
                               formData          : formData },
				    	success: function(result){
				    		console.log(result);
				    		ResourceRequest.table.ajax.reload();
							$('#resourceHoursModal').modal('hide');
				    		}
				    	});

		    		}
		    	});
		});
	},


	this.listenForSaveAdjustedHoursWithDrawDown = function(){
		$(document).on('click','#saveAdjustedHoursWithDrawDown', function(e){
			// First create a duplicate Record.
			var resourceReference = $('#ModalResourceReference').val();
			var formData = $('#resourceHoursForm').serialize();
			console.log(resourceReference);
		    $.ajax({
		    	url: "ajax/duplicateResource.php",
		        type: 'POST',
		    	data: { resourceReference : resourceReference,
                        drawDown: true,
                        delta : false},
		    	success: function(result){
					$('#confirmDuplicationModal').modal('hide');
					var resultObj = JSON.parse(result);
		    		var deltaResourceReference = resultObj.resourceReference;
		    		console.log(resultObj);
					console.log('Delta is' + deltaResourceReference);

				    $.ajax({
				    	url: "ajax/saveAdjustedHoursWithDrawDown.php",
				        type: 'POST',
				    	data: {deltaResourceRef  : deltaResourceReference,
				    		   originalResourceRef : resourceReference,
                               formData          : formData },
				    	success: function(result){
				    		console.log(result);
				    		addPlatformTypePrnCode(resultObj.resourceReference,$(e.target).data('parent'));
				    		ResourceRequest.table.ajax.reload();
							$('#resourceHoursModal').modal('hide');
				    		}
				    	});

		    		}
		    	});
		});
	},


	this.listenForReportOne = function(){
		$(document).on('click','#reportOne', function(e){
			console.log('triggered report one');
			ResourceRequest.table.columns().visible(true,false);
			ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,12,13,15,16,17,18,20,21,22]).visible(false,false);
		    ResourceRequest.table.columns.adjust().draw(false);
			});
	},

	this.listenForReportTwo = function(){
		$(document).on('click','#reportTwo', function(e){
			console.log('triggered report Two');
		    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22]).visible(true,false);
		    ResourceRequest.table.columns.adjust().draw(false);
			});
	},

	this.listenForDdDetails = function(){
		$(document).on('click','#ddDetails', function(e){
			console.log('triggered ddDetails');
			ResourceRequest.table.columns([17,18]).visible(false,false);
		    ResourceRequest.table.columns([22,24,25]).visible(true,false);
		    ResourceRequest.table.columns.adjust().draw(false);
			});
	},

	this.listenForResetReport = function(){
		$(document).on('click','#resetReport', function(e){
			ResourceRequest.table.columns().visible(true,false);
		    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,17,18,20,21,22,23,24,25,26]).visible(false,false);
		    ResourceRequest.table.columns.adjust().draw(false);
			});
	},



	this.initialiseDataTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#resourceRequestsTable_id tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    ResourceRequest.table = $('#resourceRequestsTable_id').DataTable({
	    	ajax: {
	            url: 'ajax/populateResourceRequestHTMLTable.php',
	            data: function ( d ) {
	                d.startDate = $('#START_DATE').val();
	                d.endDate = $('#END_DATE').val();
	            },
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


	    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,17,18,20,21,22,23,24,25,26]).visible(false,false);
	    ResourceRequest.table.columns.adjust().draw(false);



	    // Apply the search
	    ResourceRequest.table.columns().every( function () {
	        var that = this;

	        $( 'input', this.footer() ).on( 'keyup change', function () {
	            if ( that.search() !== this.value ) {
	                that
	                    .search( this.value )
	                    .draw();
	            }
	        } );
	    } );

	    /* Custom filtering function which will search for BWO and it's child records, when the hidden BWO input field has a value */
	    /* The hidden BWO input field has it's value set when the search glphicon for the BWO is clicked - and datatables draw is then called */
	    $.fn.dataTable.ext.search.push(
	        function( settings, data, dataIndex ) {
	            var seekBWO = parseInt( $('#bwo').val(), 10 );
	            var ParentBwo = parseInt( data[22],10 ); // use data for the ParentBWO column
	            var Bwo = parseInt( data[10],10 );       // use data for the RR  column
	            if ( isNaN( seekBWO) ||
	               ( ( seekBWO == Bwo ) || ( seekBWO == ParentBwo ) )
	               )
	            {
	                return true;
	            }
	            return false;
	        }
	    );



	},

	this.buildResourceReport =  function(){
		var formData = $('form').serialize();
		var resourceRequest = new ResourceRequest();

	    $.ajax({
	    	url: "ajax/createResourceReportHTMLTable.php",
	        type: 'POST',
	    	data: formData,
	        before: function(){
	        	$('#resourceTableDiv').html('<h2>Table being built</h2>');
	        },
	    	success: function(result){
	    		$('#resourceRequestsTable_id').DataTable().destroy();
	        	$("#resourceTableDiv").html(result);
	        	resourceRequest.initialiseDataTable();
	    		}
	    });

	    $(document).on('draw.dt','#resourceRequestsTable_id', function () {
	        if($('#userLevel').html()!='Admin User'){
	        	$('.editRecord').hide();
	        };
	    } );


	},


	this.initialiseDateSelect = function(){
		var startDate,
		endDate,

		startPicker = new Pikaday({
			firstDay:1,
			disableDayFn: function(date){
			    // Disable all but Monday
			    return date.getDay() === 0 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 5 || date.getDay() === 6;
			},
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
			disableDayFn: function(date){
				// Disable all but Monday
				return date.getDay() === 0 || date.getDay() === 1 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 6;
			},
			field: document.getElementById('InputEND_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			onSelect: function() {
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				jQuery('#END_DATE').val(db2Value);
				endDate = this.getDate();
				updateEndDate();
			}
		}),

		updateStartDate = function() {
			var resourceRequest = new ResourceRequest();
			console.log('updatedStartDate');
		    startPicker.setStartRange(startDate);
		    endPicker.setStartRange(startDate);
		    endPicker.setMinDate(startDate);
		    console.log($('#START_DATE').val());
		    resourceRequest.destroyResourceReport();
		    resourceRequest.buildResourceReport();
		},

		updateEndDate = function() {
			var resourceRequest = new ResourceRequest();
			console.log('updatedEndDate');
		    startPicker.setEndRange(endDate);
		    startPicker.setMaxDate(endDate);
		    endPicker.setEndRange(endDate);
		    resourceRequest.destroyResourceReport();
		    resourceRequest.buildResourceReport();
		},


	_startDate = startPicker.getDate(),
	_endDate = endPicker.getDate();

	if (_startDate) {
	    startDate = _startDate;
	    this.updateStartDate();
	}

	if (_endDate) {
	    endDate = _endDate;
	    this.updateEndDate();
	}


	},


	this.destroyResourceReport = function(){
		$('#resourceRequestsTable_id').DataTable().destroy();
	}



}


$( document ).ready(function() {
	var resourceRequest = new ResourceRequest();
    resourceRequest.init();
    $('#resourceNameModal').on('shown.bs.modal', function () {
    $('#resourceNameModal').find('select').select2("destroy").select2();
    });

    $('#PlatformTypePrnCodeModal').on('shown.bs.modal', function () {
    $('#PlatformTypePrnCodeModal').find('select').select2("destroy").select2();
    });

});



