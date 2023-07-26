/**
 *
 */

let startAndEnd = await cacheBustImport('./modules/calendars/startAndEnd.js');
let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceTypeSelect = await cacheBustImport('./modules/selects/resourceType.js');
let PSBandSelect = await cacheBustImport('./modules/selects/PSBand.js');
let bandSelect = await cacheBustImport('./modules/selects/band.js');

// data services
let getResourceRateData = await cacheBustImport('./modules/dataSources/data/resourceRateData.js');

class editResourceRateBox {

    static formId = 'editResourceRateForm';
    static modalId = 'editResourceRateModal';
    static editButtonId = 'editRecord';
    static saveButtonId = 'saveResourceRateForm';
    static resetButtonId = 'resetResourceRateForm';
    static ajaxUrl = 'saveResourceRate.php';

    static startFieldId = 'modalTIME_PERIOD_START';
    static endFieldId = 'modalTIME_PERIOD_END';

    startAndEnd;
    table;
    responseObj;

    assignmentId;
    resourceRateData;

    constructor(parent) {
        // edit record
        this.table = parent.table;

        this.listenForEditResourceRateModalShown();
        this.listenForEditResourceRateModalHidden();

        this.listenForEditResourceRate();
        this.listenForSaveResourceRate();

        const StartAndEnd = new startAndEnd(editResourceRateBox.startFieldId, editResourceRateBox.endFieldId);
        this.startAndEnd = StartAndEnd;
        // edit record
    }

    clearForm() {
        this.assignmentId = '';
    }

    setForm() {
        if (typeof (this.resourceRateData) !== 'undefined') {
            ResourceTypeSelect.selectValue(this.resourceRateData.RESOURCE_TYPE_ID, editResourceRateBox.formId);
            PSBandSelect.selectValue(this.resourceRateData.PS_BAND_ID, editResourceRateBox.formId);
            bandSelect.selectValue(this.resourceRateData.BAND_ID, editResourceRateBox.formId);

            $("#modalDAY_RATE").val(this.resourceRateData.DAY_RATE);
            $("#modalHOURLY_RATE").val(this.resourceRateData.HOURLY_RATE);

            $('#InputmodalTIME_PERIOD_START').val(this.resourceRateData.TIME_PERIOD_START);
            $('#modalTIME_PERIOD_START').val(this.resourceRateData.TIME_PERIOD_START);

            $('#InputmodalTIME_PERIOD_END').val(this.resourceRateData.TIME_PERIOD_END);
            $('#modalTIME_PERIOD_END').val(this.resourceRateData.TIME_PERIOD_END);
        }
    }

    listenForEditResourceRateModalShown() {
        var $this = this;
        $(document).on('shown.bs.modal', '#' + editResourceRateBox.modalId, function (e) {

            let resourceTypesPromise = ResourceTypeSelect.prepareDataForSelect(editResourceRateBox.formId);
            let PSBandsPromise = PSBandSelect.prepareDataForSelect(editResourceRateBox.formId);
            let bandsPromise = bandSelect.prepareDataForSelect(editResourceRateBox.formId);

            const promises = [resourceTypesPromise, PSBandsPromise, bandsPromise];
            Promise.allSettled(promises)
                .then((results) => {
                    results.forEach((result) => console.log(result.status));
                    $this.setForm();

                    $this.startAndEnd.initPickers();

                    ModalMessageArea.clearMessageArea();
                });
        });
    }

    listenForEditResourceRateModalHidden() {
        var $this = this;
        $(document).on('hidden.bs.modal', '#' + editResourceRateBox.modalId, function (e) {
            $this.clearForm();
            $this.setForm();
            $this.startAndEnd.destroyPickers();
        });
    }

    listenForEditResourceRate() {
        var $this = this;
        $(document).on('click', '.' + editResourceRateBox.editButtonId, function (e) {
            ModalMessageArea.showMessageArea();
            $(this).attr('disabled', true).addClass('spinning');

            var id = $(this).data('id');
            $this.assignmentId = id;

            $('#modalID').val(id);
            const promises = [];

            let resourceRateDataPromise = getResourceRateData(id).then((response) => {
                $this.resourceRateData = response;
            });
            promises.push(resourceRateDataPromise);

            // Promise.allSettled(promises)
            Promise.all(promises)
                .then((results) => {
                    // results.forEach((result) => console.log(result.status));
                    $('#' + editResourceRateBox.modalId).modal('show');
                    $('.spinning').removeClass('spinning').attr('disabled', false);
                })
                .catch((err) => {
                    console.log("error:", err);
                });
        });
    }

    listenForSaveResourceRate() {
        var $this = this;
        $(document).on('click', '#' + editResourceRateBox.saveButtonId, function (e) {
            e.preventDefault();
            $('#' + editResourceRateBox.saveButtonId).addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#' + editResourceRateBox.formId).serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/" + editResourceRateBox.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function (result) {
                    try {
                        helper.unlockButton();
                        $('#' + editResourceRateBox.modalId).modal('hide');
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

export { editResourceRateBox as default };