/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceNameSelect = await cacheBustImport('./modules/selects/resourceName.js');
let ResourceTypeSelect = await cacheBustImport('./modules/selects/resourceType.js');
let PSBandOverrideSelect = await cacheBustImport('./modules/selects/PSBandOverride.js');

// data services
let getPSBandByResourceType = await cacheBustImport('./modules/dataSources/data/PSBandByResourceType.js');
let getResourceTraitData = await cacheBustImport('./modules/dataSources/data/resourceTraitData.js');

class editResourceTraitBox {

	static formId = 'editResourceTraitForm';
	static modalId = 'editResourceTraitModal';
	static editButtonId = 'editRecord';
	static saveButtonId = 'saveResourceTraitForm';
	static resetButtonId = 'resetResourceTraitForm';
	static ajaxUrl = 'saveResourceTrait.php';

	table;

	responseObj;

	assignmentId;
	resourceTraitData;

	constructor(parent) {
		// edit record
		this.table = parent.table;

		this.listenForEditResourceTraitModalShown();
		this.listenForEditResourceTraitModalHidden();

		this.listenForResourceTypeChange();

		this.listenForEditResourceTrait();
		this.listenForSaveResourceTrait();
		// edit record
	}

	clearForm() {
		this.assignmentId = '';
	}

	setForm() {
		if (typeof (this.resourceTraitData) !== 'undefined') {
			ResourceNameSelect.selectValue(this.resourceTraitData.RESOURCE_NAME, editResourceTraitBox.formId);
			ResourceTypeSelect.selectValue(this.resourceTraitData.RESOURCE_TYPE_ID, editResourceTraitBox.formId);
			PSBandOverrideSelect.selectValue(this.resourceTraitData.PS_BAND_OVERRIDE, editResourceTraitBox.formId);
		}
	}

	listenForEditResourceTraitModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#' + editResourceTraitBox.modalId, function (e) {

			let resourceNamesPromise = ResourceNameSelect.prepareDataForSelect(editResourceTraitBox.formId);
			let resourceTypesPromise = ResourceTypeSelect.prepareDataForSelect(editResourceTraitBox.formId);

			$("#modalPS_BAND_OVERRIDE").select2();

			const promises = [resourceNamesPromise, resourceTypesPromise];
			Promise.allSettled(promises)
				.then((results) => {
					results.forEach((result) => console.log(result.status));
					$this.setForm();
					ModalMessageArea.clearMessageArea();
				});
		});
	}

	listenForEditResourceTraitModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#' + editResourceTraitBox.modalId, function (e) {
			$this.clearForm();
			$this.setForm();
		});
	}

	resourceTypeIdChangeCallback(event) {
		let $this = event.data.box;
		let PSBandId = $this.resourceTraitData.PS_BAND_ID;

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
		$('#' + editResourceTraitBox.formId).find('#modalRESOURCE_TYPE_ID').on('change', { box: $this }, this.resourceTypeIdChangeCallback);
	}

	listenForEditResourceTrait() {
		var $this = this;
		$(document).on('click', '.' + editResourceTraitBox.editButtonId, function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');

			var id = $(this).data('id');
			$this.assignmentId = id;

			$('#modalID').val(id);

			const promises = [];

			let resourceTraitDataPromise = getResourceTraitData(id).then((response) => {
				$this.resourceTraitData = response;
			});
			promises.push(resourceTraitDataPromise);

			// Promise.allSettled(promises)
			Promise.all(promises)
				.then((results) => {
					// results.forEach((result) => console.log(result.status));
					$('#' + editResourceTraitBox.modalId).modal('show');
					$('.spinning').removeClass('spinning').attr('disabled', false);
				})
				.catch((err) => {
					console.log("error:", err);
				});
		});
	}

	listenForSaveResourceTrait() {
		var $this = this;
		$(document).on('click', '#' + editResourceTraitBox.saveButtonId, function (e) {
			e.preventDefault();
			$('#' + editResourceTraitBox.saveButtonId).addClass('spinning').attr('disabled', true);
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $('#' + editResourceTraitBox.formId).serialize();
			$(disabledFields).attr('disabled', true);
			$.ajax({
				url: "ajax/" + editResourceTraitBox.ajaxUrl,
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						helper.unlockButton();
						$('#' + editResourceTraitBox.modalId).modal('hide');
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

export { editResourceTraitBox as default };