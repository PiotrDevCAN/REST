/**
 *
 */

let StaticOrganisations = await cacheBustImport('./modules/dataSources/staticOrganisations.js');
let StaticServices = await cacheBustImport('./modules/dataSources/staticServices.js');

class organisationServiceEntry {

    table;
    responseObj;

    constructor() {
        this.prepareSelect2();
        this.listenForEditRecord();
        this.listenForDeleteRecord();
        this.listenForSaveRecord();
        this.listenForResetForm();
    }

    prepareSelect2() {

		// FormMessageArea.showMessageArea();

		let orgaisationPromise = StaticOrganisations.getOrganisations().then((response) => {
			$("#ORGANISATION").select2({
				data: response,
				tags: true,
				createTag: function (params) {
					return undefined;
				}
			});
		});

        let servicePromise = StaticServices.getServices().then((response) => {
			$("#SERVICE").select2({
				data: response,
				tags: true,
				createTag: function (params) {
					return undefined;
				}
			});
		});

		const promises = [orgaisationPromise, servicePromise];
		Promise.allSettled(promises)
			.then((results) => {
				results.forEach((result) => console.log(result.status));
				// FormMessageArea.clearMessageArea();
			});

		// FormMessageArea.clearMessageArea();
	}

    listenForEditRecord() {
        $(document).on("click", ".editRecord", function () {
            $("#ORGANISATION").val($(this).data("organisation"));
            $("#SERVICE").val($(this).data("service"));
            if ($(this).data("status") == "enabled") {
                $("#statusRadioEnabled").prop("checked", true);
            } else {
                $("#statusRadioDisabled").prop("checked", true);
            }
            $("#mode").val("edit");
        });
    }

    listenForDeleteRecord() {
        var $this = this;
        $(document).on("click", ".deleteRecord", function () {
            var status = $(this).data('status');
            var organisation = $(this).data('organisation');
            var service = $(this).data('service');
            $.ajax({
                url: "ajax/deleteOrganisationService.php",
                type: 'POST',
                data: {
                    currentStatus: status,
                    ORGANISATION: organisation,
                    SERVICE: service
                },
                success: function (result) {
                    try {
                        var resultObj = JSON.parse(result);
                        var success = resultObj.success;
                        var messages = resultObj.messages;
                        if (success) {
                            messages = 'Record deleted';
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
        });
    }

    listenForSaveRecord() {
        var $this = this;
        $(document).on('click', '#saveOrganisationService', function (e) {
            e.preventDefault();
            $('#saveOrganisationService').addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#organisationServiceForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveOrganisationService.php",
                type: 'POST',
                data: formData,
                success: function (result) {
                    try {
                        var resultObj = JSON.parse(result);
                        var success = resultObj.success;
                        var messages = resultObj.messages;
                        if (success) {
                            messages = 'Save successful';
                        }
                        helper.displaySaveResultModal(messages);
                        $('#ORGANISATION').val('');
                        $('#SERVICE').val('');
                        $('#statusRadioDisabled').prop('checked', false);
                        $('#statusRadioEnabled').prop('checked', true);
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

    listenForResetForm() {
        $(document).on('click', '#resetOrganisation', function () {
            $("input[name=statusRadio][value=enabled]").prop('checked', true);
            $("input[name=statusRadio]").attr('disabled', false);
            $('#ORGANISATION').val('');
            $('#SERVICE').val('');
            $('#saveCtbService').val('Submit');
            $('#mode').val('Define');
        });
    }
}

const OrganisationServiceEntry = new organisationServiceEntry();

export { OrganisationServiceEntry as default };