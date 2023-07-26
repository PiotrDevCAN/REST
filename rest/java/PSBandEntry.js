/**
 *
 */

class PSBandEntry {

    table;
    responseObj;

    constructor() {
        this.listenForSavePSBand();
        this.listenForResetForm();
    }

    listenForSavePSBand() {
        var $this = this;
        $(document).on('click', '#saveBand', function (e) {
            e.preventDefault();
            $('#saveBand').addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#psBandForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveStaticPSBand.php",
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
                        $('#BAND_ID').val('');
                        $('#BAND').val('');
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
        $(document).on('click', '#resetBand', function () {
            $('#BAND_ID').val('');
            $('#BAND').val('');
            $('#saveBand').val('Submit');
            $('#mode').val('Define');
        });
    }
}

const PsBandEntry = new PSBandEntry();

export { PsBandEntry as default };