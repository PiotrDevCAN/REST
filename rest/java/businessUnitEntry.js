/**
 *
 */

class businessUnitEntry {

    table;
    responseObj;

    constructor() {
        this.listenForEditRecord();
        this.listenForDeleteRecord();
        this.listenForSaveRecord();
        this.listenForResetForm();
    }

    listenForEditRecord() {
        $(document).on("click", ".editRecord", function () {
            $("#BUSINESS_UNIT").val($(this).data("businessunit"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteRecord() {
        var $this = this;
        $(document).on("click", ".deleteRecord", function () {
            var businessUnit = $(this).data('businessunit');
            $.ajax({
                url: "ajax/deleteBusinessUnit.php",
                type: 'POST',
                data: {
                    BUSINESS_UNIT: businessUnit
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
        $(document).on('click', '#saveBusinessUnit', function (e) {
            e.preventDefault();
            $('#saveBusinessUnit').addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#businessUnitForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveBusinessUnit.php",
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
        $(document).on('click', '#resetBusinessUnit', function () {
            $('#BUSINESS_UNIT').val('');
            $('#mode').val('Define');
        });
    }
}

const BusinessUnitEntry = new businessUnitEntry();

export { BusinessUnitEntry as default };