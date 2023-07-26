/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let formatResourceRequest = await cacheBustImport('./modules/functions/formatResourceRequest.js');
let RfsIdSelect = await cacheBustImport('./modules/selects/rfsId.js');
let ResourceTypeSelect = await cacheBustImport('./modules/selects/resourceType.js');

// data services
let getResponceRequestsExtendedByRfs = await cacheBustImport('./modules/dataSources/data/responceRequestsExtendedByRfs.js');
let getPSBandByResourceType = await cacheBustImport('./modules/dataSources/data/PSBandByResourceType.js');
let getBespokeRateData = await cacheBustImport('./modules/dataSources/data/bespokeRateData.js');

class editBespokeRateBox {

	static formId = 'editBespokeRateForm';
	static modalId = 'editBespokeRateModal';
	static editButtonId = 'editRecord';
	static saveButtonId = 'saveBespokeRateForm';
	static resetButtonId = 'resetBespokeRateForm';
	static ajaxUrl = 'saveBespokeRate.php';

	table;

	responseObj;

	assignmentId;
	bespokeRateData;

	constructor(parent) {
		// edit record
		this.table = parent.table;

		this.listenForEditBespokeRateModalShown();
		this.listenForEditBespokeRateModalHidden();

		this.listenForRfsChange();
		this.listenForResourceTypeChange();

		this.listenForEditBespokeRate();
		this.listenForSaveBespokeRate();
		// edit record
	}

	clearForm() {
		this.assignmentId = '';
	}

	setForm() {
		if (typeof (this.bespokeRateData) !== 'undefined') {
			RfsIdSelect.selectValue(this.bespokeRateData.RFS_ID, editBespokeRateBox.formId);
			ResourceTypeSelect.selectValue(this.bespokeRateData.RESOURCE_TYPE_ID, editBespokeRateBox.formId);
		}
	}

	listenForEditBespokeRateModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#' + editBespokeRateBox.modalId, function (e) {

			let RFSsPromise = RfsIdSelect.prepareDataForSelect(editBespokeRateBox.formId);
			let resourceTypesPromise = ResourceTypeSelect.prepareDataForSelect(editBespokeRateBox.formId);

			const promises = [RFSsPromise, resourceTypesPromise];
			Promise.allSettled(promises)
				.then((results) => {
					results.forEach((result) => console.log(result.status));
					$this.setForm();
					ModalMessageArea.clearMessageArea();
				});
		});
	}

	listenForEditBespokeRateModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#' + editBespokeRateBox.modalId, function (e) {
			$this.clearForm();
			$this.setForm();
		});
	}

	rfsIdChangeCallback(event) {
		let $this = event.data.box;
		let resourceReference = $this.bespokeRateData.RESOURCE_REFERENCE;

		var rfsIdSelected = $(this).val();
		ModalMessageArea.showMessageArea();
		getResponceRequestsExtendedByRfs(rfsIdSelected).then((response) => {
			if ($('#modalRESOURCE_REFERENCE').hasClass("select2-hidden-accessible")) {
				// Select2 has been initialized
				$('#modalRESOURCE_REFERENCE').val("").trigger("change");
				$('#modalRESOURCE_REFERENCE').empty().select2('destroy').attr('disabled', true);
			}
			var data = response;
			if (typeof (data) !== 'undefined') {
				$("#modalRESOURCE_REFERENCE").select2({
					data: data,
					templateResult: formatResourceRequest
				}).attr('disabled', false).val('').trigger('change');

				if (typeof (resourceReference) !== 'undefined') {
					$("#modalRESOURCE_REFERENCE").val(resourceReference).trigger('change');
				}
			}
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForRfsChange() {
		var $this = this;
		$('#' + editBespokeRateBox.formId).find('#modalRFS_ID').on('change', { box: $this }, this.rfsIdChangeCallback);
	}

	resourceTypeIdChangeCallback(event) {
		let $this = event.data.box;
		let PSBandId = $this.bespokeRateData.PS_BAND_ID;

		var resourceTypeIdSelected = $(this).val();
		ModalMessageArea.showMessageArea();
		getPSBandByResourceType(resourceTypeIdSelected).then((response) => {
			if ($('#modalPS_BAND_ID').hasClass("select2-hidden-accessible")) {
				// Select2 has been initialized
				$('#modalPS_BAND_ID').val("").trigger("change");
				$('#modalPS_BAND_ID').empty().select2('destroy').attr('disabled', true);
			}
			var data = response;
			if (typeof (data) !== 'undefined') {
				$("#modalPS_BAND_ID").select2({
					data: data
				}).attr('disabled', false).val('').trigger('change');

				if (typeof (PSBandId) !== 'undefined') {
					$("#modalPS_BAND_ID").val(PSBandId).trigger('change');
				}
			}
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForResourceTypeChange() {
		var $this = this;
		$('#' + editBespokeRateBox.formId).find('#modalRESOURCE_TYPE_ID').on('change', { box: $this }, this.resourceTypeIdChangeCallback);
	}

	listenForEditBespokeRate() {
		var $this = this;
		$(document).on('click', '.' + editBespokeRateBox.editButtonId, function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');

			var id = $(this).data('id');
			$this.assignmentId = id;

			$('#modalID').val(id);

			const promises = [];

			let bespokeRateDataPromise = getBespokeRateData(id).then((response) => {
				$this.bespokeRateData = response;
			});
			promises.push(bespokeRateDataPromise);

			// Promise.allSettled(promises)
			Promise.all(promises)
				.then((results) => {
					// results.forEach((result) => console.log(result.status));
					$('#' + editBespokeRateBox.modalId).modal('show');
					$('.spinning').removeClass('spinning').attr('disabled', false);
				})
				.catch((err) => {
					console.log("error:", err);
				});
		});
	}

	listenForSaveBespokeRate() {
		var $this = this;
		$(document).on('click', '#' + editBespokeRateBox.saveButtonId, function (e) {
			e.preventDefault();
			$('#' + editBespokeRateBox.saveButtonId).addClass('spinning').attr('disabled', true);
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $('#' + editBespokeRateBox.formId).serialize();
			$(disabledFields).attr('disabled', true);
			$.ajax({
				url: "ajax/" + editBespokeRateBox.ajaxUrl,
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						helper.unlockButton();
						$('#' + editBespokeRateBox.modalId).modal('hide');
						var resultObj = JSON.parse(result);
						var success = resultObj.success;
						var messages = resultObj.messages;
						if (success) {
							messages = 'Save successful';
						}
						helper.displaySaveResultModal(messages);
						$('.spinning').removeClass('spinning').attr('disabled', false);
						$this.table.ajax.reload();
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
			// e.preventDefault();
		});
	}
}

export { editBespokeRateBox as default };