/**
 *
 */


function ResourceRequest() {

	var table;
	var resourceNamesForSelect2 = [];

	this.init = function(){
		console.log('+++ Function +++ ResourceRequest.init');
		$('#resourceHoursModal').on('shown.bs.modal', function (e) {
			ModalstartPicker = new Pikaday({
				firstDay:1,
//				disableDayFn: function(date){
//				    // Disable all but Monday
//				    return date.getDay() === 0 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 5 || date.getDay() === 6;
//				},
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
//				disableDayFn: function(date){
//				    // Disable all but Friday
//				    return date.getDay() === 0 || date.getDay() === 1 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 6;
//				},
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
				$('#bwo').val($(this).data('reference'));
			}
			ResourceRequest.table.draw();
		});
	},


	this.listenForDeleteRecord = function(){
		$(document).on('click','.deleteRecord', function(e){
			var resourceReference = $(this).data('reference');
			var platform = $(this).data('platform');
			var type = $(this).data('type');
			var rfs = $(this).data('rfs');

			$('#deleteResourceRef').val(resourceReference);

			var message = "<p>Please confirm you wish to delete Resource Reference : " + resourceReference + "</p>";
			message += "<br/><b>RFS:</b>" + rfs;
			message += "<br/><b>Platform:</b>" + platform;
			message += "<br/><b>Type:</b>"  +type;
			$('#deleteMessageBody').html(message);
			$('#confirmDeleteResource').attr('disabled',false);
			$('#confirmDeleteModal').modal('show');
		});
	},

	this.listenForConfirmedDelete = function(){
		$(document).on('click','#confirmDeleteResource', function(e){
			var formData = $('#confirmDeleteModalForm').serialize();
		    $.ajax({
		    	url: "ajax/deleteResourceRequest.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		resultObj = JSON.parse(result);
		    		var message = "Record deleted, you may now close this window";
		    		message += "<br/>Feedback from Delete : " +resultObj.Messages
		    		$('#deleteMessageBody').html(message);
		    		$('#confirmDeleteResource').attr('disabled',true);
		    		ResourceRequest.table.ajax.reload();
		    		}
		    });


		});
	},

	this.listenForEditRecord = function(){
		$(document).on('click','.editRecord', function(e){
			var resourceReference = $(this).data('reference');
			var URL = "pa_newResourceRequest.php?resource=" + resourceReference;
			var child = window.open(URL, "_blank");
			child.onunload = function(){ ResourceRequest.table.ajax.reload(); };
		});
	},

	this.populateResourceDropDownWhenModalShown = function(){
		$('#resourceNameModal').on('shown.bs.modal', function(){
			console.log($('#resourceNameModal').find('select').hasClass("select2-hidden-accessible"));
			console.log($('#RESOURCE_NAME'));
			var currentResourceName = $('#currentResourceName').val();
			if (!$('#resourceNameModal').find('select').hasClass("select2-hidden-accessible")){
				$('#resourceNameModal')
				.find('select')
				.select2({data : resourceNamesForSelect2})
				.val(currentResourceName)
				.trigger('change');
			} else {
				$('#resourceNameModal')
				.find('select')
				.val(currentResourceName)
				.trigger('change');
			}			
		});
	},
	

	this.listenForEditResourceName = function(){
		$(document).on('click','.editResource', function(e){
			$(this).addClass('spinning');
			var resourceReference = $(this).data('reference');
			var resourceName      = $(this).data('resourceName');
			var parent            = $(this).data('parent');
			$('#resourceNameForm').find('#RESOURCE_REFERENCE').val(resourceReference);	
			$('#currentResourceName').val(resourceName);
			
			if(resourceNamesForSelect2.length){
				console.log('resourcenames alreadt populated');
	    		$('#resourceNameModal').modal('show');
				$('.spinning').removeClass('spinning');				
			} else {
				console.log('need to popualte names');
				$.ajax({
			    	url: "ajax/getVbacActiveResourcesForSelect2.php",
			        type: 'POST',
			    	success: function(result){
			    		console.log(result);
			    		var resultObj = JSON.parse(result);
			    		resourceNamesForSelect2 = resultObj.data;
			    		$('#resourceNameModal').modal('show');
						$('.spinning').removeClass('spinning');
			    		}
			    });				
			}
		});
	},

	this.listenForSaveResourceName = function(){
		$(document).on('click','#saveResourceName', function(e){
			$(this).addClass('spinning');
			var formData = $('#resourceNameForm').serialize();
		    $.ajax({
		    	url: "ajax/saveResourceName.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
					$('#resourceNameForm').find('#RESOURCE_REFERENCE').val("");
					$('#resourceNameForm').find('#RESOURCE_NAME').val("").trigger('change');
					$('#resourceNameModal').modal('hide');
					$('.spinning').removeClass('spinning');
					ResourceRequest.table.ajax.reload();
		    		}
		    });
		});
	},


	this.listenForEditHours = function(){
		$(document).on('click','.editHours', function(e){
			var resourceRequest = new ResourceRequest();
			var resourceReference = $(this).data('reference');
			var startDate = $(this).data('startDate');
			$('#resourceHoursForm').find('#RESOURCE_REFERENCE').val(resourceReference);
			$('#messageArea').html("<div class='col-sm-4'></div><dic class='col-sm-4'><h3>Form loading.... <span class='glyphicon glyphicon-refresh spinning'></span></h3></div><div class='col-sm-4></div>");
			$.ajax({
		    	url: "ajax/contentsOfEditHoursModal.php",
		        type: 'POST',
		    	data: {	resourceReference: resourceReference
		    	},
		    	success: function(result){
		    		//$('#resourceHoursModal').modal('hide');
		    		resultObj = JSON.parse(result);
		    		$('#messageArea').html("");
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

	this.listenForMoveEndDate = function(){
		$(document).on('click','#moveEndDate', function(e){
			console.log('move end date listener fired');
			var endDate = $('#ModalEND_DATE').val();
			var endDateWas = $('#endDateWas').val();
			var hrsPerWeek = $('#ModalHRS_PER_WEEK').val();
			var resourceReference = $('#ModalResourceReference').val();
			$('#moveEndDate').addClass('spinning').addClass('glyphicon');
			console.log(endDate + ":" + hrsPerWeek + ":" + endDateWas + ":" + resourceReference);
		    $.ajax({
		    	url: "ajax/moveEndDate.php",
		        type: 'POST',
		    	data: {endDate: endDate,
		    		   endDateWas : endDateWas,
		    		   hrsPerWeek : hrsPerWeek,
		    		   resourceReference : resourceReference },
		    	success: function(result){
		    		console.log(result);
					$('#moveEndDate').removeClass('spinning').removeClass('glyphicon');
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
			$('#confirmDuplicateRR').text($.trim($(this).data('reference')));
			$('#confirmDuplicateRFS').text($.trim($(this).data('rfs')));
			$('#confirmDuplicateType').text($.trim($(this).data('type')));
			$('#confirmDuplicateStart').text($.trim($(this).data('start')));
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
				    		addPlatformTypePrnCode(resultObj.resourceReference,$(this).data('parent'));
				    		ResourceRequest.table.ajax.reload();
							$('#resourceHoursModal').modal('hide');
				    		}
				    	});

		    		}
		    	});
		});
	},

	this.listenForSaveStatusChange = function(){
		$(document).on('click','#saveStatusChange', function(e){
			console.log('save status change triggered');

			var disabled = $('input:disabled');

			console.log(disabled);

			$(disabled).prop('disabled',false);
			var formData = $('#statusChangeForm').serialize();
			$(disabled).prop('disabled',true);

			console.log(formData);

		    $.ajax({
		    	url: "ajax/saveStatusChange.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		ResourceRequest.table.ajax.reload();
					$('#statusModal').modal('hide');
		    		}
		    	});
		});
	},



	this.listenForReportOne = function(){
		$(document).on('click','#reportOne', function(e){
			console.log('triggered report one');
			ResourceRequest.table.columns().visible(true,false);
			ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,13,14,16,17,18,19,21,22,23]).visible(false,false);
		    ResourceRequest.table.columns.adjust().draw(false);
			});
	},

	this.listenForReportTwo = function(){
		$(document).on('click','#reportTwo', function(e){
			console.log('triggered report Two');
		    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]).visible(true,false);
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
		    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,18,19,21,22,23,24,25,26,27]).visible(false,false);
		    ResourceRequest.table.columns.adjust().draw(false);
			});
	},


	this.listenForChangeStatus = function(){
		$(document).on('click','.changeStatus', function(e){

			console.log($(this));
			var resourceReference = $(this).data('reference');
			$('#statusChangeRR').val($.trim($(this).data('reference')));
			$('#statusChangeRfs').val($.trim($(this).data('rfs')));
			$('#statusChangePhase').val($.trim($(this).data('phase')));
			$('#statusChangePlatform').val($.trim($(this).data('platform')));
			$('#statusChangeStart').val($.trim($(this).data('start')));
			$('#statusChangeType').val($.trim($(this).data('type')));
			$('#statusModal').modal('show');

			var status = $(this).data('status') ;
			var statusId = '#statusRadio' + status.replace(' ','_');

			$(statusId).prop("checked", true).trigger("click");
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
	    console.log('setup columns');
	    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,20,21,23,24,25,26,27,28,30,31,32,33,34,35,36,37]).visible(false,false);
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
		console.log('build Resource Report');
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
	},


	this.initialiseDateSelect = function(){
		var startDate,
		endDate,

		startPicker = new Pikaday({
			firstDay:1,
//			disableDayFn: function(date){
//			    // Disable all but Monday
//			    return date.getDay() === 0 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 5 || date.getDay() === 6;
//			},
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
//			disableDayFn: function(date){
//				// Disable all but Monday
//				return date.getDay() === 0 || date.getDay() === 1 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 6;
//			},
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
});



