/**
 *
 */

let formMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let Services = await cacheBustImport('./modules/dataSources/servicesByOrganisation.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');
let StaticOrganisations = await cacheBustImport('./modules/dataSources/staticOrganisationsIds.js');
let StaticServices = await cacheBustImport('./modules/dataSources/staticServicesIds.js');

class RREditBox {

	responseObj;

	constructor() {
		// edit record
		this.listenForResourceRequestEditShown();
		this.listenForResourceRequestEditHidden();
		this.listenForEditRecord();
		this.listenForOrganisationSelect();
		this.listenForResourceRequestFormSubmit();
		// edit record
	}

	listenForResourceRequestEditShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#editRequestModal', function (e) {
			// ModalMessageArea.clearMessageArea();
			formMessageArea.clearMessageArea();

			var selectedOrganisation = $("#originalORGANISATION").val();
			StaticOrganisations.getOrganisations().then((response) => {
				$("#ORGANISATION").select2({
					data: response,
					tags: true,
					createTag: function (params) {
						return undefined;
					}
				})
					.val(selectedOrganisation)
					.trigger('change');
			});

			var selectedService = $("#originalSERVICE").val();
			StaticServices.getServices().then((response) => {
				$("#SERVICE").select2({
					data: response,
					tags: true,
					createTag: function (params) {
						return undefined;
					}
				})
					.val(selectedService)
					.trigger('change');
			});
		});
	}

	listenForResourceRequestEditHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#editRequestModal', function (e) {

		});
	}

	listenForResourceRequestFormSubmit() {
		var $this = this;
		$(document).on('submit', '#resourceRequestForm', function (event) {
			event.preventDefault();
			$('#resourceRequestForm :submit').addClass('spinning').attr('disabled', true);
			var url = 'ajax/saveResourceRecord.php';
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#resourceRequestForm").serialize();
			$(disabledFields).attr('disabled', true);
			$.ajax({
				type: 'post',
				url: url,
				data: formData,
				context: document.body,
				beforeSend: function (data) {
					// do the following before the save is started
				},
				success: function (result) {
					// do what ever you want with the server response if that response is "success"						
					try {
						var responseObj = JSON.parse(result);
						$this.responseObj = responseObj;
						var resourceRefTxt = "";
						if (responseObj.resourceReference !== '') {
							resourceRefTxt = "<p><b>Resource Ref: " + responseObj.resourceReference + "</b></p>";
						} else {
							resourceRefTxt = "";
						}
						var savedResponse = responseObj.saveResponse;
						var span = '';
						if (savedResponse) {
							span = "<span>";
						} else {
							span = "<span style='color:red'>";
						}
						var savedResponseTxt = "<p>" + span + " <b>Record Saved: </b>" + savedResponse + "</span></p>";
						var hoursResponseTxt = "<p>" + responseObj.hoursResponse + "</p>";
						var messages = "<p><b>" + responseObj.messages + "</b></p>";

						$('#editRequestModal').modal('hide');

						helper.unlockButton();
						ResourceRequest.refreshAndReloadTable();

						$('#recordSaveDiv').html(resourceRefTxt + savedResponseTxt + hoursResponseTxt + messages);
						$('#recordSavedModal').modal('show');
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to save resource record Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}

	listenForEditRecord() {
		var $this = this;
		$(document).on('click', '.editRecord', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$(this).prev('td.details-control').trigger('click');

			var resourceReference = $(this).data('reference');
			ModalMessageArea.showMessageArea();

			$.ajax({
				url: "ajax/getEditResourceForm.php",
				type: 'POST',
				data: { resourceReference: resourceReference },
				success: function (resultObj) {
					try {
						helper.unlockButton();
						$('#editRequestModalBody').html(resultObj.form);
						$('#editRequestModal').modal('show');
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to get edit resource form Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}

	listenForOrganisationSelect() {
		$(document).on('select2:select', '#ORGANISATION', function (e) {
			var organisationSelectedId = $(e.params.data)[0].id;
			Services.getServices().then((response) => {
				var data = response[organisationSelectedId];
				if ($('#SERVICE').hasClass("select2-hidden-accessible")) {
					// Select2 has been initialized
					$('#SERVICE').val("").trigger("change");
					$('#SERVICE').empty().select2('destroy').attr('disabled', true);
				}
				$("#SERVICE").select2({
					data: data
				}).attr('disabled', false).val('').trigger('change');

				if (data.length == 2) {
					$("#SERVICE").val(data[1].text).trigger('change');
				}
			});
		});
	}
}

const resourceRequestEditBox = new RREditBox();

export { resourceRequestEditBox as default };