/**
 *
 */

class resourceTypeEntry {

    static saveButtonId = 'saveResourceType';
    static resetButtonId = 'resetResourceType';

    table;
    responseObj;

    constructor() {
        this.listenForSaveResourceType();
        this.listenForResetForm();
    }

    listenForSaveResourceType() {
        var $this = this;
        $(document).on('click', '#' + resourceTypeEntry.saveButtonId, function (e) {
            e.preventDefault();
            $('#' + resourceTypeEntry.saveButtonId).addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#resourceTypeForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveStaticResourceType.php",
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
                        $('#RESOURCE_TYPE_ID').val('');
                        $('#RESOURCE_TYPE').val('');
                        $('#HRS_PER_DAY').val('');
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
        $(document).on('click', '#resetResourceType', function () {
            $('#RESOURCE_TYPE_ID').val('');
            $('#RESOURCE_TYPE').val('');
            $('#HRS_PER_DAY').val('');
            $('#saveResourceType').val('Submit');
            $('#mode').val('Define');
        });
    }
}

const ResourceTypeEntry = new resourceTypeEntry();

export { ResourceTypeEntry as default };