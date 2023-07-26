/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let RfsIdSelect = await cacheBustImport('./modules/selects/rfsId.js');
let ResourceRequestSelect = await cacheBustImport('./modules/selects/resourceRequest.js');
let ResourceNameSelect = await cacheBustImport('./modules/selects/resourceName.js');
let PSBandSelect = await cacheBustImport('./modules/selects/PSBand.js');

class editPSBandBox {

	static formId = 'editPSBandForm';
	static modalId = 'editPSBandModal';
	static editButtonId = 'editPSBand';
	static saveButtonId = 'saveResourcePSBandForm';
	static resetButtonId = 'resetResourcePSBandForm';

	table;
	ajaxUrl;

	assignmentId;
	rfsId;
	resourceReference;
	resourceName;
	PSBandId;

	constructor(parent, ajaxUrl) {
		// edit PS Band
		this.table = parent.table;
		this.ajaxUrl = ajaxUrl;

		this.listenForEditPSBandModalShown();
		this.listenForEditPSBandModalHidden();
		this.listenForEditPSBand();
		this.listenForSavePSBand();
		// edit PS Band
	}

	clearResourceNameForm() {
		this.assignmentId = '';
		this.rfsId = '';
		this.resourceReference = '';
		this.resourceName = '';
		this.PSBandId = '';
	}

	setResourceNameForm() {
		RfsIdSelect.selectValue(this.rfsId, editPSBandBox.formId);
		ResourceRequestSelect.selectValue(this.resourceReference, editPSBandBox.formId);
		ResourceNameSelect.selectValue(this.resourceName, editPSBandBox.formId);
		PSBandSelect.selectValue(this.PSBandId, editPSBandBox.formId);
	}

	listenForEditPSBandModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#' + editPSBandBox.modalId, function (e) {

			let RFSsPromise = RfsIdSelect.prepareDataForSelect(editPSBandBox.formId);
			let resourceRequestsPromise = ResourceRequestSelect.prepareDataForSelect(editPSBandBox.formId);
			let resourceNamesPromise = ResourceNameSelect.prepareDataForSelect(editPSBandBox.formId);
			let PSBandsPromise = PSBandSelect.prepareDataForSelect(editPSBandBox.formId);

			const promises = [RFSsPromise, resourceRequestsPromise, resourceNamesPromise, PSBandsPromise];
			Promise.allSettled(promises)
				.then((results) => {
					results.forEach((result) => console.log(result.status));
					$this.setResourceNameForm();
					ModalMessageArea.clearMessageArea();
				});
		});
	}

	listenForEditPSBandModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#' + editPSBandBox.modalId, function (e) {
			$this.clearResourceNameForm();
			$this.setResourceNameForm();
		});
	}

	listenForEditPSBand() {
		var $this = this;
		$(document).on('click', '.' + editPSBandBox.editButtonId, function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');
			var assignmentId = $(this).data('id');
			var rfsId = $(this).data('rfsid');
			var resourceReferenceid = $(this).data('resourcereferenceid');
			var resourceName = $(this).data('resourcename');
			var resourcePSBandId = $(this).data('resourcepsbandid');
			$this.assignmentId = assignmentId;
			$this.rfsId = rfsId;
			$this.resourceReference = resourceReferenceid;
			$this.resourceName = resourceName;
			$this.PSBandId = resourcePSBandId;
			$('#' + editPSBandBox.modalId).modal('show');
			helper.unlockButton();
		});
	}

	listenForSavePSBand() {
		var $this = this;
		$(document).on('click', '#' + editPSBandBox.saveButtonId, function (e) {
			e.preventDefault();
			$('#' + editPSBandBox.saveButtonId).addClass('spinning').attr('disabled', true);
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $('#' + editPSBandBox.formId).serialize();
			$(disabledFields).attr('disabled', true);
			$.ajax({
				url: "ajax/" + $this.ajaxUrl,
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						helper.unlockButton();
						$('#' + editPSBandBox.modalId).modal('hide');
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

export { editPSBandBox as default };