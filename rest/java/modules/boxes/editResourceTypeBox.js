/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let RfsIdSelect = await cacheBustImport('./modules/selects/rfsId.js');
let ResourceRequestSelect = await cacheBustImport('./modules/selects/resourceRequest.js');
let ResourceNameSelect = await cacheBustImport('./modules/selects/resourceName.js');
let ResourceTypeSelect = await cacheBustImport('./modules/selects/resourceType.js');

class editResourceTypeBox {

	static formId = 'editResourceTypeForm';
	static modalId = 'editResourceTypeModal';
	static editButtonId = 'editResourceType';
	static saveButtonId = 'saveResourceTypeForm';
	static resetButtonId = 'resetResourceTypeForm';

	table;
	ajaxUrl;

	assignmentId;
	rfsId;
	resourceReference;
	resourceName;
	resourceTypeId;

	constructor(parent, ajaxUrl) {
		// edit resource type
		this.table = parent.table;
		this.ajaxUrl = ajaxUrl;

		this.listenForEditResourceTypeModalShown();
		this.listenForEditResourceTypeModalHidden();
		this.listenForEditResourceType();
		this.listenForSaveResourceType();
		// edit resource type
	}

	clearResourceNameForm() {
		this.assignmentId = '';
		this.rfsId = '';
		this.resourceReference = '';
		this.resourceName = '';
		this.resourceTypeId = '';
	}

	setResourceNameForm() {
		RfsIdSelect.selectValue(this.rfsId, editResourceTypeBox.formId);
		ResourceRequestSelect.selectValue(this.resourceReference, editResourceTypeBox.formId);
		ResourceNameSelect.selectValue(this.resourceName, editResourceTypeBox.formId);
		ResourceTypeSelect.selectValue(this.resourceTypeId, editResourceTypeBox.formId);
	}

	listenForEditResourceTypeModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#' + editResourceTypeBox.modalId, function (e) {

			let RFSsPromise = RfsIdSelect.prepareDataForSelect(editResourceTypeBox.formId);
			let resourceRequestsPromise = ResourceRequestSelect.prepareDataForSelect(editResourceTypeBox.formId);
			let resourceNamesPromise = ResourceNameSelect.prepareDataForSelect(editResourceTypeBox.formId);
			let resourceTypesPromise = ResourceTypeSelect.prepareDataForSelect(editResourceTypeBox.formId);

			const promises = [RFSsPromise, resourceRequestsPromise, resourceNamesPromise, resourceTypesPromise];
			Promise.allSettled(promises)
				.then((results) => {
					results.forEach((result) => console.log(result.status));
					$this.setResourceNameForm();
					ModalMessageArea.clearMessageArea();
				});
		});
	}

	listenForEditResourceTypeModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#' + editResourceTypeBox.modalId, function (e) {
			$this.clearResourceNameForm();
			$this.setResourceNameForm();
		});
	}

	listenForEditResourceType() {
		var $this = this;
		$(document).on('click', '.' + editResourceTypeBox.editButtonId, function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');
			var assignmentId = $(this).data('id');
			var rfsId = $(this).data('rfsid');
			var resourceReferenceid = $(this).data('resourcereferenceid');
			var resourceName = $(this).data('resourcename');
			var resourceTypeId = $(this).data('resourcetypeid');
			$this.assignmentId = assignmentId;
			$this.rfsId = rfsId;
			$this.resourceReference = resourceReferenceid;
			$this.resourceName = resourceName;
			$this.resourceTypeId = resourceTypeId;
			$('#' + editResourceTypeBox.modalId).modal('show');
			helper.unlockButton();
		});
	}

	listenForSaveResourceType() {
		var $this = this;
		$(document).on('click', '#' + editResourceTypeBox.saveButtonId, function (e) {
			e.preventDefault();
			$('#' + editResourceTypeBox.saveButtonId).addClass('spinning').attr('disabled', true);
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $('#' + editResourceTypeBox.formId).serialize();
			$(disabledFields).attr('disabled', true);
			$.ajax({
				url: "ajax/" + $this.ajaxUrl,
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						helper.unlockButton();
						$('#' + editResourceTypeBox.modalId).modal('hide');
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

export { editResourceTypeBox as default };