/**
 *
 */

/**
 * await blocks further code
 * 
 * .then does not block further code
 */

let formatResourceName = await cacheBustImport('./modules/functions/formatResourceName.js');
let VBACActiveResources = await cacheBustImport('./modules/dataSources/vbacActiveResources.js');
let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

const promises = [
	formatResourceName,
	ModalMessageArea,
	ResourceRequest
];
Promise.allSettled(promises)
	.then((results) => {
		// results.forEach((result) => console.log('test_' + result.status));
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

	VBACActiveResourcesData; // do not delete

	constructor() {
		this.listenForEditResourceName();
		this.listenForChangeResourceName();

		this.listenForResourceNameModalShown();
		this.listenForResourceNameModalHidden();

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

	checkResourceNameIsActive(resourceName) {
		var employeeFound = false;
		if (typeof (resourceName) !== 'undefined') {
			var resourceNames = this.VBACActiveResourcesData;
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
		var cnum = '';
		var emailAddress = '';
		var kynEmailAddress = '';
		var resourceNames = this.VBACActiveResourcesData;
		for (var i = 0; i < resourceNames.length; i++) {
			if (resourceNames[i].id == resourceName) {
				// console.log("The search found in JSON Object");
				cnum = resourceNames[i].cnum;
				emailAddress = resourceNames[i].emailAddress;
				kynEmailAddress = resourceNames[i].kynEmailAddress;
				break;
			}
		}
		return {
			cnum: cnum,
			emailAddress: emailAddress,
			kynEmailAddress: kynEmailAddress
		};
	}

	setResourceNameForm() {
		var data = this.basicFormData;
		var formObj = $('#' + RRResourceNameBox.formId);
		if (typeof (data) !== 'undefined') {
			formObj.find('#BUSINESS_UNIT').val(data.BUSINESS_UNIT);
			formObj.find('#RFS_ID').val(data.RFS_ID);
			formObj.find('#RESOURCE_REFERENCE').val(data.RESOURCE_REFERENCE);
			formObj.find('#RESOURCE_NAME')
				.val(data.RESOURCE_NAME)
				.trigger('change');
		}
	}

	setResourceKyndrylDataForm() {
		var data = this.basicFormData;
		var formObj = $('#' + RRResourceNameBox.formId);
		if (typeof (data) !== 'undefined') {
			formObj.find('#RESOURCE_CNUM').val(data.RESOURCE_CNUM);
			formObj.find('#RESOURCE_EMAIL_ADDRESS').val(data.RESOURCE_EMAIL);
			formObj.find('#RESOURCE_KYN_EMAIL_ADDRESS').val(data.RESOURCE_KYN_EMAIL);
		}
	}

	modalShownCallback(event) {
		let $this = event.data.box;

		// $(this).off('shown.bs.modal');

		ModalMessageArea.showMessageArea();

		$this.disableSaveResourceName();
		$this.disableClearResourceName();

		$('#' + RRResourceNameBox.formId).find('#RESOURCE_NAME').select2({
			data: $this.VBACActiveResourcesData,
			templateResult: formatResourceName
		});

		// setup Resource Name form
		$this.setResourceNameForm();

		ModalMessageArea.clearMessageArea();
		helper.unlockButton();

		// $this.listenForResourceNameModalHidden();
	}

	modalHiddenCallback(event) {
		let $this = event.data.box;

		// $(this).off('hidden.bs.modal');

		var basicFormData = {
			RFS_ID: '',
			RESOURCE_REFERENCE: '',
			RESOURCE_NAME: '',
			RESOURCE_CNUM: '',
			RESOURCE_EMAIL: '',
			RESOURCE_KYN_EMAIL: '',
			BUSINESS_UNIT: ''
		};
		$this.basicFormData = basicFormData;

		// $this.listenForResourceNameModalShown();
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
				var resourceCNUM = dataOwner.data('resourcecnum');
				var resourceEmailAddress = dataOwner.data('resourceemailaddress');
				var resourceKynEmailAddress = dataOwner.data('resourcekynemailaddress');
				var businessUnit = dataOwner.data('businessunit');

				var basicFormData = {
					RFS_ID: rfsId,
					RESOURCE_REFERENCE: resourceReference,
					RESOURCE_NAME: resourceName,
					RESOURCE_CNUM: resourceCNUM,
					RESOURCE_EMAIL: resourceEmailAddress,
					RESOURCE_KYN_EMAIL: resourceKynEmailAddress,
					BUSINESS_UNIT: businessUnit
				};
				$this.basicFormData = basicFormData;

				resolve('Success');
			});

			basicDataPromise.then((response) => {

				const promises = [];

				// check for vBAC employees
				var resourceNamesPromise = VBACActiveResources.getActiveResources().then((response) => {
					$this.VBACActiveResourcesData = response;
				});
				promises.push(resourceNamesPromise);

				// Promise.allSettled(promises)
				Promise.all(promises)
					.then((results) => {
						// results.forEach((result) => console.log(result.status));
						$('#' + RRResourceNameBox.modalId).modal('show');
						// $('.spinning').removeClass('spinning').attr('disabled', false);
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
			messageForUser = 'Presently assigned employee is not found in dataset read from VBAC <br/>or is not active resource. <br/><b>New resource must be assigned</b>.';
		} else if (resourceName === '') {
			messageForUser = 'Resource has been not allocated yet.';
		} else {
			$this.enableSaveResourceName();
			$this.enableClearResourceName();
			messageForUser = 'Employee found in dataset read from VBAC.';
		}
		$('#pleaseWaitMessage').html(messageForUser);

		var notFound = 'Not found in vBAC';
		var cnum = '';
		var emailAddress = '';
		var kynEmailAddress = '';

		// read email addresses
		if (resourceName !== '') {
			var employeeFound = $this.checkResourceNameIsActive(resourceName);
			if (employeeFound === true) {
				var emails = $this.getEmails(resourceName);
				cnum = emails.cnum;
				emailAddress = emails.emailAddress;
				kynEmailAddress = emails.kynEmailAddress;
			} else {
				cnum = notFound;
				emailAddress = notFound;
				kynEmailAddress = notFound;
			}
		}

		$this.basicFormData.RESOURCE_CNUM = cnum;
		$this.basicFormData.RESOURCE_EMAIL = emailAddress;
		$this.basicFormData.RESOURCE_KYN_EMAIL = kynEmailAddress;

		// setup Kyndryl Employee Data form
		$this.setResourceKyndrylDataForm();

		ModalMessageArea.clearMessageArea();
	};

	listenForChangeResourceName() {
		var $this = this;
		$(document).on('change', '#' + RRResourceNameBox.resourceNameId, { box: $this }, this.resourceNameChangeCallback);
		// $('#' + RRResourceNameBox.formId).find('#RESOURCE_NAME').on('change', { box: $this }, this.resourceNameChangeCallback);
	}

	listenForSaveResourceName() {
		var $this = this;
		$(document).on('click', '#' + RRResourceNameBox.saveButonId, function (e) {
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
							ResourceRequest.refreshAndReloadTable();
						} else {
							$('#' + RRResourceNameBox.modalId).modal('hide');
							$this.enableSaveResourceName();
							helper.unlockButton();
							helper.displayErrorMessageModal(resultObj.messages);
							ResourceRequest.refreshAndReloadTable();
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
		$(document).on('click', '#' + RRResourceNameBox.clearButonId, function (e) {
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
							ResourceRequest.refreshAndReloadTable();
						} else {
							$('#' + RRResourceNameBox.modalId).modal('hide');
							$this.enableSaveResourceName();
							helper.unlockButton();
							helper.displayErrorMessageModal(resultObj.messages);
							ResourceRequest.refreshAndReloadTable();
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