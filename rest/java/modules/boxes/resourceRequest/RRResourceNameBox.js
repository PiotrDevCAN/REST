/**
 *
 */

/**
 * await blocks further code
 * 
 * .then does not block further code
 */

let StaticResourceTypes = await cacheBustImport('./modules/dataSources/staticResourceTypes.js');
let getPSBandByResourceType = await cacheBustImport('./modules/dataSources/data/PSBandByResourceType.js');

let formatResourceName = await cacheBustImport('./modules/functions/formatResourceName.js');
// let VBACActiveResources = await cacheBustImport('./modules/dataSources/vbacActiveResources.js');
let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

// data services
// let getRateCardData = await cacheBustImport('./modules/dataSources/data/rateCardData.js');
let getRateCardData = await cacheBustImport('./modules/dataSources/data/resourceTraitData.js');
let getBespokeRateData = await cacheBustImport('./modules/dataSources/data/bespokeRateData.js');

const promises = [
	StaticResourceTypes,
	formatResourceName,
	// VBACActiveResources,
	ModalMessageArea,
	ResourceRequest,
	getRateCardData,
	getBespokeRateData
];
Promise.allSettled(promises)
	.then((results) => {
		results.forEach((result) => console.log('test_' + result.status));
	});

class RRResourceNameBox {

	static formId = 'resourceNameForm';
	static modalId = 'resourceNameModal';
	static resourceNameId = 'RESOURCE_NAME';
	static editButtonId = 'editResource';
	static saveButonId = 'saveResourceName';
	static clearButonId = 'clearResourceName';

	responseObj;

	// Basic Data refreshed each time user opens a modal
	basicFormData;
	bespokeRateData;
	rateCardData;

	VBACActiveResourcesData; // do not delete
	resourceTypesData; // do not delete

	constructor() {
		this.listenForEditResourceName();

		this.listenForResourceNameModalShown();
		// this.listenForResourceNameModalHidden();

		this.listenForChangeResourceName();
		this.listenForResourceTypeChange();
		// this.listenForPSBandChange();

		this.listenForSaveResourceName();
		this.listenForClearResourceName();
	}

	enableSaveResourceName() {
		$('#' + RRResourceNameBox.saveButonId).attr('disabled', false);
	}
	disableSaveResourceName() {
		$('#' + RRResourceNameBox.saveButonId).attr('disabled', true);
	}

	enableClearResourceName() {
		$('#' + RRResourceNameBox.clearButonId).attr('disabled', false);
	}
	disableClearResourceName() {
		$('#' + RRResourceNameBox.clearButonId).attr('disabled', true);
	}

	setBespokeRateMessage(text) {
		$('#bespokeRateMessage').html(text);
	}
	clearBespokeRateMessage() {
		$('#bespokeRateMessage').html('');
	}

	setBespokeRateErrorMessage(text) {
		$('#bespokeRateErrorMessage').html(text);
	}
	clearBespokeRateErrorMessage() {
		$('#bespokeRateErrorMessage').html('');
	}

	setRateCardMessage(text) {
		$('#rateCardMessage').html(text);
	}
	clearRateCardMessage() {
		$('#rateCardMessage').html('');
	}

	setRateCardErrorMessage(text) {
		$('#rateCardErrorMessage').html(text);
	}
	clearRateCardErrorMessage() {
		$('#rateCardErrorMessage').html('');
	}

	setAssignmentTypeMessage(text) {
		$('#assignmentTypeMessage').html(text);
	}
	clearAssignmentTypeMessage() {
		$('#assignmentTypeMessage').html('');
	}

	validateBespokeRate() {
		var data = this.bespokeRateData;
		if (data.DAY_RATE === null && data.HOURLY_RATE === null) {
			this.setBespokeRateErrorMessage('No rates found for selected Resource Type and PS Band according to Bespoke Rate');
		}
	}

	validateRateCard(data) {
		if (data.DAY_RATE === null && data.HOURLY_RATE === null) {
			this.setRateCardErrorMessage('No rates found for selected Resource Type and PS Band according to Rate Card');
		}
	}

	checkResourceNameIsActive(resourceName) {
		console.log('checkResourceNameIsActive');
		console.log(resourceName);
		var resourceNames = this.VBACActiveResourcesData;
		var employeeFound = false;
		if (typeof (resourceName) !== 'undefined') {
			for (var i = 0; i < resourceNames.length; i++) {
				if (resourceNames[i].id == resourceName) {
					employeeFound = true;
					break;
				}
			}
		}
		return employeeFound;
	}

	getEmails(resourceName) {
		var emailAddress = '';
		var kynEmailAddress = '';
		var resourceNames = this.VBACActiveResourcesData;
		for (var i = 0; i < resourceNames.length; i++) {
			if (resourceNames[i].id == resourceName) {
				// console.log("The search found in JSON Object");
				emailAddress = resourceNames[i].emailAddress;
				kynEmailAddress = resourceNames[i].kynEmailAddress;
				break;
			}
		}
		return {
			emailAddress: emailAddress,
			kynEmailAddress: kynEmailAddress
		};
	}

	// setResourceNameFormParameters(resourceName) {

	// 	var resourceNames = this.VBACActiveResourcesData;

	// 	var employeeFound = false;
	// 	var messageForUser = '';

	// 	var emailAddress = '';
	// 	var kynEmailAddress = '';

	// 	if (resourceName === '') {
	// 		messageForUser = 'Resource has been not allocated yet.';
	// 		// console.log(messageForUser);
	// 	} else {
	// 		for (var i = 0; i < resourceNames.length; i++) {
	// 			if (resourceNames[i].id == resourceName) {
	// 				// console.log("The search found in JSON Object");
	// 				emailAddress = resourceNames[i].emailAddress;
	// 				kynEmailAddress = resourceNames[i].kynEmailAddress;
	// 				employeeFound = true;
	// 				break;
	// 			}
	// 		}
	// 		if (employeeFound == true) {
	// 			messageForUser = '';
	// 			// console.log('clear message');
	// 		} else {
	// 			messageForUser = 'Presently assigned employee not found in dataset read from VBAC. New resource must be assigned.';
	// 			// console.log(messageForUser);
	// 		}
	// 	}

	// 	$('#pleaseWaitMessage').html(messageForUser);

	// 	this.basicFormData.RESOURCE_EMAIL = emailAddress;
	// 	this.basicFormData.RESOURCE_KYN_EMAIL = kynEmailAddress;
	// 	this.setResourceKyndrylDataForm();

	// 	return employeeFound;
	// }

	setResourceNameForm() {
		var data = this.basicFormData;
		if (typeof (data) !== 'undefined') {
			$('#' + RRResourceNameBox.formId).find('#BUSINESS_UNIT').val(data.BUSINESS_UNIT);
			$('#' + RRResourceNameBox.formId).find('#RFS_ID').val(data.RFS_ID);
			$('#' + RRResourceNameBox.formId).find('#RESOURCE_REFERENCE').val(data.RESOURCE_REFERENCE);
			$('#' + RRResourceNameBox.formId).find('#RESOURCE_NAME')
				.val(data.RESOURCE_NAME)
				.trigger('change');
		}
	}

	setResourceKyndrylDataForm() {
		var data = this.basicFormData;
		$('#' + RRResourceNameBox.formId).find('#RESOURCE_EMAIL_ADDRESS').val(data.RESOURCE_EMAIL);
		$('#' + RRResourceNameBox.formId).find('#RESOURCE_KYN_EMAIL_ADDRESS').val(data.RESOURCE_KYN_EMAIL);
	}

	setBespokeRateForm() {
		var data = this.bespokeRateData;
		// console.log('setBespokeRateForm');
		// console.log('data');
		// console.log(data);
		if (typeof (this.bespokeRateData) !== 'undefined') {
			var recordData = this.bespokeRateData;
			this.setResourceRateForm(recordData);
			this.validateBespokeRate();
			this.setBespokeRateMessage('Resource Request has Bespoke Rate');
			this.setAssignmentTypeMessage('<b>Bespoke Rate (for an individual Resource Request) data displayed</b>');
		} else {
			this.setBespokeRateMessage('Resource Request does not have a Bespoke Rate defined yet.');
		}
	}

	setRateCardForm() {
		var data = this.rateCardData;
		if (typeof (data) !== 'undefined') {

			// this.setResourceRateForm(data);
			this.validateRateCard(data);

			this.setRateCardMessage('Individual has Rate Card');
			this.setAssignmentTypeMessage('<b>Rate Card (for an individual person) data displayed</b>');
		} else {
			this.setRateCardMessage('The individual does not have a Rate Card defined yet.');
		}
	}

	setResourceRateForm(data) {

		var bespokeRateId = this.basicFormData.BESPOKE_RATE_ID;

		// console.log('setResourceRateForm');
		// console.log('data');
		// console.log(data);
		if (typeof (data) !== 'undefined') {
			$('#' + RRResourceNameBox.formId).find('#BESPOKE_RATE_ID').val(bespokeRateId);
			$('#' + RRResourceNameBox.formId).find('#RESOURCE_TYPE_ID')
				.val(data.RESOURCE_TYPE_ID)
				.trigger('change'
					// ,
					// [
					// 	data.PS_BAND_ID
					// ]
				);
			// .trigger({
			// 	type: 'change',
			// 	params: {
			// 		data: 'TEST 111',
			// 		aaa: 'fejwihewf'
			// 	}
			// });
			// .trigger('change');
			// $('#' + RRResourceNameBox.formId).find('#PS_BAND_ID')
			// 	.val(data.PS_BAND_ID)
			// 	.trigger('change');
			$('#' + RRResourceNameBox.formId).find('#DAY_RATE').val(data.DAY_RATE);
			$('#' + RRResourceNameBox.formId).find('#HOURLY_RATE').val(data.HOURLY_RATE);
		} else {
			$('#' + RRResourceNameBox.formId).find('#BESPOKE_RATE_ID').val('');
			$('#' + RRResourceNameBox.formId).find('#RESOURCE_TYPE_ID')
				.val('')
				.trigger('change');
			$('#' + RRResourceNameBox.formId).find('#PS_BAND_ID')
				.val('')
				.trigger('change');
			$('#' + RRResourceNameBox.formId).find('#DAY_RATE').val('');
			$('#' + RRResourceNameBox.formId).find('#HOURLY_RATE').val('');
		}
	}

	modalShownCallback(event) {
		let $this = event.data.box;

		$(this).off('shown.bs.modal');

		ModalMessageArea.showMessageArea();

		$this.disableSaveResourceName();
		$this.disableClearResourceName();

		$('#' + RRResourceNameBox.formId).find('#RESOURCE_NAME').select2({
			data: $this.VBACActiveResourcesData,
			templateResult: formatResourceName
		});

		$('#' + RRResourceNameBox.formId).find('#RESOURCE_TYPE_ID').select2({
			data: $this.resourceTypesData,
			tags: true,
			createTag: function (params) {
				return undefined;
			}
		});

		console.log('All data together !');
		console.log($this.basicFormData);
		console.log($this.bespokeRateData);
		console.log($this.rateCardData);

		// setup Resource Name form
		$this.setResourceNameForm();

		// setup Bespoke Rate form
		// $this.setBespokeRateForm();

		// ModalMessageArea.clearMessageArea();
		// helper.unlockButton();

		$this.listenForResourceNameModalHidden();
	}

	modalHiddenCallback(event) {
		let $this = event.data.box;

		$(this).off('hidden.bs.modal');

		// delete $this.basicFormData;
		var basicFormData = {
			RFS_ID: '',
			RESOURCE_REFERENCE: '',
			RESOURCE_NAME: '',
			RESOURCE_EMAIL: '',
			RESOURCE_KYN_EMAIL: '',
			BUSINESS_UNIT: '',
			BESPOKE_RATE_ID: '',
			RESOURCE_TRAIT_ID: ''
		};
		$this.basicFormData = basicFormData;
		delete $this.bespokeRateData;
		delete $this.rateCardData;

		$this.clearBespokeRateMessage();
		$this.clearBespokeRateErrorMessage();
		$this.clearRateCardMessage();
		$this.clearRateCardErrorMessage();
		$this.clearAssignmentTypeMessage();

		// $this.setResourceNameForm();
		// $this.setResourceKyndrylDataForm();
		// $this.setResourceRateForm();

		$this.listenForResourceNameModalShown();
	};

	listenForEditResourceName() {
		var $this = this;
		$(document).on('click', '.' + RRResourceNameBox.editButtonId, function (e) {

			$(this).addClass('spinning').attr('disabled', true);

			let basicDataPromise = new Promise((resolve, reject) => {
				var dataOwner = $(this).parent('.dataOwner');
				var rfsId = dataOwner.data('rfs');
				var resourceReference = dataOwner.data('resourcereference');
				var resourceName = dataOwner.data('resourcename');
				var resourceEmailAddress = dataOwner.data('resourceemailaddress');
				var resourceKynEmailAddress = dataOwner.data('resourcekynemailaddress');
				var businessUnit = dataOwner.data('businessunit');
				var bespokeRateId = dataOwner.data('bespokerate');
				var resourceTraitId = dataOwner.data('resourcetrait');

				var basicFormData = {
					RFS_ID: rfsId,
					RESOURCE_REFERENCE: resourceReference,
					RESOURCE_NAME: resourceName,
					RESOURCE_EMAIL: resourceEmailAddress,
					RESOURCE_KYN_EMAIL: resourceKynEmailAddress,
					BUSINESS_UNIT: businessUnit,
					BESPOKE_RATE_ID: bespokeRateId,
					RESOURCE_TRAIT_ID: resourceTraitId
				};
				$this.basicFormData = basicFormData;

				resolve('Success');
			});

			basicDataPromise.then((response) => {

				const promises = [];

				// check for vBAC employees
				// var resourceNamesPromise = VBACActiveResources.getActiveResources().then((response) => {
				// 	$this.VBACActiveResourcesData = response;
				// });
				// promises.push(resourceNamesPromise);

				let resourceNamesPromise = new Promise((resolve, reject) => {
					// check for vBAC employees
					if (typeof ($this.VBACActiveResourcesData) !== 'undefined') {
						resolve('from cache');
					} else {
						let VBACActiveResources = cacheBustImport('./modules/dataSources/vbacActiveResources.js').then((response) => {
							response.getActiveResources().then((response) => {
								$this.VBACActiveResourcesData = response;
								resolve('from API');
							});
						});
					}
				});
				promises.push(resourceNamesPromise);

				// check for Resource Types
				var resourceTypesPromise = StaticResourceTypes.getResourceTypes().then((response) => {
					$this.resourceTypesData = response;
				});
				promises.push(resourceTypesPromise);

				// check for Bespoke Rate data
				if ($this.basicFormData.BESPOKE_RATE_ID !== '') {
					let bespokeRateDataPromise = getBespokeRateData($this.basicFormData.BESPOKE_RATE_ID).then((response) => {
						$this.bespokeRateData = response;
					});
					promises.push(bespokeRateDataPromise);
				}

				// check for Rate Card data
				if ($this.basicFormData.RESOURCE_TRAIT_ID !== '') {
					let rateCardDataPromise = getRateCardData($this.basicFormData.RESOURCE_TRAIT_ID).then((response) => {
						$this.rateCardData = response;
					})
					promises.push(rateCardDataPromise);
				}

				// Promise.allSettled(promises)
				Promise.all(promises)
					.then((results) => {
						// results.forEach((result) => console.log(result.status));
						$('#' + RRResourceNameBox.modalId).modal('show');
						$('.spinning').removeClass('spinning').attr('disabled', false);
					})
					.catch((err) => {
						console.log("error:", err);
					});
			})
				.catch((err) => {
					console.log("error:", err);
				});
		});
	}

	listenForResourceNameModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#' + RRResourceNameBox.modalId, { box: $this }, this.modalShownCallback);
		// $('#resourceNameModal').on('shown.bs.modal', { box: $this }, this.modalShownCallback);
	}

	listenForResourceNameModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#' + RRResourceNameBox.modalId, { box: $this }, this.modalHiddenCallback);
		// $('#resourceNameModal').on('hidden.bs.modal', { box: $this }, this.modalHiddenCallback);
	}

	resourceNameChangeCallback(event) {
		let $this = event.data.box;

		ModalMessageArea.showMessageArea();

		$this.basicFormData.RESOURCE_NAME = $(this).val();

		var resourceName = $(this).val();
		var messageForUser = '';
		if (resourceName === null) {
			$this.enableClearResourceName();
			messageForUser = 'Presently assigned employee not found in dataset read from VBAC. New resource must be assigned.';
		} else if (resourceName === '') {
			messageForUser = 'Resource has been not allocated yet.';
		} else {
			$this.enableSaveResourceName();
			$this.enableClearResourceName();
			messageForUser = 'Employee found in dataset read from VBAC.';
		}
		$('#pleaseWaitMessage').html(messageForUser);

		// read email addresses
		var employeeFound = $this.checkResourceNameIsActive(resourceName);
		if (employeeFound === true) {
			var emails = $this.getEmails(resourceName);
			$this.basicFormData.RESOURCE_EMAIL = emails.emailAddress;
			$this.basicFormData.RESOURCE_KYN_EMAIL = emails.kynEmailAddress;
		} else {
			$this.basicFormData.RESOURCE_EMAIL = 'Not found in VBAC';
			$this.basicFormData.RESOURCE_KYN_EMAIL = 'Not found in VBAC';
		}
		// setup Kyndryl Employee Data form
		$this.setResourceKyndrylDataForm();

		// setup Rate Card form
		$this.setRateCardForm();






		// display message regarding selected employee
		// var messageForUser = '';
		// if (resourceName === '') {
		// 	messageForUser = 'Resource has been not allocated yet.';
		// } else {
		// 	if (employeeFound == true) {
		// 		messageForUser = '';
		// 	} else {
		// 		messageForUser = 'Presently assigned employee not found in dataset read from VBAC. New resource must be assigned.';
		// 	}
		// }
		// $('#pleaseWaitMessage').html(messageForUser);

		// var employeeFound = this.setResourceNameFormParameters(resourceName);
		// console.log(resourceName);
		// console.log('employeeFound');
		// console.log(employeeFound);
		// if (employeeFound === true) {
		// 	// check for Rate Card data
		// 	// let updateRateCardPromise = $this.updateRateCard(resourceName);
		// 	// updateRateCardPromise.then((response) => {
		// 	// 	ModalMessageArea.clearMessageArea();
		// 	// });
		// } else {
		// 	ModalMessageArea.clearMessageArea();
		// }
	};

	listenForChangeResourceName() {
		var $this = this;
		$('#' + RRResourceNameBox.formId).find('#RESOURCE_NAME').on('change', { box: $this }, this.resourceNameChangeCallback);
	}

	resourceTypeIdChangeCallback(event) {
		let $this = event.data.box;
		// let PSBandId = $this.resourceTraitData.PS_BAND_ID;
		let PSBandId = '';
		var resourceTypeIdSelected = $(this).val();
		getPSBandByResourceType(resourceTypeIdSelected).then((response) => {
			if ($('#PS_BAND_ID').hasClass("select2-hidden-accessible")) {
				// Select2 has been initialized
				$('#PS_BAND_ID').val("").trigger("change");
				$('#PS_BAND_ID').empty().select2('destroy').attr('disabled', true);
			}
			var data = response;
			if (typeof (data) !== 'undefined') {
				$("#PS_BAND_ID").select2({
					data: data
				}).attr('disabled', false).val('').trigger('change');

				if (typeof (PSBandId) !== 'undefined') {
					$("#PS_BAND_ID").val(PSBandId).trigger('change');
				}
			}
		});
	}

	listenForResourceTypeChange() {
		var $this = this;
		$('#' + RRResourceNameBox.formId).find('#RESOURCE_TYPE_ID').on('change', { box: $this }, this.resourceTypeIdChangeCallback);
	}

	PSBandChangeCallback(event) {
		let $this = event.data.box;
		var PSBandIdSelected = $(this).val();
	}

	listenForPSBandChange() {
		var $this = this;
		$('#' + RRResourceNameBox.formId).find('#RESOURCE_TYPE_ID').on('change', { box: $this }, this.PSBandChangeCallback);
	}

	listenForSaveResourceName() {
		var $this = this;
		$(document).on('click', '#saveResourceName', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$this.disableClearResourceName();
			var formData = $('#' + RRResourceNameBox.formId).serialize();
			$.ajax({
				url: "ajax/saveResourceName.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						var resourceRefTxt = "";
						var resourceNameTxt = "";
						var resourceEmailTxt = "";
						var resourceKynEmailTxt = "";
						if (resultObj.resourceReference !== '') {
							resourceRefTxt = "<p><b>Resource Ref: </b>" + resultObj.resourceReference + "</p>";
						} else {
							resourceRefTxt = "";
						}
						if (resultObj.resourceName !== '') {
							resourceNameTxt = "<p><b>Resource Name: </b>" + resultObj.resourceName + "</p>";
						} else {
							resourceNameTxt = "";
						}
						if (resultObj.resourceEmail !== '') {
							resourceEmailTxt = "<p><b>Resource Email Address: </b>" + resultObj.resourceEmail + "</p>";
						} else {
							resourceEmailTxt = "";
						}
						if (resultObj.resourceKynEmail !== '') {
							resourceKynEmailTxt = "<p><b>Resource Kyndryl Email Address: </b>" + resultObj.resourceKynEmail + "</p>";
						} else {
							resourceKynEmailTxt = "";
						}

						var savedResponse = resultObj.success;
						var span = '';
						if (savedResponse) {
							span = "<span>";
						} else {
							span = "<span style='color:red'>";
						}
						var savedResponseTxt = "<p>" + span + " <b>Record Saved: </b>" + savedResponse + "</span></p>";
						var messages = "<p><b>" + resultObj.messages + "</b></p>";

						if (resultObj.success == true) {
							$('#' + RRResourceNameBox.modalId).modal('hide');
							helper.unlockButton();
							helper.displayMessageModal(resourceRefTxt + resourceNameTxt + resourceEmailTxt + resourceKynEmailTxt + savedResponseTxt + messages);
							ResourceRequest.table.ajax.reload();
						} else {
							$('#' + RRResourceNameBox.modalId).modal('hide');
							$this.enableSaveResourceName();
							helper.unlockButton();
							helper.displayErrorMessageModal(resultObj.messages);
							ResourceRequest.table.ajax.reload();
						}
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to save resource name Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}

	listenForClearResourceName() {
		var $this = this;
		$(document).on('click', '#clearResourceName', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$this.disableSaveResourceName();
			var formData = $('#' + RRResourceNameBox.formId).serialize();
			formData += "&clear=clear";
			$.ajax({
				url: "ajax/saveResourceName.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						var resourceRefTxt = "";
						if (resultObj.resourceReference !== '') {
							resourceRefTxt = "<p><b>Resource Ref: </b>" + resultObj.resourceReference + "</p>";
						} else {
							resourceRefTxt = "";
						}

						var savedResponse = resultObj.success;
						var span = '';
						if (savedResponse) {
							span = "<span>";
						} else {
							span = "<span style='color:red'>";
						}
						var savedResponseTxt = "<p>" + span + " <b>Record Resource Name Cleared: </b>" + savedResponse + "</span></p>";
						var messages = "<p><b>" + resultObj.messages + "</b></p>";

						if (resultObj.success == true) {
							$('#' + RRResourceNameBox.modalId).modal('hide');
							helper.unlockButton();
							helper.displayMessageModal(resourceRefTxt + savedResponseTxt + messages);
							ResourceRequest.table.ajax.reload();
						} else {
							$('#' + RRResourceNameBox.modalId).modal('hide');
							$this.enableSaveResourceName();
							helper.unlockButton();
							helper.displayErrorMessageModal(resultObj.messages);
							ResourceRequest.table.ajax.reload();
						}
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to clear resource name Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}
}

const resourceRequestResourceName = new RRResourceNameBox();

export { resourceRequestResourceName as default };