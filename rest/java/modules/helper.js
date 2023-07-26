/**
 *
 */

let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');
let KnownRfs = await cacheBustImport('./modules/dataSources/knownRfs.js');
let KnownRfsPcr = await cacheBustImport('./modules/dataSources/knownRfsPcr.js');

class Helper {

	constructor() {
		$.fn.dataTable.ext.errMode = 'none';
		// $.fn.dataTable.ext.errMode = 'throw';

		this.listenForMessageModallShown();
		this.listenForMessageModallHidden();
		this.listenForErrorMessageModalShown();
		this.listenForErrorMessageModalHidden();
		this.listenForSaveResultModallShown();
		this.listenForSaveResultModallHidden();
	}

	/*
	* message modal
	*/

	listenForMessageModallShown() {
		$(document).on('shown.bs.modal', '#messageModal', function (e) {
			$(this).css("z-index", "999999");
		});
	}

	listenForMessageModallHidden() {
		$(document).on('hidden.bs.modal', '#messageModal', function (e) {
			$('#messageBody').html("");
		});
	}

	displayMessageModal(text) {
		$('#messageBody').html(text);
		$('#messageModal').modal('show');
	}

	/*
	* errorMessage modal
	*/

	listenForErrorMessageModalShown() {
		$(document).on('shown.bs.modal', '#errorMessageModal', function (e) {
			$(this).css("z-index", "999999");
		});
	}

	listenForErrorMessageModalHidden() {
		$(document).on('hidden.bs.modal', '#errorMessageModal', function (e) {
			$('#errorMessageBody').html("");
		});
	}

	displayErrorMessageModal(text) {
		$('#errorMessageBody').html(text);
		$('#errorMessageModal').modal('show');
	}

	displayTellDevMessageModal(error) {
		var message = "<h2>Json call failed.Tell Piotr</h2><p>" + error + "</p>";
		$('#errorMessageBody').html(message);
		$('#errorMessageModal').modal('show');
	}

	/*
	* saveResult modal
	*/

	listenForSaveResultModallShown() {
		$(document).on('shown.bs.modal', '#saveResultModal', function (e) {
			// $(this).css("z-index", "999999");
		});
	}

	listenForSaveResultModallHidden() {
		$(document).on('hidden.bs.modal', '#saveResultModal', function (e) {
			$('#saveResultModal .modal-body').html("");
		});
	}

	displaySaveResultModal(text) {
		$('#saveResultModal .modal-body').html(text);
		$('#saveResultModal').modal('show');
	}

	/*
	* deleteResult modal
	*/

	listenForDeleteResultModallShown() {
		$(document).on('shown.bs.modal', '#deleteResultModal', function (e) {
			// $(this).css("z-index", "999999");
		});
	}

	listenForDeleteResultModallHidden() {
		$(document).on('hidden.bs.modal', '#deleteResultModal', function (e) {
			$('#deleteResultModal .modal-body').html("");
		});
	}

	displayDeleteResultModal(text) {
		$('#deleteResultModal .modal-body').html(text);
		$('#deleteResultModal').modal('show');
	}

	lockButton(button) {
		$(button).addClass('spinning').attr('disabled', true);
	}

	unlockButton() {
		$('button.spinning').removeClass('spinning').attr('disabled', false);
	}

	offHighlight(el) {
		$(el).css("background-color", "#ffffff");
	}

	highlightOnGreen(el) {
		$(el).css("background-color", "LightGreen");
	}

	highlightOnRed(el) {
		$(el).css("background-color", "LightPink");
	}

	lockSubmitButton() {
		$(':submit').attr('disabled', true);
	}

	unlockSubmitButton() {
		$(':submit').attr('disabled', false);
	}

	async checkIfRfsIdExists(newRfsId) {
		var knownRfs = await KnownRfs.getRfses();
		if (typeof (knownRfs[newRfsId]) !== 'undefined') {
			return true;
		} else {
			return false;
		}
	}

	addRfsIdToKnown(newRfsId) {
		KnownRfs.addRef(newRfsId);
	}

	async checkIfRfsPcrIdExists(newRfsPcrId) {
		var knownRfsPcrs = await KnownRfsPcr.getRfsPcrs();
		if (typeof (knownRfsPcrs[newRfsPcrId]) !== 'undefined') {
			return true;
		} else {
			return false;
		}
	}

	addRfsPcrIdToKnown(newRfsId) {
		KnownRfsPcr.addRef(newRfsId);
	}

	getId(el) {
		var id = $(el).val().trim();
		return id;
	}

	checkIfRfsIsValid(rfsId) {
		// XXXX-XXX-000001
		var regex = RegExp('^[a-zA-Z]{4}-[a-zA-Z]{3}-[0-9]{6}$');
		var isValid = regex.test(rfsId);
		return isValid;
	}

	checkIfRfsIsSpecificType(rfsId, type) {
		// XXXX-type-000001
		var regex = RegExp('^[a-zA-Z]{4}(-' + type + '-)[0-9]{6}$');
		var isValid = regex.test(rfsId);
		return isValid;
	}

	addEventTitlesToPicker(events, titles) {
		events.forEach((date, index) => {
			var year = BankHolidays.getYearFromDate(date);
			var month = BankHolidays.getMonthFromDate(date);
			var day = BankHolidays.getDayFromDate(date);
			var title = titles[date];
			$('[data-pika-year="' + year + '"][data-pika-month="' + month + '"][data-pika-day="' + day + '"]').attr('title', title);
		});
	}
}

// Workaround to get textStatus from ajax request
$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
	jqXHR.fail(function (jqXHR, textStatus, errorThrown) {
		var message = options.url + ": ";
		var suppress = false;
		if (textStatus == 'parsererror') {
			message += "Parsing request has failed - " + errorThrown;
		} else if (errorThrown == 'timeout') {
			message += "Request time out.";
		} else if (errorThrown == 'abort') {
			message += "Request was aborted.";
			suppress = true;
		} else if (jqXHR.status === 0) {
			message += "No connection.";
			suppress = true;
		} else if (jqXHR.status) {
			message += "HTTP Error " + jqXHR.status + " - " + jqXHR.statusText + ".";
		} else {
			message += "Unknown error.";
		}
		console.warn(message);
		// console.log(options);
		// console.log(originalOptions);
		// console.error(message);
		if (suppress !== true) {
			//	handle errors here. What errors	            :-)!
			$('#errorMessageBody').html("<h2>Json call errored. Tell Piotr</h2><p>" + message + "</p>");
			helper.unlockButton();
			$(".modal").modal('hide');
			$('#errorMessageModal').modal('show');
		}
	});
});

// // Register a handler to be called when Ajax requests complete. This is an AjaxEvent.
// $(document).ajaxComplete(function( event, jqxhr, settings ) {
// 	console.log('ajaxComplete');
// });

// // Register a handler to be called when Ajax requests complete with an error. This is an Ajax Event.
// $(document).ajaxError(function( event, jqXHR, settings, thrownError ) {
// 	console.log('ajaxError');
// });

// Attach a function to be executed before an Ajax request is sent. This is an Ajax Event.
// $(document).ajaxSend(function( event, jqxhr, settings ) {
// 	console.log('ajaxSend');
// });

// // Register a handler to be called when the first Ajax request begins. This is an Ajax Event.
// $(document).ajaxStart(function( event, jqxhr, settings ) {
// 	console.log('ajaxStart');
// });

// // Register a handler to be called when all Ajax requests have completed. This is an Ajax Event.
// $(document).ajaxStop(function( event, jqxhr, settings ) {
// 	console.log('ajaxStop');
// });

// // Attach a function to be executed whenever an Ajax request completes successfully. This is an Ajax Event.
// $(document).ajaxSuccess(function( event, jqxhr, settings ) {
// 	console.log('ajaxSuccess');
// });

const helper = new Helper();

// make this module global
console.log('make "helper" module global');
window.helper = helper;

export { helper as default };