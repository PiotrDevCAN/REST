/**
 *
 */

let StaticValueStreams = await cacheBustImport('./modules/dataSources/staticValueStreams.js');
let StaticBusinessUnits = await cacheBustImport('./modules/dataSources/staticBusinessUnits.js');

class businessUnitsValuesStreamsEntry {

    table;
    responseObj;

    constructor() {
        this.prepareSelect2();
        this.listenForDeleteRecord();
        this.listenForEditRecord();
        this.listenForSaveValueStream();
        this.listenForResetForm();
    }


    prepareSelect2() {

		// FormMessageArea.showMessageArea();

		let orgaisationPromise = StaticValueStreams.getValueStreams().then((response) => {
			$("#VALUE_STREAM").select2({
				data: response,
				tags: true,
				createTag: function (params) {
					return undefined;
				}
			});
		});

        let servicePromise = StaticBusinessUnits.getBusinessUnits().then((response) => {
			$("#BUSINESS_UNIT").select2({
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
            $("#VALUE_STREAM").val($(this).data("valuestream"));
            $("#BUSINESS_UNIT").val($(this).data("businessunit"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteRecord() {
        $(document).on("click", ".deleteRecord", function () {
            var valuestream = $(this).data('valuestream');
            var businessunit = $(this).data('businessunit');
            $.ajax({
                url: "ajax/deleteValueStream.php",
                type: 'POST',
                data: {
                    VALUE_STREAM: valuestream,
                    BUSINESS_UNIT: businessunit
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
                    } catch (e) {
                        helper.unlockButton();
                        helper.displayTellDevMessageModal(e);
                    }
                }
            });
        });
    }

    listenForSaveValueStream() {
        var $this = this;
        $(document).on('click', '#saveValueStream', function (e) {
            e.preventDefault();
            $('#saveValueStream').addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#valueStreamForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveValueStream.php",
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
                        $('#VALUE_STREAM').val('');
                        $('#BUSINESS_UNIT').val('');
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
        $(document).on('click', '#resetValueStream', function () {
            $('#VALUE_STREAM').val('');
            $('#BUSINESS_UNIT').val('');
            $('#saveValueStream').val('Submit');
            $('#mode').val('Define');
        });
    }
}

const BusinessUnitsValuesStreamsEntry = new businessUnitsValuesStreamsEntry();

export { BusinessUnitsValuesStreamsEntry as default };