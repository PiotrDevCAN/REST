/**
 *
 */

let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');
let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRIndicateCompletedBox {

	constructor() {
		// indicate completed
		this.listenForEndEarly();
		this.listenForEndEarlyModalShown();
		this.listenForEndEarlyModalHidden();
		this.listenForSaveEndEarly();
		// indicate completed
	}

	listenForEndEarlyModalShown() {
		$(document).on('shown.bs.modal', '#endEarlyModal', async function (e) {
			ModalMessageArea.clearMessageArea();

			let events = await BankHolidays.getFormattedEvents();
			let eventsRaw = await BankHolidays.getEvents();
			let eventTitles = await BankHolidays.getEventTitles();

			var startDateStr = $('#endEarlyStart_Date').val();

			var startDatePika = new Date(startDateStr);
			startDatePika.setDate(startDatePika.getDate() + 7); // The earliest End Date is 1 week after the Start Date.

			ResourceRequest.ModalendEarlyPicker = new Pikaday({
				events: events,
				firstDay: 1,
				field: document.getElementById('endEarlyInputEND_DATE'),
				format: 'D MMM YYYY',
				showTime: false,
				maxDate: new Date(),
				minDate: startDatePika,
				onSelect: function () {
					var db2Value = this.getMoment().format('YYYY-MM-DD');
					$('#endEarlyEND_DATE').val(db2Value);

				},
				onDraw: function () {
					helper.addEventTitlesToPicker(eventsRaw, eventTitles);
				}
			});
		});
	}

	listenForEndEarlyModalHidden() {
		$(document).on('hidden.bs.modal', '#endEarlyModal', function (e) {
			helper.unlockButton();
			$('#endEarlyRR').val('');
			$('#endEarlyOrganisation').val('');
			$('#endEarlyService').val('');
			$('#endEarlyResource').val('');
			$('#endEarlyInputEND_DATE').val('');
			$('#endEarlyEND_DATE').val('');
			$('#endEarlyEndWas').val('');
			$('#endEarlyTotalHrs').val('');
			$('#endEarlyHrsPerWeek').val('');
			ResourceRequest.ModalendEarlyPicker.destroy();
		});
	}

	listenForSaveEndEarly() {
		$(document).on('click', '#endEarlyConfirmed', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			var resourceReference = $('#endEarlyRR').val();
			var endDate = $('#endEarlyEND_DATE').val();
			var endDateWas = $('#endEarlyEndWas').val();
			var totalHrs = $('#endEarlyTotalHrs').val();
			var hrsPerWeek = $('#endEarlyHrsPerWeek').val();
			ResourceRequest.ModalendEarlyPicker.destroy();

			$.ajax({
				url: "ajax/moveEndDate.php",
				type: 'POST',
				data: {
					resourceReference: resourceReference,
					endDate: endDate,
					endDateWas: endDateWas,
					totalHours: totalHrs,
					hrsPerWeek: hrsPerWeek
				},
				success: function (result) {
					helper.unlockButton();
					$('#endEarlyRR').val('');
					$('#endEarlyOrganisation').val('');
					$('#endEarlyService').val('');
					$('#endEarlyResource').val('');
					$('#endEarlyInputEND_DATE').val('');
					$('#endEarlyEND_DATE').val('');
					$('#endEarlyEndWas').val('');
					$('#endEarlyTotalHrs').val('');
					$('#endEarlyHrsPerWeek').val('');
					$('#endEarlyModal').modal('hide');
					ResourceRequest.table.ajax.reload();
				}
			});
		});
	}

	listenForEndEarly() {
		$(document).on('click', '.endEarly', function (e) {
			ModalMessageArea.showMessageArea();
			$(this).addClass('spinning').attr('disabled', true);
			var dataOwner = $(this).parent('.dataOwner'); $('#endEarlyRFS').val(dataOwner.data('rfs'));
			$('#endEarlyRR').val(dataOwner.data('resourcereference'));
			$('#endEarlyOrganisation').val(dataOwner.data('organisation'));
			$('#endEarlyService').val(dataOwner.data('service'));
			$('#endEarlyResource').val(dataOwner.data('resourcename'));
			$('#endEarlyInputEND_DATE').val(moment().format('D MMM YYYY'));
			$('#endEarlyEND_DATE').val(moment().format('YYYY-MM-DD'));
			$('#endEarlyEndWas').val(dataOwner.data('end'));

			$('#endEarlyTotalHrs').val(dataOwner.data('hrs'));
			$('#endEarlyHrsPerWeek').val(dataOwner.data('hrsperweek'));
			
			$('#endEarlyStart_Date').val(dataOwner.data('startpika'));
			$('#endEarlyModal').modal('show');
		});
	}
}

const resourceRequestIndicateCompleted = new RRIndicateCompletedBox();

export { resourceRequestIndicateCompleted as default };