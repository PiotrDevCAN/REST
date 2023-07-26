/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');

class editPSBandBox {

    static formId = 'editPSBandForm';
    static modalId = 'editPSBandModal';
    static editButtonId = 'editRecord';
    static saveButtonId = 'savePSBandForm';
    static resetButtonId = 'resetPSBandForm';
    static ajaxUrl = 'saveStaticPSBand.php';

    table;

    responseObj;

    constructor(parent) {
        // edit record
        this.table = parent.table;

        this.listenForEditBandModalShown();
        this.listenForEditBandModalHidden();

        this.listenForEditBand();
        this.listenForSaveBand();
        // edit record
    }

    clearForm() {

    }

    setForm() {

    }

    listenForEditBandModalShown() {
        var $this = this;
        $(document).on('shown.bs.modal', '#' + editPSBandBox.modalId, function (e) {

        });
    }

    listenForEditBandModalHidden() {
        var $this = this;
        $(document).on('hidden.bs.modal', '#' + editPSBandBox.modalId, function (e) {
            $this.clearForm();
            $this.setForm();
        });
    }

    listenForEditBand() {
        var $this = this;
        $(document).on('click', '.' + editPSBandBox.editButtonId, function (e) {
            ModalMessageArea.showMessageArea();
            $(this).attr('disabled', true).addClass('spinning');

            var id = $(this).data('id');
            var band = $(this).data('band');

            $('#modalID').val(id);
            $('#modalBAND').val(band);

            ModalMessageArea.clearMessageArea();
            $('#' + editPSBandBox.modalId).modal('show');
            $('.spinning').removeClass('spinning').attr('disabled', false);
        });
    }

    listenForSaveBand() {
        var $this = this;
        $(document).on('click', '#' + editPSBandBox.saveButtonId, function (e) {
            e.preventDefault();
            $('#' + editPSBandBox.saveButtonId).addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#' + editPSBandBox.formId).serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/" + editPSBandBox.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function (result) {
                    try {
                        helper.unlockButton();
                        $('#' + editPSBandBox.modalId).modal('hide');
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

export { editPSBandBox as default };