/**
 *
 */

let helper = await cacheBustImport('./modules/helper.js');
let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');
let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let rfsIdValidator = await cacheBustImport('./modules/validators/rfsId.js');
let StaticValueStreams = await cacheBustImport('./modules/dataSources/staticValueStreamsIds.js');
let Rfs = await cacheBustImport('./modules/rfs.js');

class entry {

	static formId = 'rfsForm';

	static startFieldId = 'RFS_START_DATE';
	static endFieldId = 'RFS_END_DATE';

	events;
	eventsRaw;
	eventTitles;

	endPicker;
	endDateMoment;    // Moment object

	responseObj;

	constructor() {
		this.initialiseDateFields();
		this.prepareSelect2();
		this.listenForRFS_IDInput();
		this.listenForRFS_IDFocusOut();
		this.listenForValueStreamChange();
		this.listenForFormReset();
		this.listenForRfsFormSubmit();
		this.listenForEditResultModalHidden();
	}

	initialiseDateFields() {

		var $this = this;
		let eventsPromise = BankHolidays.getFormattedEvents().then((response) => {
			$this.events = response;
		});
		let eventsRawPromise = BankHolidays.getEvents().then((response) => {
			$this.eventsRaw = response;
		});
		let eventTitlesPromise = BankHolidays.getEventTitles().then((response) => {
			$this.eventTitles = response;
		});

		const promises = [eventsPromise, eventsRawPromise, eventTitlesPromise];
		Promise.allSettled(promises)
			.then((results) => {
				$this.initialiseEndDate();
			});
	}

	initialiseEndDate() {

		var $this = this;
		this.endPicker = new Pikaday({
			events: $this.events,
			firstDay: 1,
			field: document.getElementById('InputRFS_END_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			minDate: new Date(),
			onSelect: function () {
				$this.endDateMoment = this.getMoment();
				var db2Value = $this.endDateMoment.format('YYYY-MM-DD');
				$('#RFS_END_DATE').val(db2Value);
			},
			onDraw: function () {
				helper.addEventTitlesToPicker($this.eventsRaw, $this.eventTitles);
			}
		});
	}

	prepareSelect2() {

		FormMessageArea.showMessageArea();

		let valueStreamsPromise = StaticValueStreams.getValueStreams().then((response) => {
			$("#VALUE_STREAM").select2({
				data: response,
				tags: true,
				createTag: function (params) {
					return undefined;
				}
			});
		});

		const promises = [valueStreamsPromise];
		Promise.allSettled(promises)
			.then((results) => {
				results.forEach((result) => console.log(result.status));
				FormMessageArea.clearMessageArea();
			});

		$(".select").select2({
			tags: true,
			createTag: function (params) {
				return undefined;
			}
		});
		// $(".select").select2();

		FormMessageArea.clearMessageArea();
	}

	listenForRFS_IDInput() {
		$(document).on('input', '#RFS_ID', async function (e) {
			await rfsIdValidator.validateId(this);
		});
	}

	listenForRFS_IDFocusOut() {
		$(document).on('focusout', '#RFS_ID', async function (e) {
			await Rfs.changeValueStreamIfUnique(this);
		});
	}

	listenForValueStreamChange() {
		var $this = this;
		$(document).on('change', '#VALUE_STREAM', function () {
			FormMessageArea.showMessageArea();
			var valueStream = $('#VALUE_STREAM option:selected').val();
			$.ajax({
				url: "ajax/getBusinessUnitByValueStreamId.php",
				type: 'POST',
				data: {
					valueStream: valueStream
				},
				success: function (result) {
					try {
						$('#BUSINESS_UNIT').val(result.businessUnit);
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to get business unit for value stream Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
					FormMessageArea.clearMessageArea();
				}
			});
		});
	}

	listenForFormReset() {
		$(document).on('reset', 'form', function (e) {
			$(".select").val('').trigger('change');
			$("#RFS_ID").val('').trigger('input');
		});
	}

	listenForEditResultModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#myModal', function (e) {
			// do something…
			if ($this.responseObj.create == true || $this.responseObj.update == true) {
				// reset form
				// $('#resetRfs').click();
				// $('#RFS_ID').css("background-color", "#ffffff");
				// reload form
				location.reload();
			} else {
				window.close();
			}
			helper.unlockButton();
		});
	}

	listenForRfsFormSubmit() {
		var $this = this;
		$(document).on('submit', '#' + entry.formId, function (event) {
			event.preventDefault();
			$(':submit').addClass('spinning').attr('disabled', true);
			var url = 'ajax/saveRfsRecord.php';
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#" + entry.formId).serialize();
			$(disabledFields).attr('disabled', true);
			$.ajax({
				type: 'post',
				url: url,
				data: formData,
				context: document.body,
				beforeSend: function (data) {
					// do the following before the save is started
				},
				success: function (result) {
					// do what ever you want with the server response if that response is "success"
					try {
						var responseObj = JSON.parse(result);
						$this.responseObj = responseObj;
						var rfsIdTxt = "<p><b>RFS ID: </b>" + responseObj.rfsId + "</p>";
						var savedResponse = responseObj.saveResponse;
						var span = '';
						if (savedResponse) {
							span = "<span>";
						} else {
							span = "<span style='color:red'>";
						}
						var savedResponseTxt = "<p>" + span + " <b>Record Saved: </b>" + savedResponse + "</span></p>";
						var messages = "<p><b>" + responseObj.messages + "</b></p>";
						helper.addRfsIdToKnown(responseObj.rfsId);
						helper.displaySaveResultModal(rfsIdTxt + savedResponseTxt + messages);
						$('.spinning').removeClass('spinning').attr('disabled', false);
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to save RFS record Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				},
				complete: function () {
					document.getElementById(entry.formId).reset();
				}
			});
		});
	}
}

const Entry = new entry();

$('#LINK_TO_PGMP').attr('required', false);

$('.typeahead').bind('typeahead:select', function (ev, suggestion) {
	$('.tt-menu').hide();
	$('#REQUESTOR_NAME').val(suggestion.value);
});