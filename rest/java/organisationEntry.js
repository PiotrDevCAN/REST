/**
 *
 */

class organisationEntry {

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
            $("#ORGANISATION").val($(this).data("organisation"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteRecord() {
        var $this = this;
        $(document).on("click", ".deleteRecord", function () {
            var organisation = $(this).data('organisation');
            $.ajax({
                url: "ajax/deleteOrganisation.php",
                type: 'POST',
                data: {
                    ORGANISATION: organisation
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
        $(document).on('click', '#saveOrganisation', function (e) {
            e.preventDefault();
            $('#saveOrganisation').addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#organisationForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveOrganisation.php",
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
            $('#ORGANISATION').val('');
            $('#mode').val('Define');
        });
    }
}

const OrganisationEntry = new organisationEntry();

export { OrganisationEntry as default };