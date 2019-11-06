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


function ResourceRequest() {

	var table;
	var resourceNamesForSelect2 = [];
	
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
				$('#slipStartDate').attr('disabled',false);
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
	

	this.populateResourceDropDownWhenModalShown = function(){
		$('#resourceNameModal').on('shown.bs.modal', function(){
			console.log($('#resourceNameModal').find('select'));
			console.log($('#RESOURCE_NAME'));
			var currentResourceName = $('#currentResourceName').val();
			if (!$('#RESOURCE_NAME').hasClass("select2-hidden-accessible")){
				$('#RESOURCE_NAME')
				.select2({data : resourceNamesForSelect2})
				.val(currentResourceName)
				.trigger('change');
			} else {
				$('#RESOURCE_NAME')
				.val('')
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
			$('#RESOURCE_REFERENCE').val(resourceReference);	
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
    			$('#editHoursPhase').text($(dataDetails).data('phase'));
    			$('#editHoursCio').text($(dataDetails).data('cio'));
    			$('#editHoursService').text($(dataDetails).data('service'));
    			$('#editHoursSubService').text($(dataDetails).data('subservice'));
    			$('#editHoursResourceName').text($(dataDetails).data('resourcename'));
    			$('#ModalHRS_PER_WEEK').val($(dataDetails).data('hrs'));

    			var resourceRequest = new ResourceRequest();
				resourceRequest.initialiseEditHoursModalStartEndDates();    			
    			ModalstartPicker.setDate($(dataDetails).data('start'));
    			ModalendPicker.setDate($(dataDetails).data('end'));

				$('#slipStartDate').attr('disabled',true);				
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
	
	this.listenForChangePipelineLive = function(){
		$(document).on('change','#pipelineLive',function(){
			ResourceRequest.table.ajax.reload();
		});
	},
	
	this.listenForSelectSpecificRfs = function(){
		$(document).on('change','#selectRfs',function(){
			ResourceRequest.table.ajax.reload();
		});		
	},

	this.listenForSelectSpecificCtbService = function(){
		$(document).on('change','#ctbservice',function(){	
			ResourceRequest.table.ajax.reload();
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
			$(this).addClass('spinning').attr('disabled',true);
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
	                d.pipelineLive  = $('#pipelineLive').prop('checked');
	                d.rfsid = $('#selectRfs option:selected').val();
	                d.ctbservice = $('#ctbservice option:selected').val();
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
	            { data: "CIO"              ,defaultContent: "", visible:false },
	            { data: "LINK_TO_PGMP"     ,defaultContent: "", visible:false },
	            { data: "RFS_CREATOR"      ,defaultContent: "", visible:false },
	            { data: "RFS_CREATED_TIMESTAMP",defaultContent: "", visible:false },
	            { data: "ARCHIVE"          ,defaultContent: "", visible:false },
	            { data: "RFS_TYPE"         ,defaultContent: "", visible:false },
	            { data: "ILC_WORK_ITEM"    ,defaultContent: "", visible:false },
	            { data: "RFS_STATUS"       ,defaultContent: "", visible:false },
	            { data: "RESOURCE_REFERENCE",defaultContent: "", visible:false },
	            { data: "RFS"              ,defaultContent: "", visible:true, render: { _:'display', sort:'sort' }, },	           
	            { data: "PHASE"            ,defaultContent: "", visible:true },
	            { data: "CTB_SERVICE"      ,defaultContent: "", visible:true,  render: { _:'display', sort:'sort' }, },
	            { data: "CTB_SUB_SERVICE"  ,defaultContent: "", visible:false },
	            { data: "DESCRIPTION"      ,defaultContent: "", visible:true },
	            { data: "START_DATE"       ,defaultContent: "", visible:true,  render: { _:'display', sort:'sort' }, },
	            { data: "END_DATE"         ,defaultContent: "", visible:false },
	            { data: "HRS_PER_WEEK"     ,defaultContent: "", visible:true },
	            { data: "RESOURCE_NAME"    ,defaultContent: "", visible:true },
	            { data: "RR_CREATOR"       ,defaultContent: "", visible:false },
	            { data: "RR_CREATED_TIMESTAMP",defaultContent: "", visible:false },
	            { data: "CLONED_FROM"      ,defaultContent: "", visible:false },
	            { data: "STATUS"           ,defaultContent: "", visible:true },
	            { data: "RR"               ,defaultContent: "", visible:false },
	            { data: "MONTH_01"         ,defaultContent: "",visible:true},
	            { data: "MONTH_02"         ,defaultContent: "",visible:true},
	            { data: "MONTH_03"         ,defaultContent: "",visible:true},
	            { data: "MONTH_04"         ,defaultContent: "",visible:true},
	            { data: "MONTH_05"         ,defaultContent: "",visible:true},
	            { data: "MONTH_06"         ,defaultContent: "",visible:true},
	        	
	        ],
	    });
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
		var endDate;

		startPicker = new Pikaday({
			firstDay:1,
//			disableDayFn: function(date){
//			    // Disable all but Monday
//			    return date.getDay() === 0 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 5 || date.getDay() === 6;
//			},
			field: document.getElementById('InputSTART_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			minDate: new Date(),
			onSelect: function(date) {
				console.log(date);
				console.log(this.getMoment().format('Do MMMM YYYY'));
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				console.log(db2Value);
				console.log($('#START_DATE'));
				jQuery('#START_DATE').val(db2Value);
				startDate = this.getDate();
				console.log(startDate);
				updateStartDate();
			}
		});
		endPicker = new Pikaday({
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



}


$( document ).ready(function() {
	var resourceRequest = new ResourceRequest();
    resourceRequest.init();
});



