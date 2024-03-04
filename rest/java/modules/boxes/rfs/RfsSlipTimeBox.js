/**
 *
 */

let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');
let Rfs = await cacheBustImport('./modules/rfs.js');

var slippingCounter = 0;

class RfsSlipTimeBox {

	startPickers = [];
	endPickers = [];

	constructor() {
		// slip time
		this.listenForSlipRfs();
		this.listenForSaveSlippedRfsDates();
		this.listenForSlipRfsModal();
		// slip time
	}

	async initialiseStartDateOnModal(element) {
		var $this = this;

		let events = await BankHolidays.getFormattedEvents();
		let eventsRaw = await BankHolidays.getEvents();
		let eventTitles = await BankHolidays.getEventTitles();

		var reference = $(element).data('reference');
		var db2DateElementId = '#START_DATE_' + reference;
		var startPicker = new Pikaday({
			events: events,
			firstDay: 1,
			field: element,
			format: 'D MMM YYYY',
			showTime: false,
			minDate: new Date(),
			onSelect: function () {
				var db2Value = this.getMoment().format('YYYY-MM-DD');
				$(db2DateElementId).val(db2Value);
				$($this.startPickers).each(function (index, element) {
					var sDate = element.getDate();
					$this.startPickers[index].setStartRange(sDate);
					$this.endPickers[index].setStartRange(sDate);
					$this.endPickers[index].setMinDate(sDate);
				});
			},
			onDraw: function () {
				helper.addEventTitlesToPicker(eventsRaw, eventTitles);
			}
		});
		return startPicker;
	}

	async initialiseEndDateOnModal(element) {
		var $this = this;

		let events = await BankHolidays.getFormattedEvents();
		let eventsRaw = await BankHolidays.getEvents();
		let eventTitles = await BankHolidays.getEventTitles();

		var reference = $(element).data('reference');
		var db2DateElementId = '#END_DATE_' + reference;
		var endPicker = new Pikaday({
			events: events,
			firstDay: 1,
			field: element,
			format: 'D MMM YYYY',
			showTime: false,
			minDate: new Date(),
			onSelect: function () {
				var db2Value = this.getMoment().format('YYYY-MM-DD');
				$(db2DateElementId).val(db2Value);
				$($this.endPickers).each(function (index, element) {
					var eDate = element.getDate();
					$this.startPickers[index].setEndRange(eDate);
					$this.startPickers[index].setMaxDate(eDate);
					$this.endPickers[index].setEndRange(eDate);
				});
			},
			onDraw: function () {
				helper.addEventTitlesToPicker(eventsRaw, eventTitles);
			}
		});
		return endPicker;
	}

	listenForSlipRfs() {
		$(document).on('click', '.slipRfs', function (e) {
			e.preventDefault();
			$(this).addClass('spinning').attr('disabled', true);
			$(this).prev('td.details-control').trigger('click');

			var rfsId = $(this).data('rfsid');

			$.ajax({
				url: "ajax/getSlipRfsForm.php",
				type: 'POST',
				data: { rfsId: rfsId },
				success: function (resultObj) {
					try {
						helper.unlockButton();
						$('#slipRfsModalBody').html(resultObj.form);
						$('#slipRfsModal').modal('show');
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
			// e.preventDefault();
		});
	}

	listenForSlipRfsModal() {
		var $this = this;
		$(document).on('shown.bs.modal', '#slipRfsModal', function (e) {
			$('.startDate').each(async function (index, element) {
				// var reference = $(element).data('reference');
				$this.startPickers[index] = await $this.initialiseStartDateOnModal(element);
			});
			$('.endDate').each(async function (index, element) {
				// var reference = $(element).data('reference');
				$this.endPickers[index] = await $this.initialiseEndDateOnModal(element);
			});
		});
	}

	listenForSaveSlippedRfsDates() {
		var $this = this;
		$(document).on('click', '#saveSlippedRfsDates', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$('.startDate2').each(function (index, element) {
				var reference = $(element).data('reference');
				var startDate = $(element).val();
				$.ajax({
					url: "ajax/slipResourceHours.php",
					type: 'POST',
					data: {
						ModalSTART_DATE: startDate,
						ModalResourceReference: reference
					},
					beforeSend: function () {
						slippingCounter++;
					},
					success: function (result) {
						if (--slippingCounter <= 0) {
							// $('#editResourceHours').html('<p></p>');
							$('#resourceHoursModal').modal('hide');
							helper.unlockButton();
							$('#slipRfsModal').modal('hide');
							Rfs.refreshAndReloadTable();
						}
					}
				});
			});
		});
	}
}

const RFSSlipTimeBox = new RfsSlipTimeBox();

export { RFSSlipTimeBox as default };