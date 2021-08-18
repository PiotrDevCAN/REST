/**
 *
 */

var ModalstartPicker;
var ModalendPicker;

var buttonCommon = {
	exportOptions: {
		format: {
			body: function ( data, row, column, node ) {
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

	this.applySearch = function(){
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
	}

	this.initialiseEditHoursModalStartEndDates = function(){
		ModalstartPicker = new Pikaday({
			firstDay:1,
			field: document.getElementById('InputModalSTART_DATE'),		
			format: 'D MMM YYYY',
			showTime: false,
			minDate: new Date(),
			onSelect: function() {
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				$('#ModalSTART_DATE').val(db2Value);	
				$('#saveAdjustedHoursWithDelta').attr('disabled',true);	
				$('#saveAdjustedHours').attr('disabled',true);
			//	$('#moveStartDate').attr('disabled',false);
				$('#reinitialise').attr('disabled',false);
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
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				$('#ModalEND_DATE').val(db2Value);	
				$('#saveAdjustedHoursWithDelta').attr('disabled',true);	
				$('#saveAdjustedHours').attr('disabled',true);
			//	$('#moveEndDate').attr('disabled',false);
				$('#reinitialise').attr('disabled',false);
			}
		});
	},
	
	this.init = function(){
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
			$('#confirmDeleteResource').attr('disabled',false);
		});
		$('#confirmDeleteModal').on('hidden.bs.modal', function () {
			$('.spinning').removeClass('spinning').attr('disabled',false);
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
					try {
						var resultObj = JSON.parse(result);
						var message = "<h3>Record(s) deleted, you may now close this window</h3>";
						message += "<br/>Feedback from Delete : <small>" +resultObj.messages + "</small>"
						$('#deleteMessageBody').html(message);
						$('#confirmDeleteResource').attr('disabled',true);
						var clickedButtons = $('.spinning');
						clickedButtons.removeClass('spinning');
						clickedButtons.not('#confirmDeleteResource').attr('disabled',false);					
						ResourceRequest.table.ajax.reload();
					} catch (e) {
		    			$('#errorMessageBody').html("<h2>Json call to delete resource request Failed.Tell Piotr</h2><p>"+e+"</p>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					}
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to delete resource request Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to delete resource request Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
		    });
		});
	},

	this.listenForEditRecord = function(){
		$(document).on('click','.editRecord', function(e){			
			$(this).addClass('spinning').attr('disabled',true);			
			$(this).prev('td.details-control').trigger('click');	
			
			var resourceReference = $(this).data('reference');
			$('#messageArea').html("<div class='col-sm-4'></div><div class='col-sm-4'><h4>Form loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4><br/><small>This may take a few seconds</small></div><div class='col-sm-4'></div>");
			
			// $('#messageModalBody').html("<h4>Form loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4><br/><small>This may take a few seconds</small>");
			// $('#messageModal').modal('show');

		    $.ajax({
		    	url: "ajax/getEditResourceForm.php",
		        type: 'POST',
		    	data: {resourceReference:resourceReference},
		    	success: function(result){
					// $('#messageModal').modal('hide');
					try {
						var resultObj = JSON.parse(result);
						$('#messageArea').html("");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#editRequestModalBody').html(resultObj.form);
						$('#editRequestModal').modal('show');
					} catch (e) {
						$('#errorMessageBody').html("<h2>Json call to delete resource request Failed.Tell Piotr</h2><p>"+e+"</p>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					}
		    	},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to get edit resource form Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to get edit resource form  Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

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
		
			var startDateStr = $('#endEarlyStart_Date').val()
			
			var startDatePika = new Date(startDateStr);
			startDatePika.setDate(startDatePika.getDate() + 7); // The earliest End Date is 1 week after the Start Date.
			
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
			ResourceRequest.ModalendEarlyPicker.destroy();
		});
	},
	
	this.listenForSaveEndEarly = function(){
		$(document).on('click','#endEarlyConfirmed', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			var resourceReference = $('#endEarlyRR').val();
			var endDate           = $('#endEarlyEND_DATE').val();
			var endDateWas        = $('#endEarlyEndWas').val();
			ResourceRequest.ModalendEarlyPicker.destroy(); 

		    $.ajax({
		    	url: "ajax/moveEndDate.php",
		        type: 'POST',
		    	data: {resourceReference:resourceReference,
					endDate:endDate,
					endDateWas:endDateWas
				},
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
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to move End Date Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to move End Date Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

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
					try {
						var resultObj = JSON.parse(result);	
						$('#saveDiaryEntry').attr('disabled',false);
						$('#newDiaryEntry').html('').attr('contenteditable',true);
						$('#diary').html(resultObj.diary);
					} catch (e) {
						$('#errorMessageBody').html("<h2>Json call to get diary for resource reference Failed.Tell Piotr</h2><p>"+e+"</p>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					}
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to get diary for resource reference Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to get diary for resource reference Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
			});
		});	
	},

	this.setFormParameters = function(resourceNames, resourceName){

		var employeeFound = false;
		var unlockForm = false;
		var messageForUser = '';
		
		if (resourceName === '') {
			unlockForm = true;
			messageForUser = 'Resource has been not allocated yet.';
			console.log(messageForUser);
		} else {
			for(var i=0; i<resourceNames.length; i++){
				if(resourceNames[i].id == resourceName){
					console.log("The search found in JSON Object");
					employeeFound = true;
					break;
				}
			}
			if(employeeFound == true){
				unlockForm = true;
				messageForUser = '';
				console.log('clear message');
			} else {
				unlockForm = false;
				messageForUser = 'Employee not found in dataset read from VBAC.';
				console.log(messageForUser);
			}
		}

		if(unlockForm == true){
			$('#RESOURCE_NAME').attr('disabled',true);
			$('#saveResourceName').attr('disabled',true);
			$('#clearResourceName').attr('disabled',true);
		} else {
			$('#RESOURCE_NAME').attr('disabled',false);
			$('#saveResourceName').attr('disabled',false);
			$('#clearResourceName').attr('disabled',false);
		}
		$('#pleaseWaitMessage').html(messageForUser);

	}

	this.populateResourceDropDownWhenModalShown = function(){
		$('#resourceNameModal').on('shown.bs.modal', function(){
			$('#RESOURCE_NAME').attr('disabled',true);
			$('#saveResourceName').addClass('spinning').attr('disabled',true);
			$('#clearResourceName').attr('disabled',true);
			var businessUnit = $.trim($('#businessUnit').val());
			var currentResourceName = $.trim($('#currentResourceName').val());	
			
			var resourceNamesForSelect2;
			var resourceRequest = new ResourceRequest();

			console.log(currentResourceName);
			console.log(resourceNamesForSelect2);
			console.log(typeof(resourceNamesForSelect2));
			
			if(typeof(resourceNamesForSelect2) === 'undefined' ){
				// make ajax call
				$.ajax({
			    	url: "ajax/getVbacActiveResourcesForSelect2.php",
			        type: 'POST',
                    data: {businessUnit : businessUnit},
			    	success: function(result){
						try {
							var resultObj = JSON.parse(result);
							resourceNamesForSelect2 = resultObj.data;
							$('#RESOURCE_NAME').select2({
								data          : resourceNamesForSelect2,
								templateResult: formatResourceName
							}).val(currentResourceName).trigger('change');
							
							$('.spinning').removeClass('spinning');
							resourceRequest.setFormParameters(resourceNamesForSelect2, currentResourceName);
						} catch (e) {
							$('#errorMessageBody').html("<h2>Json call to get Vbac active resources for select Failed.Tell Piotr</h2><p>"+e+"</p>");
							$('.spinning').removeClass('spinning').attr('disabled',true);
							$('#errorMessageModal').modal('show');
						}
					},
					fail: function(response){
						$('#errorMessageBody').html("<h2>Json call to get Vbac active resources for select Failed.Tell Piotr</h2>");
						$('.spinning').removeClass('spinning').attr('disabled',true);
						$('#errorMessageModal').modal('show');
					},
					error: function(error){
						//	handle errors here. What errors	            :-)!
						$('#errorMessageBody').html("<h2>Json call to get Vbac active resources for select Errored " + error.statusText + " Tell Piotr</h2>");
						$('.spinning').removeClass('spinning').attr('disabled',true);
						$('#errorMessageModal').modal('show');
					},
					always: function(){
	
					}
			    });
			} else {
				$('.spinning').removeClass('spinning');
				resourceRequest.setFormParameters(resourceNamesForSelect2, currentResourceName);
			}
		});
	},

	this.listenForEditResourceName = function(){
		$(document).on('click','.editResource', function(e){
			var dataOwner = $(this).parent('.dataOwner');
			$(this).addClass('spinning').attr('disabled',true);
			var resourceReference = dataOwner.data('resourcereference');
			var resourceName      = dataOwner.data('resourcename');
			var parent            = dataOwner.data('parent');
			var businessUnit      = dataOwner.data('businessunit');
			$('#RESOURCE_REFERENCE').val(resourceReference);	
			$('#currentResourceName').val(resourceName);
			$('#businessUnit').val(businessUnit);
    		$('#resourceNameModal').modal('show');
			$('.spinning').removeClass('spinning').attr('disabled',false);	
		});
	},

	this.listenForSaveResourceName = function(){
		$(document).on('click','#saveResourceName', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			$('#clearResourceName').attr('disabled',true);
			var formData = $('#resourceNameForm').serialize();
		    $.ajax({
		    	url: "ajax/saveResourceName.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		try {
						var resultObj = JSON.parse(result);
						if(resultObj.success==true){
							$('#resourceNameForm').find('#RESOURCE_REFERENCE').val("");
							$('#resourceNameForm').find('#RESOURCE_NAME').val("").trigger('change');
							$('#resourceNameModal').modal('hide');
							$('.spinning').removeClass('spinning');
							ResourceRequest.table.ajax.reload();		    			
						} else {
							$('#errorMessageBody').html(resultObj.messages);
							$('#resourceNameModal').modal('hide');
							$('.spinning').removeClass('spinning').attr('disabled',false);
							$('#clearResourceName').attr('disabled',false);
							ResourceRequest.table.ajax.reload();
							$('#errorMessageModal').modal('show');
						}
					} catch (e) {
						$('#errorMessageBody').html("<h2>Json call to save resource name Failed.Tell Piotr</h2><p>"+e+"</p>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					}
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to save resource name Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to save resource name Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
		    });
		});
	},
	
	this.listenForClearResourceName = function(){
		$(document).on('click','#clearResourceName', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			$('#saveResourceName').attr('disabled',true);
			var formData = $('#resourceNameForm').serialize();
			formData += "&clear=clear";
			$.ajax({
		    	url: "ajax/saveResourceName.php",
		        type: 'POST',
		    	data: formData, 
		    	success: function(result){
		    		try {
						var resultObj = JSON.parse(result);
						if(resultObj.success==true){
							$('#resourceNameForm').find('#RESOURCE_REFERENCE').val("");
							$('#resourceNameForm').find('#RESOURCE_NAME').val("").trigger('change');
							$('#resourceNameModal').modal('hide');
							$('.spinning').removeClass('spinning');
							ResourceRequest.table.ajax.reload();		    			
						} else {
							$('#errorMessageBody').html(resultObj.messages);
							$('#resourceNameModal').modal('hide');
							$('.spinning').removeClass('spinning').attr('disabled',false);
							$('#saveResourceName').attr('disabled',false);
							ResourceRequest.table.ajax.reload();
							$('#errorMessageModal').modal('show');
						}
					} catch (e) {
						$('#errorMessageBody').html("<h2>Json call to get contents of edit hours modal Failed.Tell Piotr</h2><p>"+e+"</p>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					}
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to clear resource name Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to clear resource name Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

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
				if ($(dataDetails).data('resourcename') != '' ) {
					var resourcenameText = $(dataDetails).data('resourcename');
				} else {
					var resourcenameText = 'Unallocated';
				}
				$('#editHoursResourceName').text(resourcenameText);
    			$('#editHoursType').text($(dataDetails).data('hrstype'));
				$('#ModalTOTAL_HOURS').val($(dataDetails).data('hrs'));			
				$('#originalTotalHours').val($(dataDetails).data('hrs'));
				$('#ModalHOURS_TYPE').val($(dataDetails).data('hrstype'));
				$('#ModalRATE_TYPE').val($(dataDetails).data('ratetype'));
					
    			var resourceRequest = new ResourceRequest();
				resourceRequest.initialiseEditHoursModalStartEndDates();    			
    			
 				var edate = new Date($(dataDetails).data('end'));
				var sdate = new Date($(dataDetails).data('start'));
				var rfsEndDate = new Date($(dataDetails).data('rfsenddate'));
				
				ModalstartPicker.setDate($(dataDetails).data('start'));
				ModalstartPicker.setMaxDate(edate);
				
				ModalendPicker.setDate($(dataDetails).data('end'));
				ModalendPicker.setMinDate(sdate);
				ModalendPicker.setMaxDate(rfsEndDate);

				$('#endDateWas').val($('#ModalEND_DATE').val());				
				$('#startDateWas').val($('#ModalSTART_DATE').val());

				$('#moveStartDate').attr('disabled',true);	// Not available until they change Start_Date value			
				$('#moveEndDate').attr('disabled',true);	// Not available until they change end_date value	
				$('#reinitialise').attr('disabled',false);  // Available by default.	
				$('#resourceHoursModal').off('shown.bs.modal');				
    		});

    		var resourceReference = $(dataDetails).data('resourcereference');
			$('#resourceHoursForm').find('#RESOURCE_REFERENCE').val(resourceReference);
			$('#messageArea').html("<div class='col-sm-4'></div><div class='col-sm-4'><h4>Form loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4><br/><small>This may take a few seconds</small></div><div class='col-sm-4'></div>");
			
			// $('#messageModalBody').html("<h4>Form loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4><br/><small>This may take a few seconds</small>");
			// $('#messageModal').modal('show');
			
			$.ajax({
		    	url: "ajax/contentsOfEditHoursModal.php",
		        type: 'POST',
		    	data: {	resourceReference: resourceReference },
		    	success: function(result){
		    		// $('#messageModal').modal('hide');
					try {
						var resultObj = JSON.parse(result);
						// $('#messageArea').html("");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#editResourceHours').html(resultObj.editResourceHours);
						$('#editResourceHoursFooter').html(resultObj.editResourceHoursFooter);
						$('#resourceHoursModal').modal('show');
					} catch (e) {
						$('#errorMessageBody').html("<h2>Json call to get contents of edit hours modal Failed.Tell Piotr</h2><p>"+e+"</p>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
		    			$('#errorMessageModal').modal('show');
					}
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to get contents of edit hours modal Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to get contents of edit hours modal Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
			});			
		});
	},

	this.listenForSlipStartDate = function(){
		$(document).on('click','#slipStartDate', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/slipResourceHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
				    $('#editResourceHours').html('');
					$('#resourceHoursModal').modal('hide');
		    		ResourceRequest.table.ajax.reload();
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to slip resource hours Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to slip resource hours Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
		    });
		});
	},

	this.listenForReinitialise = function(){
		$(document).on('click','#reinitialise', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/reinitialiseHours.php",
		        type: 'POST',
		    	data: formData,
				beforeSend: function(data) {
					// do the following before the save is started
				},
				success: function(result){		  
		    		try {
						var resultObj = JSON.parse(result);
						var hoursResponse =  "<p>" + resultObj.hoursResponse +  "</p>";
						var messages =  "<p><b>" + resultObj.messages +  "</b></p>";
						if(resultObj.success==true){
							$('#editResourceHours').html('<p></p>');
							$('#resourceHoursModal').modal('hide');

							$('.spinning').removeClass('spinning').attr('disabled',false);
							ResourceRequest.table.ajax.reload();

							$('#recordSaveDiv').html(hoursResponse + messages);
							$('#recordSavedModal').modal('show');
						} else {
							if(resultObj.hoursResponse != '') {
								$('#errorMessageBody').html(hoursResponse);
							} else {
								$('#errorMessageBody').html(messages);
							}
							$('#editResourceHours').html('<p></p>');
							$('#resourceHoursModal').modal('hide');

							$('.spinning').removeClass('spinning').attr('disabled',false);
							ResourceRequest.table.ajax.reload();
							
							$('#recordSaveDiv').html(hoursResponse + messages);
							$('#recordSavedModal').modal('show');
						}
					} catch (e) {
						$('#errorMessageBody').html("<h2>Json call to reinitialise hours Failed.Tell Piotr</h2><p>"+e+"</p>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					}
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to reinitialise hours Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to reinitialise hours Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

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
		$('#confirmDuplicationModal').on('hidden.bs.modal', function () {
			$('.spinning').removeClass('spinning').attr('disabled',false);
		});
	},

	this.listenForConfirmedDuplication = function(){
		$(document).on('click','#duplicationConfirmed', function(e){			
			$(this).addClass('spinning').attr('disabled',true);
			var resourceReference = $('#confirmDuplicateRR').text();
		    $.ajax({
		    	url: "ajax/duplicateResource.php",
		        type: 'POST',
		    	data: { 
					resourceReference : resourceReference,
					delta: false,
				},
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#confirmDuplicationModal').modal('hide');
					
		    		ResourceRequest.table.ajax.reload();
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to duplicate resource Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to duplicate resource Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
			});
		});
	},
	
	this.listenForSaveDiaryEntry = function(){
		$(document).on('click','#saveDiaryEntry', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			var newDiaryEntry = $('#newDiaryEntry').html();
			var resourceReference = $('#RESOURCE_REFERENCE').val();
		    $.ajax({
		    	url: "ajax/saveDiaryEntry.php",
		        type: 'POST',
		    	data: {newDiaryEntry : newDiaryEntry,
 					   resourceReference : resourceReference },
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
					$('#RESOURCE_REFERENCE').val('');
					$('#newDiaryEntry').html('');
					$('#diaryModal').modal('hide');
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to save diary entry Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to save diary entry Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
			});
		});
	},

	this.listenForSaveAdjustedHours = function(){
		$(document).on('click','#saveAdjustedHours', function(e){
			$(this).addClass('spinning').attr('disabled',true);
			$('#ModalTOTAL_HOURS').prop('disabled',false);
			var formData = $('#resourceHoursForm').serialize();
		    $.ajax({
		    	url: "ajax/saveAdjustedHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
					$('#resourceHoursModal').modal('hide');
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to save adjusted hours Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to save adjusted hours Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

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
			
			$.ajax({
		    	url: "ajax/saveAdjustedHours.php",
		        type: 'POST',
		    	data: formData,
		    	success: function(result){
						
					formDataPlus = formData + '&delta=true&resourceReference=' + resourceReference;			
		    		$.ajax({
						url: "ajax/duplicateResource.php",
		        		type: 'POST',
		    			data: formDataPlus,
		    			success: function(result){									
							ResourceRequest.table.ajax.reload();
							$('.spinning').removeClass('spinning').attr('disabled',false);
							$('#resourceHoursModal').modal('hide');
						},
						fail: function(response){
							$('#errorMessageBody').html("<h2>Json call to duplicate resource Failed.Tell Piotr</h2>");
							$('.spinning').removeClass('spinning').attr('disabled',false);
							$('#errorMessageModal').modal('show');
						},
						error: function(error){
							//	handle errors here. What errors	            :-)!
							$('#errorMessageBody').html("<h2>Json call to duplicate resource Errored " + error.statusText + " Tell Piotr</h2>");
							$('.spinning').removeClass('spinning').attr('disabled',false);
							$('#errorMessageModal').modal('show');
						},
						always: function(){
		
						}
					});	
				}
			});
		});
	},

	/*
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
	*/

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
			ResourceRequest.table.columns([17,18]).visible(false,false);
		    ResourceRequest.table.columns([22,24,25]).visible(true,false);
		    ResourceRequest.table.columns.adjust().draw(false);
		});
	},
	
	this.listenForUnallocated = function(){
		$(document).on('click','#unallocated', function(e){		
		    ResourceRequest.table.column(28).search('New').draw();
		});
	},
	
	this.listenForCompleteable = function(){
		$(document).on('click','#completeable', function(e){		
		    ResourceRequest.table.column(28).search('Assigned.').draw();
		});
	},
	
	this.listenForPlannedOnly = function(){
		$(document).on('click','#plannedOnly', function(e){		
		    ResourceRequest.table.column(21).search('Planned').draw();
		});
	},
	
	this.listenForActiveOnly = function(){
		$(document).on('click','#activeOnly', function(e){		
		    ResourceRequest.table.column(21).search('Active').draw();
		});
	},

	this.listenForRemovePassed = function(){
		$(document).on('click','#removePassed', function(e){
			ResourceRequest.table.column(21).search("").column(24).search("").column(28).search("");
			$.fn.dataTable.ext.search.push(
    			function( settings, data, dataIndex ) {
			        if (data[21].includes('Completed')  ){
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
		    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,19,22,23,25,26,27,29,30,31]).visible(false,false);
		    ResourceRequest.table.columns.adjust().column(21).search("").column(24).search("").column(28).search("").draw(false);
		})			
	},

	this.listenForChangeStatus = function(){
		$(document).on('click','.changeStatus', function(e){
			$(this).addClass('spinning').attr('disabled',true);
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
		    		$('.spinning').removeClass('spinning').attr('disabled',false);
		    		ResourceRequest.table.ajax.reload();
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to save status change Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to save status change Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

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
					// d.pipelineLiveArchive = $("input:radio[name=pipelineLiveArchive]:checked").val();
					d.pipelineLiveArchive = $('input[name="pipelineLiveArchive"]').val();
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
	            { data: "RFS_END_DATE"     ,defaultContent: "", visible:false },
	            { data: "RESOURCE_REFERENCE",defaultContent: "", visible:false },
	            { data: "RFS"              ,defaultContent: "", visible:true, render: { _:'display', sort:'sort' }},	           
	            { data: "ORGANISATION"     ,defaultContent: "", visible:true,  render: { _:'display', sort:'sort' }, },
	            { data: "SERVICE"          ,defaultContent: "", visible:false },
	            { data: "DESCRIPTION"      ,defaultContent: "", visible:true },
	            { data: "START_DATE"       ,defaultContent: "", visible:true,  render: { _:'display', sort:'sort' }, },
	            { data: "END_DATE"         ,defaultContent: "", visible:false, render: { _:'display', sort:'sort' }, },
	            { data: "TOTAL_HOURS"      ,defaultContent: "", visible:false, render: { _:'display', sort:'sort' }, },
	            { data: "RESOURCE_NAME"    ,defaultContent: "", visible:true , render: { _:'display', sort:'sort' }, },
	            { data: "RR_CREATOR"       ,defaultContent: "", visible:false },
	            { data: "RR_CREATED_TIMESTAMP",defaultContent: "", visible:false },
	            { data: "CLONED_FROM"      ,defaultContent: "", visible:false },
	            { data: "STATUS"           ,defaultContent: "", visible:true },
	            { data: "RATE_TYPE"        ,defaultContent: "", visible:true },
	            { data: "HOURS_TYPE"       ,defaultContent: "", visible:true },
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
	    	restrictButtonAccess();
	    });

	},

	this.buildResourceReport =  function(getColumnsFromAjax){
		var resourceRequest = new ResourceRequest();
		
		if(getColumnsFromAjax == null){
			var formData = $('form').serialize();
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
				},
				fail: function(response){
					$('#errorMessageBody').html("<h2>Json call to create resource report HTML table Failed.Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				error: function(error){
					//	handle errors here. What errors	            :-)!
					$('#errorMessageBody').html("<h2>Json call to create resource report HTML table Errored " + error.statusText + " Tell Piotr</h2>");
					$('.spinning').removeClass('spinning').attr('disabled',false);
					$('#errorMessageModal').modal('show');
				},
				always: function(){

				}
			});
		} else {
			resourceRequest.initialiseDataTable();
		}
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
				jQuery('#START_DATE').val(db2Value);
				startDate = this.getDate();
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
		    startPicker.setStartRange(startDate);
		    endPicker.setStartRange(startDate);
		    endPicker.setMinDate(startDate);
		    resourceRequest.destroyResourceReport();
		    resourceRequest.buildResourceReport();
		};

		updateEndDate = function() {
			var resourceRequest = new ResourceRequest();
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
					// var pipelineLiveArchive = $("input:radio[name=pipelineLiveArchive]:checked").val();
					var pipelineLiveArchive = $('input[name="pipelineLiveArchive"]').val();
					var organisation = $('#selectOrganisation option:selected').val();
      				return { pipelineLiveArchive : pipelineLiveArchive, term: params.term, organisation: organisation };
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
						// do the following before the save is started
					},
					success: function(response) {
						// do what ever you want with the server response if that response is "success"						
						try {
							var responseObj = JSON.parse(response);
							var resourceRef =  "<p>Resource Ref:" + responseObj.resourceReference + "</p>";
							var savedResponse =  "<p>Saved:" + responseObj.saveResponse +  "</p>";
							var hoursResponse =  "<p>" + responseObj.hoursResponse +  "</p>";
							var messages =  "<p><b>" + responseObj.messages +  "</b></p>";
							
							$('#editRequestModal').modal('hide');

							$('.spinning').removeClass('spinning').attr('disabled',false);
							ResourceRequest.table.ajax.reload();
	
							$('#recordSaveDiv').html(resourceRef + savedResponse + hoursResponse + messages);
							$('#recordSavedModal').modal('show');	
						} catch (e) {
							$('#errorMessageBody').html("<h2>Json call to save resource record Failed.Tell Piotr</h2><p>"+e+"</p>");
							$('.spinning').removeClass('spinning').attr('disabled',false);
							$('#errorMessageModal').modal('show');
						}
					},
					fail: function(response){
						$('#errorMessageBody').html("<h2>Json call to save resource record Failed.Tell Piotr</h2>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					},
					error: function(error){
						//	handle errors here. What errors	            :-)!
						$('#errorMessageBody').html("<h2>Json call to save resource record Errored " + error.statusText + " Tell Piotr</h2>");
						$('.spinning').removeClass('spinning').attr('disabled',false);
						$('#errorMessageModal').modal('show');
					},
					always: function(){

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