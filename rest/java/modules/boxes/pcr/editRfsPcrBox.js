/**
 *
 */

let startAndEnd = await cacheBustImport('./modules/calendars/startAndEnd.js');
let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let rfsPcrIdValidator = await cacheBustImport('./modules/validators/rfsPcrId.js');
let Rfs = await cacheBustImport('./modules/rfs.js');

// data services
let getRfsPcrData = await cacheBustImport('./modules/dataSources/data/rfsPcrData.js');

class editRfsPcrBox {

	static formId = 'createPcrForm';
	static modalId = 'editPcrModal';
	static editButtonId = 'createPcr';
	static saveButtonId = 'editConfirmedPcr';
	static ajaxUrl = 'saveRfsPcrRecord.php';

	static startFieldId = 'modalPCR_START_DATE';
	static endFieldId = 'modalPCR_END_DATE';

	startAndEnd;
	responseObj;

	assignmentId;
	rfsPcrData;

	constructor() {
		// edit PCR
		this.listenForPCR_NUMBERInput();
		this.listenForPCR_NUMBERFocusOut();

		this.listenForConfirmEditModalShown();
		this.listenForConfirmEditModalHidden();
		this.listenForCreatePcr();
		this.listenForConfirmCreatePcr();

		const StartAndEnd = new startAndEnd(editRfsPcrBox.startFieldId, editRfsPcrBox.endFieldId);
		this.startAndEnd = StartAndEnd;
		// edit PCR
	}

	clearForm() {
		this.assignmentId = '';
	}

	setForm() {
		if (typeof (this.rfsPcrData) !== 'undefined') {
			$('#modalPCR_NUMBER').val(this.rfsPcrData.PCR_NUMBER);
			$('#modalPCR_START_DATE').val(this.rfsPcrData.PCR_START_DATE);
			$('#modalPCR_END_DATE').val(this.rfsPcrData.PCR_END_DATE);
			$('#modalPCR_AMOUNT').val(this.rfsPcrData.PCR_AMOUNT);
		}
	}

	listenForPCR_NUMBERInput() {
		$(document).on('input', '#modalPCR_NUMBER', async function (e) {
			await rfsPcrIdValidator.validateId(this);
		});
	}

	listenForPCR_NUMBERFocusOut() {
		$(document).on('focusout', '#modalPCR_NUMBER', function (e) {

		});
	}

	listenForConfirmEditModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#editPcrModal', function (e) {
			$this.setForm();

			$this.startAndEnd.initPickers();

			ModalMessageArea.clearMessageArea();
		});
	}

	listenForConfirmEditModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#editPcrModal', function (e) {
			$('#modalPCR_NUMBER').val('').trigger('input');
			$this.startAndEnd.destroyPickers();
			$('#modalPCR_AMOUNT').val('');
		});
	}

	listenForCreatePcr() {
		var $this = this;
		$(document).on('click', '.' + editRfsPcrBox.editButtonId, function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');

			var rfsPcrId = $(this).data('id');
			var rfsPcrNumber = $(this).data('pcrnumber');
			var rfsId = $(this).data('rfsid');
			$this.assignmentId = rfsId;

			$('.labelRfsId').html(rfsId);
			$('#modalPCR_ID').val(rfsPcrId);
			$('#modalRFS_ID').val(rfsId);
			const promises = [];

			if (rfsId !== '' && rfsPcrId !== '') {
				let rfsPcrDataPromise = getRfsPcrData(rfsId, rfsPcrId).then((response) => {
					$this.rfsPcrData = response;
				});
				promises.push(rfsPcrDataPromise);
			}

			// Promise.allSettled(promises)
			Promise.all(promises)
				.then((results) => {
					// results.forEach((result) => console.log(result.status));
					$('#' + editRfsPcrBox.modalId).modal('show');
					$('.spinning').removeClass('spinning').attr('disabled', false);
				})
				.catch((err) => {
					console.log("error:", err);
				});
		});
	}

	listenForConfirmCreatePcr() {
		var $this = this;
		$(document).on('click', '#editConfirmedPcr', function (e) {
			$(this).addClass('spinning').attr('disabled');
			var formData = $('#createPcrForm').serialize();
			$.ajax({
				url: "ajax/saveRfsPcrRecord.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						helper.unlockButton();
						$('#editPcrModal').modal('hide');
						var responseObj = JSON.parse(result);
						$this.responseObj = responseObj;
						var rfsIdTxt = "<p><b>RFS ID: </b>" + responseObj.rfsId + "</p>";
						var pcrIdTxt = "<p><b>PCR ID: </b>" + responseObj.pcrId + "</p>";
						var pcrNumberTxt = "<p>PCR Number: " + responseObj.pcrNumber + "</p>";
						var pcrStartDateTxt = "<p>PCR Start Date: " + responseObj.pcrStartDate + "</p>";
						var pcrEndDateTxt = "<p>PCR End Date: " + responseObj.pcrEndDate + "</p>";
						var pcrAmountTxt = "<p>PCR Amount: " + responseObj.pcrAmount + "</p>";
						var savedResponse = responseObj.saveResponse;
						var span = "";
						if (savedResponse) {
							span = "<span>";
						} else {
							span = "<span style='color:red'>";
						}
						var savedResponseTxt = "<p>" + span + " <b>Record Saved:</b>" + savedResponse + "</span></p>";
						if (responseObj.messages != null) {
							var messages = "<p>" + responseObj.messages + "</p>";
						}
						var messages = "<p>" + responseObj.messages + "</p>";
						helper.addRfsPcrIdToKnown(responseObj.pcrNumber);
						helper.displayMessageModal(rfsIdTxt + pcrIdTxt + pcrNumberTxt + pcrStartDateTxt + pcrEndDateTxt + pcrAmountTxt + savedResponseTxt + messages);
						Rfs.refreshAndReloadTable();
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to save RFS PCR record Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}
}

export { editRfsPcrBox as default };