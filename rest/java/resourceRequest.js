/**
 *
 */

var ModalstartPicker;
var ModalendPicker;

var buttonCommon = {
		 exportOptions: {
            format: {
               body: function ( data, row, column, node ) {
               	console.log(data);
               //   return data ?  data.replace( /<br\s*\/?>/ig, "\n") : data ;
               return data ? data.replace( /<br\s*\/?>/ig, "\n").replace(/(&nbsp;|<([^>]+)>)/ig, "") : data ;
               //    data.replace( /[$,.]/g, '' ) : data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
               }
            }
        }
};

function formatResourceName(resource){	
	var symbol = resource.distance=='local' ? '' : '';
	var text = $("<span style='color:black' >&nbsp;" + resource.text + "</span><br/>&nbsp;&nbsp;" + resource.role + "<br/>&nbsp;&nbsp;<span style='color:silver'>" + resource.tribe + "<span>");	
	return text;
}


function ResourceRequest() {

	var table;
	var resourceNamesForSelect2 = [];
	var ModalendEarlyPicker;
	
	this.initialiseEditHoursModalStartEndDates = function(){
		console.log('initialiseEditHoursModalStartEndDates');
		ModalstartPicker = new Pikaday({
			firstDay:1,
			field: document.getElementById('InputModalSTART_DATE'),		
			format: 'D MMM YYYY',
			showTime: false,
			minDate: new Date(),
			onSelect: function() {
				console.log('selected start date');
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				$('#ModalSTART_DATE').val(db2Value);	
				$('#saveAdjustedHoursWithDelta').attr('disabled',true);	
				$('#saveAdjustedHours').attr('disabled',true);
				$('#moveStartDate').attr('disabled',false);
				this.setMaxDate(ModalendPicker.getMoment().toDate());
				
			}
		});
		
		ModalendPicker = new Pikaday({
			firstDay:1,
			field: document.getElementById('InputModalEND_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			minDate: new Date(),
			onSelect: function() {
				console.log('selected end date');
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				$('#ModalEND_DATE').val(db2Value);	
				$('#saveAdjustedHoursWithDelta').attr('disabled',true);	
				$('#saveAdjustedHours').attr('disabled',true);
				$('#moveEndDate').attr('disabled',false);
			}
		});
	},
	

	this.init = function(){
		console.log('+++ Function +++ ResourceRequest.init');
		console.log('--- Function --- ResourceRequest.init');
	},

	this.listenForDeleteRecord = function(){
		$(document).on('click','.deleteRecord', function(e){
			$(this).attr('disabled',true).addClass('spinning');
			var resourceReference = $(this).data('reference');
			var platform = $(this).data('platform');
			var type = $(this).data('type');
			var rfs = $(this).data('rfs');

			$('#deleteResourceRef').val(resourceReference);

			var message = "<p>Please confirm you wish to delete Resource Reference :<b>" + resourceReference + "</b></p>";
			message += "<div class='container'>";
			message += "<div class='row'>";
			message += "<div class='col-sm-1'><b>RFS</b></div><div class='col-sm-11'>" + rfs + "</div>";			
			message += "</div>";
			message += "<div class='row'>";
			message += "<div class='col-sm-1'><b>Platform</b></div><div class='col-sm-11'>" + platform + "</div>";			
			message += "</div>";			
			message += "<div class='row'>";
			message += "<div class='col-sm-1'><b>Type</b></div><div class='col-sm-11'>" + type + "</div>";			
			message += "</div>";			
			message += "</div>";

			$('#deleteMessageBody').html(message);
			$('#confirmDeleteResource').attr('disabled',false);
			$('#confirmDeleteModal').modal('show');
		});
	},

	this.listenForConfirmedDelete = function(){
		$(document).on('click','#confirmDeleteResource', function(e){	
			$(this).attr('disabled',true).addClass('spinning');		
			var formData = $('#confirmDeleteModalForm').serialize();
		    $.ajax({
		    	url: "ajax/deleteResourceRequest.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		resultObj = JSON.parse(result);
		    		var message = "<h3>Record(s) deleted, you may now close this window</h3>";
		    		message += "<br/>Feedback from Delete : <small>" +resultObj.Messages + "</small>"
		    		$('#deleteMessageBody').html(message);
		    		$('#confirmDeleteResource').attr('disabled',true);
					$('.spinning').attr('disabled',false).removeClass('spinning');
		    		ResourceRequest.table.ajax.reload();
		    		}
		    });


		});
	},

	this.listenForEditRecord = function(){
		$(document).on('click','.editRecord', function(e){			
			$(this).addClass('spinning').attr('disabled',true);			
			$(this).prev('td.details-control').trigger('click');	
			
			var resourceReference = $(this).data('reference');
			
		    $.ajax({
		    	url: "ajax/getEditResourceForm.php",
		        type: 'POST',
		    	data: {resourceReference:resourceReference},
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		console.log(result);
		    		var resultObj = JSON.parse(result);		    		
		    		$('#editRequestModalBody').html(resultObj.form);
		    		$('#editRequestModal').modal('show');
		    	}
		    });			
			
		});
	},
	
	this.listenForEndEarly = function(){
		$(document).on('click','.endEarly', function(e){			
			$(this).addClass('spinning').attr('disabled',true);			
			var dataOwner = $(this).parent('.dataOwner');			$('#endEarlyRFS').val(dataOwner.data('rfs'));
			$('#endEarlyRR').val(dataOwner.data('resourcereference'));
			$('#endEarlyOrganisation').val(dataOwner.data('organisation'));
			$('#endEarlyService').val(dataOwner.data('service'));
			$('#endEarlyResource').val(dataOwner.data('resourcename'));
			$('#endEarlyInputEND_DATE').val(moment().format('D MMM YYYY'));
			$('#endEarlyEND_DATE').val(moment().format('YYYY-MM-DD'));
			$('#endEarlyEndWas').val(dataOwner.data('end'));
			$('#endEarlyStart_Date').val(dataOwner.data('startpika'));
			$('#endEarlyModal').modal('show');
		});
	},
	
	this.endEarlyModalShown = function(){
		$('#endEarlyModal').on('shown.bs.modal', function(){
			console.log($('#endEarlyInputEND_DATE'));
			console.log($('#endEarlyStart_Date').val());
			
			var startDateStr = $('#endEarlyStart_Date').val()
			
			var startDatePika = new Date(startDateStr);
			startDatePika.setDate(startDatePika.getDate() + 7); // The earliest End Date is 1 week after the Start Date.
			
			console.log(startDatePika);
			
			
			ResourceRequest.ModalendEarlyPicker = new Pikaday({
			firstDay:1,
			field: document.getElementById('endEarlyInputEND_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			maxDate: new Date(),
			minDate: startDatePika,
			onSelect: function() {
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				$('#endEarlyEND_DATE').val(db2Value);	

				}
			});
			console.log(ResourceRequest.ModalendEarlyPicker);
		});
	},
	
		this.endEarlyModalHidden = function(){
		$('#endEarlyModal').on('hidden.bs.modal', function(){
		    $('.spinning').removeClass('spinning').attr('disabled',false);
			$('#endEarlyRR').val('');
			$('#endEarlyOrganisation').val('');
			$('#endEarlyService').val('');
			$('#endEarlyResource').val('');
			$('#endEarlyInputEND_DATE').val('');
			$('#endEarlyEND_DATE').val('');		
			$('#endEarlyEndWas').val('');	
			console.log(ResourceRequest.ModalendEarlyPicker);
			ResourceRequest.ModalendEarlyPicker.destroy();
		});
	},
	
	
	this.listenForSaveEndEarly = function(){
		$(document).on('click','#endEarlyConfirmed', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			var resourceReference = $('#endEarlyRR').val();
			var endDate           = $('#endEarlyEND_DATE').val();
			var endDateWas        = $('#endEarlyEndWas').val();
			console.log(ResourceRequest.ModalendEarlyPicker);
			ResourceRequest.ModalendEarlyPicker.destroy(); 

		    $.ajax({
		    	url: "ajax/moveEndDate.php",
		        type: 'POST',
		    	data: {resourceReference:resourceReference,
					             endDate:endDate,
				              endDateWas:endDateWas },
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#endEarlyRR').val('');
					$('#endEarlyOrganisation').val('');
					$('#endEarlyService').val('');
					$('#endEarlyResource').val('');
					$('#endEarlyInputEND_DATE').val('');
					$('#endEarlyEND_DATE').val('');
					$('#endEarlyEndWas').val('');
					$('#endEarlyModal').modal('hide');
					ResourceRequest.table.ajax.reload();
			    	}
			    });		
			});
	},
	
	
	this.populateDiaryWhenModalShown = function(){
		$('#diaryModal').on('shown.bs.modal', function(){
			$('#diary').html('');
			var resourceReference = $('#RESOURCE_REFERENCE').val();
				$.ajax({
			    	url: "ajax/getDiaryForResourceReference.php",
			        type: 'POST',	
					data: {resourceReference : resourceReference },		
			    	success: function(result){
				   		var resultObj = JSON.parse(result);	
						$('#saveDiaryEntry').attr('disabled',false);
						$('#newDiaryEntry').html('').attr('contenteditable',true);
						$('#diary').html(resultObj.diary);
				}
			});		
		});	
	},

	this.populateResourceDropDownWhenModalShown = function(){
		$('#resourceNameModal').on('shown.bs.modal', function(){
			$('#RESOURCE_NAME').attr('disabled',true);
			$('#saveResourceName').addClass('spinning');
			var businessUnit = $('#businessUnit').val();
			var currentResourceName = $('#currentResourceName').val();	
			if (!resourceNamesForSelect2.length){	
				$.ajax({
			    	url: "ajax/getVbacActiveResourcesForSelect2.php",
			        type: 'POST',
                    data: {businessUnit : businessUnit},
			    	success: function(result){
					    		var resultObj = JSON.parse(result);
			    		resourceNamesForSelect2 = resultObj.data;
						$('#RESOURCE_NAME').select2({
							data          : resourceNamesForSelect2,
						    templateResult: formatResourceName
							}).val(currentResourceName).trigger('change');
						$('.spinning').removeClass('spinning');
						$('#RESOURCE_NAME').attr('disabled',false);
						$('#pleaseWaitMessage').html('');
			    		}
			    });	
			} else {
				$('.spinning').removeClass('spinning');
				$('#RESOURCE_NAME').val(currentResourceName).trigger('change');
				$('#RESOURCE_NAME').attr('disabled',false);
			}
		});
	},
	

	this.listenForEditResourceName = function(){
		$(document).on('click','.editResource', function(e){
			var dataOwner = $(this).parent('.dataOwner');
			$(this).addClass('spinning');
			var resourceReference = dataOwner.data('resourcereference');
			var resourceName      = dataOwner.data('resourceName');
			var parent            = dataOwner.data('parent');
			var businessUnit      = dataOwner.data('businessunit');
			
			$('#RESOURCE_REFERENCE').val(resourceReference);	
			$('#currentResourceName').val(resourceName);
			$('#businessUnit').val(businessUnit);
    		$('#resourceNameModal').modal('show');
			$('.spinning').removeClass('spinning');	
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
		    		var resultObj = JSON.parse(result);
		    		if(resultObj.success==true){
						$('#resourceNameForm').find('#RESOURCE_REFERENCE').val("");
						$('#resourceNameForm').find('#RESOURCE_NAME').val("").trigger('change');
						$('#resourceNameModal').modal('hide');
						$('.spinning').removeClass('spinning');
						ResourceRequest.table.ajax.reload();		    			
		    		} else {
		    			$('#errorMessageBody').html(resultObj.Messages);
		    			$('#resourceNameModal').modal('hide');
						$('.spinning').removeClass('spinning');
						ResourceRequest.table.ajax.reload();
		    			$('#errorMessageModal').modal('show');
		    		}

		    		}
		    });
		});
	},
	
	this.listenForClearResourceName = function(){
		$(document).on('click','#clearResourceName', function(e){
			$(this).addClass('spinning');
			var formData = $('#resourceNameForm').serialize();
			
			formData += "&clear=clear";
			
			console.log(formData);
			
			
		    $.ajax({
		    	url: "ajax/saveResourceName.php",
		        type: 'POST',
		    	data: formData, 
		    	success: function(result){
		    		console.log(result);
		    		var resultObj = JSON.parse(result);
		    		if(resultObj.success==true){
						$('#resourceNameForm').find('#RESOURCE_REFERENCE').val("");
						$('#resourceNameForm').find('#RESOURCE_NAME').val("").trigger('change');
						$('#resourceNameModal').modal('hide');
						$('.spinning').removeClass('spinning');
						ResourceRequest.table.ajax.reload();		    			
		    		} else {
		    			$('#errorMessageBody').html(resultObj.Messages);
		    			$('#resourceNameModal').modal('hide');
						$('.spinning').removeClass('spinning');
						ResourceRequest.table.ajax.reload();
		    			$('#errorMessageModal').modal('show');
		    		}

		    		}
		    });
		});
	},


	this.listenForEditHours = function(){
		$(document).on('click','.editHours', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			var dataDetails = $(this).parent('span');		
			
    		$('#resourceHoursModal').on('shown.bs.modal',function(){
    			$('#editHoursRfs').text($(dataDetails).data('rfs'));	
    			$('#editHoursPrn').text($(dataDetails).data('prn'));
    			$('#editHoursValueStream').text($(dataDetails).data('valuestream'));
    			$('#editHoursService').text($(dataDetails).data('service'));
    			$('#editHoursSubService').text($(dataDetails).data('subservice'));
    			$('#editHoursResourceName').text($(dataDetails).data('resourcename'));
    			$('#ModalHRS_PER_WEEK').val($(dataDetails).data('hrs'));
				

    			var resourceRequest = new ResourceRequest();
				resourceRequest.initialiseEditHoursModalStartEndDates();    			
    			
 				var edate = new Date($(dataDetails).data('end'));
				var sdate = new Date($(dataDetails).data('start'));
				
				ModalstartPicker.setDate($(dataDetails).data('start'));
				ModalstartPicker.setMaxDate(edate);
				
				ModalendPicker.setDate($(dataDetails).data('end'));
				ModalendPicker.setMinDate(sdate);

				$('#endDateWas').val($('#ModalEND_DATE').val());				
				$('#startDateWas').val($('#ModalSTART_DATE').val());

				$('#moveStartDate').attr('disabled',true);				
				$('#moveEndDate').attr('disabled',true);			
				$('#resourceHoursModal').off('shown.bs.modal');				
    		});

    		var resourceReference = $(dataDetails).data('resourcereference');
			$('#resourceHoursForm').find('#RESOURCE_REFERENCE').val(resourceReference);
			$('#messageArea').html("<div class='col-sm-4'></div><div class='col-sm-4'><h4>Form loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4><br/><small>This may take a few seconds</small></div><div class='col-sm-4'></div>");
			$.ajax({
		    	url: "ajax/contentsOfEditHoursModal.php",
		        type: 'POST',
		    	data: {	resourceReference: resourceReference  	},
		    	success: function(result){		    	
		    		resultObj = JSON.parse(result);
		    		$('#messageArea').html("");
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		$('#editResourceHours').html(resultObj.editResourceHours);
		    		$('#editResourceHoursFooter').html(resultObj.editResourceHoursFooter);
		    		$('#resourceHoursModal').modal('show');		    	}
		    	});			
			});
	},

	this.listenForSlipStartDate = function(){
		console.log('listener being set');
		$(document).on('click','#slipStartDate', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			console.log('listener fired');
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/slipResourceHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
				    $('#editResourceHours').html('');
					$('#resourceHoursModal').modal('hide');
		    		ResourceRequest.table.ajax.reload();
		    		}
		    });
		});
	},

	this.listenForMoveEndDate = function(){
		$(document).on('click','#moveEndDate', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			console.log('move end date listener fired');
			var endDate = $('#ModalEND_DATE').val();
			var endDateWas = $('#endDateWas').val();
			var hrsPerWeek = $('#ModalHRS_PER_WEEK').val();
			var resourceReference = $('#ModalResourceReference').val();			
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
		    		$('.spinning').removeClass('spinning').attr('disabled',false);					
				    $('#editResourceHours').html('');
					$('#resourceHoursModal').modal('hide');
		    		ResourceRequest.table.ajax.reload();
		    		}
		    });
		});
	},
	
	this.listenForMoveStartDate = function(){
		$(document).on('click','#moveStartDate', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			console.log('move start date listener fired');
			var startDate = $('#ModalSTART_DATE').val();
			var startDateWas = $('#startDateWas').val();
			var hrsPerWeek = $('#ModalHRS_PER_WEEK').val();
			var resourceReference = $('#ModalResourceReference').val();			
			console.log(startDate + ":" + hrsPerWeek + ":" + startDateWas + ":" + resourceReference);
		    $.ajax({
		    	url: "ajax/moveStartDate.php",
		        type: 'POST',
		    	data: {startDate: startDate,
		    		   startDateWas : startDateWas,
		    		   hrsPerWeek : hrsPerWeek,
		    		   resourceReference : resourceReference },
		    	success: function(result){
		    		console.log(result);
		    		$('.spinning').removeClass('spinning').attr('disabled',false);					
				    $('#editResourceHours').html('');
					$('#resourceHoursModal').modal('hide');
		    		ResourceRequest.table.ajax.reload();
		    		}
		    });
		});
	},



	this.listenForReinitialise = function(){
		console.log('listener being set');
		$(document).on('click','#reinitialise', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			console.log('listener fired');
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/reinitialiseHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
		    		$('#editResourceHours').html('<p></p>');
				    $('#resourceHoursModal').modal('hide');
		    		}
		    });
		});
	},

	this.listenForDuplicateResource = function(){
		$(document).on('click','.requestDuplication', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			$('#confirmDuplicateRR').text($.trim($(this).data('reference')));
			$('#confirmDuplicateRFS').text($.trim($(this).data('rfs')));
			$('#confirmDuplicateType').text($.trim($(this).data('type')));
			$('#confirmDuplicateStart').text($.trim($(this).data('start')));			
			$('#confirmDuplicationModal').modal('show');			
		});
	},


	this.listenForConfirmedDuplication = function(){
		$(document).on('click','#duplicationConfirmed', function(e){			
			$(this).addClass('spinning').attr('disabled',true);
			var resourceReference = $('#confirmDuplicateRR').text();
		    $.ajax({
		    	url: "ajax/duplicateResource.php",
		        type: 'POST',
		    	data: { resourceReference : resourceReference,
		    			delta: false,
		    			},
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#confirmDuplicationModal').modal('hide');
					
		    		ResourceRequest.table.ajax.reload();
		    		console.log(result);
		    		}
		    	});
		});
	},
	
	this.listenForSaveDiaryEntry = function(){
		$(document).on('click','#saveDiaryEntry', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			console.log('save diaryEntry triggered');
			var newDiaryEntry = $('#newDiaryEntry').html();
			var resourceReference = $('#RESOURCE_REFERENCE').val();
		    $.ajax({
		    	url: "ajax/saveDiaryEntry.php",
		        type: 'POST',
		    	data: {newDiaryEntry : newDiaryEntry,
 					   resourceReference : resourceReference },
		    	success: function(result){
		    		console.log(result);
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
					$('#RESOURCE_REFERENCE').val('');
					$('#newDiaryEntry').html('');
					$('#diaryModal').modal('hide');
		    		}
		    	});
		});
	},
	
	
	

	this.listenForSaveAdjustedHours = function(){
		$(document).on('click','#saveAdjustedHours', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			console.log('save adjusted triggered');
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/saveAdjustedHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
					$('#resourceHoursModal').modal('hide');
		    		}
		    	});
		});
	},

	this.listenForSaveAdjustedHoursWithDelta = function(){
		$(document).on('click','#saveAdjustedHoursWithDelta', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			// First create a duplicate Record.
			var resourceReference = $('#ModalResourceReference').val();
			var formData = $('#resourceHoursForm').serialize();
			console.log(resourceReference);
		    $.ajax({
		    	url: "ajax/duplicateResource.php",
		        type: 'POST',
		    	data: { resourceReference : resourceReference,
                        delta: true,
                        },
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
				    		$('.spinning').removeClass('spinning').attr('disabled',false);
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
			$(this).addClass('spinning').attr('disabled',true);
			var disabled = $('input:disabled');
			$(disabled).prop('disabled',false);
			var formData = $('#statusChangeForm').serialize();
			$(disabled).prop('disabled',true);
		    $.ajax({
		    	url: "ajax/saveStatusChange.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		console.log(result);
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
					$('#statusModal').modal('hide');
		    		}
		    	});
		});
	},
	
	this.listenForChangingHours = function (){
		$(document).on('focus','input[type=number]',function(){
			$('#saveAdjustedHoursWithDelta').attr('disabled',false);
			$('#saveAdjustedHours').attr('disabled',false);
			$('#slipStartDate').attr('disabled',true);
			$('#moveEndDate').attr('disabled',true);
		});
	},
	
	this.listenForChangePipelineLiveArchive = function(){
		$(document).on('change','input:radio[name=pipelineLiveArchive]',function(){			
			var cookieName = this.dataset.cookie;
			$('input:radio[name=pipelineLiveArchive]').each(function(index, element){
				var cookieName = element.dataset.cookie;
				document.cookie = cookieName + "=;" + "path=/;max-age=604800;samesite=lax;"; 
				});
			document.cookie = cookieName + "=checked;path=/;max-age=604800;samesite=lax;"; 
			ResourceRequest.table.ajax.reload();
		});
	},
	
	this.listenForSelectSpecificRfs = function(){
		$(document).on('change','#selectRfs',function(){	
			var rfs = $('#selectRfs option:selected').val();			
			document.cookie = "selectedRfs=" + rfs + ";" + "path=/;max-age=604800;samesite=lax;"; 										
			ResourceRequest.table.ajax.reload();
		});		
	},

	this.listenForSelectOrganisation = function(){
		$(document).on('change','#selectOrganisation',function(){		
			var org = $('#selectOrganisation option:selected').val();			
			document.cookie = "selectedOrganisation=" + org + ";" + "path=/;max-age=604800;samesite=lax;"; 	
			$('#selectRfs').val('').trigger('change');		
		});		
	},
	
		this.listenForSelectBusinessUnit = function(){
		$(document).on('change','#selectBusinessUnit',function(){			
			var org = $('#selectBusinessUnit option:selected').val();			
			document.cookie = "selectedBusinessUnit=" + org + ";" + "path=/;max-age=604800;samesite=lax;";
			$('#selectRfs').val('').trigger('change');	 // This will trigger the report to reload, so we don't have to. 
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
	
	this.listenForUnallocated = function(){
		$(document).on('click','#unallocated', function(e){		
		    ResourceRequest.table.column(27).search('New').draw();
			});
	},
	
	this.listenForCompleteable = function(){
		$(document).on('click','#completeable', function(e){		
		    ResourceRequest.table.column(27).search('Assigned.').draw();
			});
	},
	
	this.listenForPlannedOnly = function(){
		$(document).on('click','#plannedOnly', function(e){		
		    ResourceRequest.table.column(20).search('Planned').draw();
			});
	},
	
	this.listenForActiveOnly = function(){
		$(document).on('click','#activeOnly', function(e){		
		    ResourceRequest.table.column(20).search('Active').draw();
			});
	},

	this.listenForRemovePassed = function(){
		$(document).on('click','#removePassed', function(e){
			 ResourceRequest.table.column(20).search("").column(23).search("").column(27).search("");
			$.fn.dataTable.ext.search.push(
    			function( settings, data, dataIndex ) {
					console.log('pushing 1');
			        if (data[20].includes('Completed')  ){
            			return false;
        			}
        			return true;
    			}
			);
		   ResourceRequest.table.draw();
			$.fn.dataTable.ext.search.pop();
		});
	},
	
	
	// $('#example').DataTable({"iDisplayLength": 100, "search": {regex: true}}).column(1).search("backlog|Stretch|Solid|NIR", true, false ).draw(); 

	this.listenForResetReport = function(){
		$(document).on('click','#resetReport', function(e){
			ResourceRequest.table.columns().visible(true,false);
		    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,18,21,24,25,26,28]).visible(false,false);
		    ResourceRequest.table.columns.adjust().column(20).search("").column(23).search("").column(27).search("").draw(false);
		})			
	},


	this.listenForChangeStatus = function(){
		$(document).on('click','.changeStatus', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			console.log($(this));
			var resourceReference = $(this).data('resourcereference');
			$('#statusChangeRR').val($.trim($(this).data('resourcereference')));
			$('#statusChangeRfs').val($.trim($(this).data('rfs')));
			$('#statusChangeService').val($.trim($(this).data('service')));
			$('#statusChangeStart').val($.trim($(this).data('start')));
			$('#statusChangeSub').val($.trim($(this).data('sub')));
			$('#statusModal').modal('show');

			var status = $(this).data('status') ;
			var statusId = '#statusRadio' + status.replace(' ','_');

			$(statusId).prop("checked", true).trigger("click");
		});
	},
	
	this.listenForChangeStatusCompleted = function(){
		$(document).on('click','.changeStatusCompleted', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			var statusChangeRR = $(this).data('resourcereference');
			var statusRadio = 'Completed';
		    $.ajax({
		    	url: "ajax/saveStatusChange.php",
		        type: 'POST',
		    	data: {statusChangeRR:statusChangeRR,
					   statusRadio:statusRadio},
		    	success: function(result){
		    		console.log(result);
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
		    		}
		    	});
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
	    	
	    	language: {
	    	      emptyTable: "Please select Organisation, Business Unit and/or RFS from dropdowns above"
	    	},
	    	ajax: {
	            url: 'ajax/populateResourceRequestHTMLTable.php',
	            data: function ( d ) {
	                d.pipelineLiveArchive  = $("input:radio[name=pipelineLiveArchive]:checked").val();
	                d.archiveLive  = $('#archiveLive').prop('checked');
	                d.rfsid = $('#selectRfs option:selected').val();
	                d.organisation = $('#selectOrganisation option:selected').val();
	                d.businessunit = $('#selectBusinessUnit option:selected').val();
	            },
	            type: 'POST',
	        }	,
	    	autoWidth: false,
	    	deferRender: true,
	    	processing: true,
	    	responsive: true,
	    	colReorder: true,
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
                    filename: 'REST_Export',
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
                },
                filename: 'REST_Export',
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
	        columns: [
	            { data: "RFS_ID"           ,defaultContent: "", visible:false },
	            { data: "PRN"              ,defaultContent: "", visible:false },
	            { data: "PROJECT_TITLE"    ,defaultContent: "", visible:false },
	            { data: "PROJECT_CODE"     ,defaultContent: "", visible:false },
	            { data: "REQUESTOR_NAME"   ,defaultContent: "", visible:false },
	            { data: "REQUESTOR_EMAIL"  ,defaultContent: "", visible:false },
	            { data: "VALUE_STREAM"     ,defaultContent: "", visible:false },
	            { data: "LINK_TO_PGMP"     ,defaultContent: "", visible:false },
	            { data: "RFS_CREATOR"      ,defaultContent: "", visible:false },
	            { data: "RFS_CREATED_TIMESTAMP",defaultContent: "", visible:false },
	            { data: "ARCHIVE"          ,defaultContent: "", visible:false },
	            { data: "RFS_TYPE"         ,defaultContent: "", visible:false },
	            { data: "ILC_WORK_ITEM"    ,defaultContent: "", visible:false },
	            { data: "RFS_STATUS"       ,defaultContent: "", visible:false },
	            { data: "BUSINESS_UNIT"    ,defaultContent: "", visible:false },
	            { data: "RESOURCE_REFERENCE",defaultContent: "", visible:false },
	            { data: "RFS"              ,defaultContent: "", visible:true, render: { _:'display', sort:'sort' }},	           
	            { data: "ORGANISATION"     ,defaultContent: "", visible:true,  render: { _:'display', sort:'sort' }, },
	            { data: "SERVICE"          ,defaultContent: "", visible:false },
	            { data: "DESCRIPTION"      ,defaultContent: "", visible:true },
	            { data: "START_DATE"       ,defaultContent: "", visible:true,  render: { _:'display', sort:'sort' }, },
	            { data: "END_DATE"         ,defaultContent: "", visible:false, render: { _:'display', sort:'sort' }, },
	            { data: "HRS_PER_WEEK"     ,defaultContent: "", visible:false, render: { _:'display', sort:'sort' }, },
	            { data: "RESOURCE_NAME"    ,defaultContent: "", visible:true },
	            { data: "RR_CREATOR"       ,defaultContent: "", visible:false },
	            { data: "RR_CREATED_TIMESTAMP",defaultContent: "", visible:false },
	            { data: "CLONED_FROM"      ,defaultContent: "", visible:false },
	            { data: "STATUS"           ,defaultContent: "", visible:true },
	            { data: "RATE_TYPE"        ,defaultContent: "", visible:false },
	            { data: "HOURS_TYPE"        ,defaultContent: "", visible:false },
	            { data: "RR"               ,defaultContent: "", visible:false },
//	            { data: "MONTH_01"         ,defaultContent: "",visible:true},
//	            { data: "MONTH_02"         ,defaultContent: "",visible:true},
//	            { data: "MONTH_03"         ,defaultContent: "",visible:true},
//	            { data: "MONTH_04"         ,defaultContent: "",visible:true},
//	            { data: "MONTH_05"         ,defaultContent: "",visible:true},
//	            { data: "MONTH_06"         ,defaultContent: "",visible:true},
	        	
	        ]
	    });       

	    // Apply the search
	    $(ResourceRequest.table.column(16).header()).text('RFS:RR');


		console.log(ResourceRequest.table.column());

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
	    
	    ResourceRequest.table.on( 'responsive-display', function () {
	    	console.log('responsive display');
	    	restrictButtonAccess();
	    });

	},

	this.buildResourceReport =  function(){
		console.log('build Resource Report');
		var formData = $('form').serialize();
		var resourceRequest = new ResourceRequest();

	    $.ajax({
	    	url: "ajax/createResourceReportHTMLTable.php",
	        type: 'POST',
	        serverside: true,
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


	this.initialiseDateSelect = function(allowPast = false){
		var endDate;
				
		var minDate = allowPast ? new Date(2000,1,1) : new Date();

		var startPicker = new Pikaday({
			firstDay:1,
			field: document.getElementById('InputSTART_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			minDate: minDate,
			onSelect: function(date) {
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				console.log(db2Value);
				console.log($('#START_DATE'));
				jQuery('#START_DATE').val(db2Value);
				startDate = this.getDate();
				console.log(startDate);
				updateStartDate();
			}
		});
		var endPicker = new Pikaday({
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

		updateStartDate = function() {
			var resourceRequest = new ResourceRequest();
			console.log('updatedStartDate');
		    startPicker.setStartRange(startDate);
		    endPicker.setStartRange(startDate);
		    endPicker.setMinDate(startDate);
		    console.log($('#START_DATE').val());
		    resourceRequest.destroyResourceReport();
		    resourceRequest.buildResourceReport();
		};

		updateEndDate = function() {
			var resourceRequest = new ResourceRequest();
			console.log('updatedEndDate');
		    startPicker.setEndRange(endDate);
		    startPicker.setMaxDate(endDate);
		    endPicker.setEndRange(endDate);
		    resourceRequest.destroyResourceReport();
		    resourceRequest.buildResourceReport();
		};


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
	
	this.prepareRfsSelect = function(){
		$('#selectRfs').select2({
			  ajax: {
				url: 'ajax/populateSelectRfsForRR.php',
				dataType: 'json',
				data: function (params) {
					var pipelineLiveArchive  = $("input:radio[name=pipelineLiveArchive]:checked").val();
					var organisation = $('#selectOrganisation option:selected').val();
      				return { pipelineLiveArchive : pipelineLiveArchive, term: params.term, organisation: organisation 	};
    			},	
		 	},	  
		})
	}
	
	this.listenForBtnDiaryEntry = function(){
		$(document).on('click','.btnOpenDiary', function(e){	
			
			$('#RESOURCE_REFERENCE').val($(this).data('reference'));
			$('#organisation').val($(this).data('organisation'));
			$('#request').val($(this).data('reference'));
			$('#rfs').val($(this).data('rfs'));
			$('#newDiaryEntry').html('').attr('contenteditable',false);	
			$('#saveDiaryEntry').attr('disabled',true);	
			
			$('#diaryModal').modal('show');	
		    
		});
	},
	
	
	
	
	this.listenForResourceRequestEditShown = function(){
		$(document).on('shown.bs.modal',function(e){
		$( "#resourceRequestForm" ).submit(function( event ) {
			$('#resourceRequestForm :submit').addClass('spinning').attr('disabled',true);
			var url = 'ajax/saveResourceRecord.php';
			var disabledFields = $(':disabled');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#resourceRequestForm").serialize();
			$(disabledFields).attr('disabled',true);
			
			$.ajax({
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
				   $('#editRequestModal').modal('hide');	
	               var responseObj = JSON.parse(response);
	               var resourceRef =  "<p>Resource Ref:" + responseObj.resourceReference + "</p>";
	               var savedResponse =  "<p>Saved:" + responseObj.saveResponse +  "</p>";
	               var hoursResponse =  "<p>" + responseObj.hoursResponse +  "</p>";
	               var messages =  "<p>" + responseObj.Messages +  "</p>";

					$('.spinning').removeClass('spinning').attr('disabled',false);
					ResourceRequest.table.ajax.reload();

	                $('#recordSaveDiv').html(resourceRef + savedResponse + hoursResponse + messages);
	                $('#recordSavedModal').modal('show');
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
	        		FormClass.displayAjaxError('<p>Ajax call has errored.</p><p>URL:"' + url + '"</p><p>Error Status:"' + error.statusText + '"</p>');
	        		jQuery('.slaSave').html('Save').prop('disable',true );
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');

	      	}
		});
	event.preventDefault();
	});		
		});
	}

}


$( document ).ready(function() {
	var resourceRequest = new ResourceRequest();
    resourceRequest.init();
});
