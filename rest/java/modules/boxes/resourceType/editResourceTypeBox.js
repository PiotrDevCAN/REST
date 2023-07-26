/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');

class editResourceTypeBox {

    static formId = 'editResourceTypeForm';
    static modalId = 'editResourceTypeModal';
    static editButtonId = 'editRecord';
    static saveButtonId = 'saveResourceTypeForm';
    static resetButtonId = 'resetResourceTypeForm';
    static ajaxUrl = 'saveStaticResourceType.php';

    table;

    responseObj;

    constructor(parent) {
        // edit record
        this.table = parent.table;

        this.listenForEditResourceTypeModalShown();
        this.listenForEditResourceTypeModalHidden();

        this.listenForEditResourceType();
        this.listenForSaveResourceType();
        // edit record
    }

    clearForm() {

    }

    setForm() {

    }

    listenForEditResourceTypeModalShown() {
        var $this = this;
        $(document).on('shown.bs.modal', '#' + editResourceTypeBox.modalId, function (e) {

        });
    }

    listenForEditResourceTypeModalHidden() {
        var $this = this;
        $(document).on('hidden.bs.modal', '#' + editResourceTypeBox.modalId, function (e) {
            $this.clearForm();
            $this.setForm();
        });
    }

    listenForEditResourceType() {
        var $this = this;
        $(document).on('click', '.' + editResourceTypeBox.editButtonId, function (e) {
            ModalMessageArea.showMessageArea();
            $(this).attr('disabled', true).addClass('spinning');

            var id = $(this).data('id');
            var resourceType = $(this).data('resourcetype');
            var hoursPerDay = $(this).data('hoursperday');

            $('#modalID').val(id);
            $('#modalRESOURCE_TYPE').val(resourceType);
            $('#modalHRS_PER_DAY').val(hoursPerDay);

            ModalMessageArea.clearMessageArea();
            $('#' + editResourceTypeBox.modalId).modal('show');
            $('.spinning').removeClass('spinning').attr('disabled', false);
        });
    }

    listenForSaveResourceType() {
        var $this = this;
        $(document).on('click', '#' + editResourceTypeBox.saveButtonId, function (e) {
            e.preventDefault();
            $('#' + editResourceTypeBox.saveButtonId).addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#' + editResourceTypeBox.formId).serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/" + editResourceTypeBox.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function (result) {
                    try {
                        helper.unlockButton();
                        $('#' + editResourceTypeBox.modalId).modal('hide');
                        var resultObj = JSON.parse(result);
                        var success = resultObj.success;
                        var messages = resultObj.messages;
                        if (success) {
                            messages = 'Save successful';
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
            // e.preventDefault();
        });
    }
}

export { editResourceTypeBox as default };