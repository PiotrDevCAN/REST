/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');

class editBandBox {

    static formId = 'editBandForm';
    static modalId = 'editBandModal';
    static editButtonId = 'editRecord';
    static saveButtonId = 'saveBandForm';
    static resetButtonId = 'resetBandForm';
    static ajaxUrl = 'saveStaticBand.php';

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
        $(document).on('shown.bs.modal', '#' + editBandBox.modalId, function (e) {

        });
    }

    listenForEditBandModalHidden() {
        var $this = this;
        $(document).on('hidden.bs.modal', '#' + editBandBox.modalId, function (e) {
            $this.clearForm();
            $this.setForm();
        });
    }

    listenForEditBand() {
        var $this = this;
        $(document).on('click', '.' + editBandBox.editButtonId, function (e) {
            ModalMessageArea.showMessageArea();
            $(this).attr('disabled', true).addClass('spinning');

            var id = $(this).data('id');
            var band = $(this).data('band');

            $('#modalID').val(id);
            $('#modalBAND').val(band);

            ModalMessageArea.clearMessageArea();
            $('#' + editBandBox.modalId).modal('show');
            $('.spinning').removeClass('spinning').attr('disabled', false);
        });
    }

    listenForSaveBand() {
        var $this = this;
        $(document).on('click', '#' + editBandBox.saveButtonId, function (e) {
            e.preventDefault();
            $('#' + editBandBox.saveButtonId).addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#' + editBandBox.formId).serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/" + editBandBox.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function (result) {
                    try {
                        helper.unlockButton();
                        $('#' + editBandBox.modalId).modal('hide');
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

export { editBandBox as default };