/**
 *
 */

let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');
let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRHoursBox {

	startPicker;
	endPicker;

	startDateDefault;
	endDateDefault;

	startDateMoment;  // Moment object
	endDateMoment;    // Moment object

	requestData;

	constructor() {
		// date - hours edit
		this.listenForResourceHoursModalShown();
		this.listenForResourceHoursModalHidden();
		this.listenForReinitialise();
		this.listenForEditHours();
		this.listenForSaveAdjustedHours();
		this.listenForSaveAdjustedHoursWithDelta();
		this.listenForHoursTypeEvents();
		this.listenForTotalHoursEvents();
		this.listenForHrsForWeekEvents();
		// removed functions
		// this.listenForSlipStartDate();
		// removed functions
		// date - hours edit
	}

	updateStartDate(date) {
		this.startPicker.setStartRange(date);
		this.endPicker.setStartRange(date);
		this.endPicker.setMinDate(date);
	}

	updateEndDate(date) {
		this.startPicker.setMaxDate(date);
		this.startPicker.setEndRange(date);
		this.endPicker.setEndRange(date);
	}

	forceHouseReInitialisation() {
		$('.hrsForWeek').prop('disabled', true);
		$('#reinitialise').attr('disabled', false);
		$('#saveAdjustedHours').attr('disabled', true);
		$('#saveAdjustedHoursWithDelta').attr('disabled', true);

		$.each($('.hrsForWeek'), function (key, element) {
			$(element).val('').attr('placeholder', 'Re-Initialise');
		});
	}

	makeResourceDuplication(data, modalId) {
		$.ajax({
			url: "ajax/duplicateResource.php",
			type: 'POST',
			data: data,
			success: function (result) {
				helper.unlockButton();
				$(modalId).modal('hide');

				ResourceRequest.table.ajax.reload();
			}
		});
	}

	async initialiseEditHoursModalStartEndDates() {

		let events = await BankHolidays.getFormattedEvents();
		let eventsRaw = await BankHolidays.getEvents();
		let eventTitles = await BankHolidays.getEventTitles();

		var $this = this;

		this.startPicker = new Pikaday({
			events: events,
			firstDay: 1,
			field: document.getElementById('InputModalSTART_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			// minDate: new Date(),
			onSelect: function () {
				$this.startDateMoment = this.getMoment();
				var db2Value = $this.startDateMoment.format('YYYY-MM-DD');
				$('#ModalSTART_DATE').val(db2Value);
				$this.updateStartDate($this.startDateMoment.toDate());
			},
			onClose: function () {
				var dateWas = $this.startDateDefault;
				var dateCurrent = $this.startDateMoment.format('YYYY-MM-DD');
				if (dateWas != dateCurrent) {
					if ($this.endDateDefault !== '') {
						if ($this.startDateMoment.isAfter($this.endDateMoment)) {
							// restore previous start date
							this.setDate(dateWas);
							dateCurrent = dateWas;
						}
					}
					$this.startDateDefault = dateCurrent;
				}
			},
			onDraw: function () {
				helper.addEventTitlesToPicker(eventsRaw, eventTitles);
			}
		});

		this.endPicker = new Pikaday({
			events: events,
			firstDay: 1,
			field: document.getElementById('InputModalEND_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			// minDate: new Date(),
			onSelect: function () {
				$this.endDateMoment = this.getMoment();
				var db2Value = $this.endDateMoment.format('YYYY-MM-DD');
				$('#ModalEND_DATE').val(db2Value);
				$this.updateEndDate($this.endDateMoment.toDate());
			},
			onClose: function () {
				var dateWas = $this.endDateDefault;
				var dateCurrent = $this.startDateMoment.format('YYYY-MM-DD');
				if (dateWas != dateCurrent) {
					if ($this.startDateDefault !== '') {
						if ($this.startDateMoment.isAfter($this.endDateMoment)) {
							// restore previous end date
							this.setDate(dateWas);
							dateCurrent = dateWas;
						}
					}
					$this.endDateDefault = dateCurrent;
				}
			},
			onDraw: function () {
				helper.addEventTitlesToPicker(eventsRaw, eventTitles);
			}
		});
	}

	setResourceHoursForm() {
		var data = this.requestData;
		$('#editHoursRfs').text(data.rfs);
		$('#editHoursPrn').text(data.prn);
		$('#editHoursValueStream').text(data.valuestream);
		$('#editHoursService').text(data.service);
		$('#editHoursSubService').text(data.subservice);
		var resourcenameText = '';
		if (data.resourcename != '') {
			resourcenameText = data.resourcename;
		} else {
			resourcenameText = 'Unallocated';
		}
		$('#editHoursResourceName').text(resourcenameText);
		$('#originalHoursType').text(data.hrstype);
		var hoursTypes = document.getElementsByName("HOURS_TYPE");
		var i;
		for (i = 0; i < hoursTypes.length; i++) {
			if (hoursTypes[i].value == data.hrstype) {
				hoursTypes[i].checked = true;
			}
		}
		$('#ModalTOTAL_HOURS').val(data.hrs);
		$('#originalTotalHours').val(data.hrs);
		$('#ModalHOURS_TYPE').val(data.hrstype);
		$('#ModalRATE_TYPE').val(data.ratetype);

		this.initialiseEditHoursModalStartEndDates()
			.then(() => {

				var data = this.requestData;

				var sdate = new Date(data.start);	// "15 Apr 2022"
				var edate = new Date(data.end);		// "15 Apr 2022"

				var rfsEndDate = new Date(data.rfsenddate);		// "15 Apr 2022"

				var sdateMoment = moment(data.start);
				var edateMoment = moment(data.end);

				this.startPicker.setDate(data.start);
				this.endPicker.setDate(data.end);

				this.updateStartDate(sdateMoment.toDate());
				this.updateEndDate(edateMoment.toDate());

				// $('#moveStartDate').attr('disabled', true); // Not available until they change Start_Date value			
				// $('#moveEndDate').attr('disabled', true); // Not available until they change end_date value	
				$('#reinitialise').attr('disabled', false); // Available by default.
			});
	}

	listenForResourceHoursModalShown() {
		$(document).on('shown.bs.modal', '#resourceHoursModal', function (e) {
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForResourceHoursModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#resourceHoursModal', function (e) {

			$this.startPicker.destroy();
			$this.endPicker.destroy();

			$('#HrsForWefForm').html("");
		});
	}

	listenForReinitialise() {
		$(document).on('click', '#reinitialise', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			var formData = $('#resourceHoursForm').serialize();
			$.ajax({
				url: "ajax/reinitialiseHours.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						var hoursResponse = "<p>" + resultObj.hoursResponse + "</p>";
						var messages = "<p><b>" + resultObj.messages + "</b></p>";
						if (resultObj.success == true) {
							$('#resourceHoursModal').modal('hide');
							helper.unlockButton();
							$('#recordSaveDiv').html(hoursResponse + messages);
							$('#recordSavedModal').modal('show');
							ResourceRequest.table.ajax.reload();
						} else {
							$('#resourceHoursModal').modal('hide');
							helper.unlockButton();
							if (resultObj.hoursResponse != '') {
								helper.displayErrorMessageModal(hoursResponse);
							} else {
								helper.displayErrorMessageModal(messages);
							}
							ResourceRequest.table.ajax.reload();
						}
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to reinitialise hours Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}

	prepareHrsElement(data) {

		let mainDiv = document.createElement('div');
		mainDiv.id = 'ModalHrsForWefFormGroup' + data.wef;
		mainDiv.className = 'form-group';
		mainDiv.setAttribute('style', 'background:' + data.stripe);

		let label = document.createElement('label');
		label.setAttribute('for', 'ModalHRSForWef' + data.wef);
		label.className = 'col-md-6 control-label';
		label.setAttribute('data-toggle', 'tooltip');
		label.setAttribute('data-placement', 'top');
		label.setAttribute('title', 'Hours for wef ' + data.wef);
		label.innerHTML = data.weekFormatted;

		let subDiv1 = document.createElement('div');
		subDiv1.className = 'col-md-3';

		let input1 = document.createElement('input');
		input1.setAttribute('type', 'number');
		input1.setAttribute('step', '0.01');
		input1.setAttribute('min', '0');
		input1.setAttribute('max', '1000');
		input1.className = 'form-control hrsForWeek';
		input1.id = 'ModalHRSForWef' + data.wef;
		input1.setAttribute('name', 'ModalHRSForWef' + data.wef);
		input1.setAttribute('value', data.hours);
		input1.setAttribute('placeholder', 'Hrs/Week');

		let input2 = document.createElement('input');
		input2.setAttribute('type', 'hidden');
		input2.setAttribute('name', 'ModalHRSForWas' + data.wef);
		input2.setAttribute('value', data.hours);

		let subDiv2 = document.createElement('div');
		subDiv2.className = 'col-md-3';
		let p = document.createElement('p');
		p.innerHTML = 'Claim: ' + data.claimMonth;

		subDiv1.appendChild(input1);
		subDiv1.appendChild(input2);

		subDiv2.appendChild(p);

		mainDiv.appendChild(label);
		mainDiv.appendChild(subDiv1);
		mainDiv.appendChild(subDiv2);

		return mainDiv;
	}

	listenForEditHours() {
		var $this = this;
		$(document).on('click', '.editHours', function (e) {
			ModalMessageArea.showMessageArea();
			$(this).addClass('spinning').attr('disabled', true);
			var dataDetails = $(this).parent('span');
			$this.requestData = dataDetails.data();

			var resourceReference = $this.requestData.resourcereference;
			$('#resourceHoursForm').find('#RESOURCE_REFERENCE').val(resourceReference);
			$('#resourceHoursForm').find('#ModalResourceReference').val(resourceReference);

			$.ajax({
				url: "ajax/contentsOfEditHoursModalJSON.php",
				type: 'POST',
				data: { resourceReference: resourceReference },
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						helper.unlockButton();

						// $('#editResourceHours').html(resultObj.editResourceHours);
						// $('#editResourceHoursFooter').html(resultObj.editResourceHoursFooter);
						$this.setResourceHoursForm();

						var hrsForm = document.getElementById('HrsForWefForm');
						var hrs = resultObj.data;
						hrs.forEach(element => {
							var mainDiv = $this.prepareHrsElement(element);
							hrsForm.appendChild(mainDiv);
						});

						$('#resourceHoursModal').modal('show');
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to get contents of edit hours modal Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}

	listenForSaveAdjustedHours() {
		$(document).on('click', '#saveAdjustedHours', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$('#ModalTOTAL_HOURS').prop('disabled', false);
			var formData = $('#resourceHoursForm').serialize();
			$.ajax({
				url: "ajax/saveAdjustedHours.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					helper.unlockButton();
					ResourceRequest.table.ajax.reload();
					$('#resourceHoursModal').modal('hide');
				}
			});
		});
	}

	listenForSaveAdjustedHoursWithDelta() {
		var $this = this;
		$(document).on('click', '#saveAdjustedHoursWithDelta', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$('#ModalTOTAL_HOURS').prop('disabled', false);

			// First create a duplicate Record.
			var resourceReference = $('#ModalResourceReference').val();
			var formData = $('#resourceHoursForm').serialize();
			var formDataPlus = formData + '&delta=true&resourceReference=' + resourceReference;

			$.ajax({
				url: "ajax/saveAdjustedHours.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						$this.makeResourceDuplication(formDataPlus, '#resourceHoursModal');
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to make Auto Delta Errored.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}

	// element removed
	/*
	listenForSlipStartDate() {
		$(document).on('click', '#slipStartDate', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			var formData = $('#resourceHoursForm').serialize();
			$.ajax({
				url: "ajax/slipResourceHours.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					$('#editResourceHours').html('<p></p>');
					$('#resourceHoursModal').modal('hide');
					ResourceRequest.table.ajax.reload();
				}
			});
		});
	}
	*/
	// element removed

	listenForHoursTypeEvents() {
		var $this = this;
		$(document).on('change', 'input[name^="HOURS_TYPE"]', function (e) {
			$this.forceHouseReInitialisation();
		});
	}

	listenForTotalHoursEvents() {
		var $this = this;
		$(document).on('keyup mouseup', '#ModalTOTAL_HOURS', function (e) {
			$this.forceHouseReInitialisation();
		});
	}

	listenForHrsForWeekEvents() {
		$(document).on('keyup mouseup', '.hrsForWeek', function (e) {
			$('#ModalTOTAL_HOURS').prop('disabled', true);
			$('#reinitialise').attr('disabled', true);
			$('#saveAdjustedHours').attr('disabled', false);

			var originalTotalHours = $('#originalTotalHours').val();
			var totalHours = 0;

			$.each($('.hrsForWeek'), function (key, element) {
				totalHours = (parseFloat(totalHours) + parseFloat(element.value)).toFixed(2);
			});

			$('#ModalTOTAL_HOURS').val(totalHours);

			$('#saveAdjustedHours').attr('data-original-title', '').tooltip('show').tooltip('hide');

			console.log(totalHours + ":" + originalTotalHours);
			console.log(parseFloat(totalHours) < parseFloat(originalTotalHours));

			if (parseFloat(totalHours) < parseFloat(originalTotalHours)) {
				$('#saveAdjustedHoursWithDelta').attr('disabled', false); // they can only Auto-Delta if they've hours to save somewhere else.
			} else {
				$('#saveAdjustedHoursWithDelta').attr('disabled', true);
			}
		});
	}
}

const resourceRequestHoursBox = new RRHoursBox();

export { resourceRequestHoursBox as default };