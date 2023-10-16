/**
 *
 */

class organisationEntry {

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
        $(document).on('click', '#saveService', function (e) {
            e.preventDefault();
            $('#saveService').addClass('spinning').attr('disabled', true);
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

const OrganisationEntry = new organisationEntry();

export { OrganisationEntry as default };