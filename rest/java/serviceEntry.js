/**
 *
 */

class serviceEntry {

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
            $("#SERVICE").val($(this).data("service"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteRecord() {
        var $this = this;
        $(document).on("click", ".deleteRecord", function () {
            var service = $(this).data('service');
            $.ajax({
                url: "ajax/deleteService.php",
                type: 'POST',
                data: {
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
        $(document).on('click', '#saveService', function (e) {
            e.preventDefault();
            $('#saveService').addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#serviceForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveService.php",
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
                        $('#SERVICE').val('');
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
            $('#SERVICE').val('');
            $('#mode').val('Define');
        });
    }
}

const ServiceEntry = new serviceEntry();

export { ServiceEntry as default };