/**
 *
 */

class valueStreamEntry {

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
            $("#VALUE_STREAM").val($(this).data("valuestream"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteRecord() {
        var $this = this;
        $(document).on("click", ".deleteRecord", function () {
            var valueStream = $(this).data('valuestream');
            $.ajax({
                url: "ajax/deleteValueStream.php",
                type: 'POST',
                data: {
                    VALUE_STREAM: valueStream
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
            $('#mode').val('Define');
        });
    }
}

const ValueStreamEntry = new valueStreamEntry();

export { ValueStreamEntry as default };