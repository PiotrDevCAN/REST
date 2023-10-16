/**
 *
 */

class businessUnitsValuesStreamsEntry {

    table;
    responseObj;

    constructor() {
        this.listenForDeleteRecord();
        this.listenForEditRecord();
        this.listenForSaveOrganisation();
        this.listenForResetForm();
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
            return false;
        });
    }

    listenForSaveOrganisation() {
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