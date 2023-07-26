/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let formatResourceRequest = await cacheBustImport('./modules/functions/formatResourceRequest.js');

let RfsIdSelect = await cacheBustImport('./modules/selects/rfsId.js');
let ResourceRequestSelect = await cacheBustImport('./modules/selects/resourceRequest.js');
let ResourceTypeSelect = await cacheBustImport('./modules/selects/resourceType.js');
let PSBandSelect = await cacheBustImport('./modules/selects/PSBand.js');

// data services
let getResponceRequestsExtendedByRfs = await cacheBustImport('./modules/dataSources/data/responceRequestsExtendedByRfs.js');
let getPSBandByResourceType = await cacheBustImport('./modules/dataSources/data/PSBandByResourceType.js');
let getBespokeRateData = await cacheBustImport('./modules/dataSources/data/bespokeRateData.js');

class previewBespokeRateBox {

	static formId = 'previewBespokeRateForm';
	static modalId = 'previewBespokeRateModal';
	static previewButtonId = 'previewRecord';

	table;

	responseObj;

	assignmentId;
	bespokeRateData;

	constructor(parent) {
		// preview record
		this.table = parent.table;

		this.listenForPreviewBespokeRateModalShown();
		this.listenForPreviewBespokeRateModalHidden();

		this.listenForRfsChange();
		this.listenForResourceTypeChange();

		this.listenForPreviewBespokeRate();
		// preview record
	}

	clearForm() {
		this.assignmentId = '';
	}

	setForm() {
		if (typeof (this.bespokeRateData) !== 'undefined') {
			RfsIdSelect.selectValue(this.bespokeRateData.RFS_ID, previewBespokeRateBox.formId);
			ResourceRequestSelect.selectValue(this.bespokeRateData.RESOURCE_REFERENCE, previewBespokeRateBox.formId);
			ResourceTypeSelect.selectValue(this.bespokeRateData.RESOURCE_TYPE_ID, previewBespokeRateBox.formId);
			PSBandSelect.selectValue(this.bespokeRateData.PS_BAND_ID, previewBespokeRateBox.formId);
		}
	}

	listenForPreviewBespokeRateModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#' + previewBespokeRateBox.modalId, function (e) {

			let RFSsPromise = RfsIdSelect.prepareDataForSelect(previewBespokeRateBox.formId);
			let resourceTypesPromise = ResourceTypeSelect.prepareDataForSelect(previewBespokeRateBox.formId);

			const promises = [RFSsPromise, resourceTypesPromise];
			Promise.allSettled(promises)
				.then((results) => {
					results.forEach((result) => console.log(result.status));
					$this.setForm();
					ModalMessageArea.clearMessageArea();
				});
		});
	}

	listenForPreviewBespokeRateModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#' + previewBespokeRateBox.modalId, function (e) {
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
				}).attr('disabled', true).val('').trigger('change');

				if (typeof (resourceReference) !== 'undefined') {
					$("#modalRESOURCE_REFERENCE").val(resourceReference).trigger('change');
				}
			}
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForRfsChange() {
		var $this = this;
		$('#' + previewBespokeRateBox.formId).find('#modalRFS_ID').on('change', { box: $this }, this.rfsIdChangeCallback);
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
				}).attr('disabled', true).val('').trigger('change');

				if (typeof (PSBandId) !== 'undefined') {
					$("#modalPS_BAND_ID").val(PSBandId).trigger('change');
				}
			}
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForResourceTypeChange() {
		var $this = this;
		$('#' + previewBespokeRateBox.formId).find('#modalRESOURCE_TYPE_ID').on('change', { box: $this }, this.resourceTypeIdChangeCallback);
	}

	listenForPreviewBespokeRate() {
		var $this = this;
		$(document).on('click', '.' + previewBespokeRateBox.previewButtonId, function (e) {
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
					$('#' + previewBespokeRateBox.modalId).modal('show');
					$('.spinning').removeClass('spinning').attr('disabled', false);
				})
				.catch((err) => {
					console.log("error:", err);
				});
		});
	}
}

export { previewBespokeRateBox as default };