/**
 *
 */

let startAndEnd = await cacheBustImport('./modules/calendars/startAndEnd.js');
let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let StaticResourceTypes = await cacheBustImport('./modules/dataSources/staticResourceTypes.js');
let StaticPSBands = await cacheBustImport('./modules/dataSources/staticPSBands.js');
let StaticBands = await cacheBustImport('./modules/dataSources/staticBands.js');

class resourceRateEntry {

  static formId = 'resourceRateForm';
  static saveButtonId = 'saveResourceRate';
  static resetButtonId = 'resetResourceRate';

  static startFieldId = 'TIME_PERIOD_START';
  static endFieldId = 'TIME_PERIOD_END';

  startAndEnd;
  table;
  responseObj;

  constructor() {
    this.prepareSelect2();
    this.listenForSaveResourceRate();
    this.listenForFormReset();
  }

  prepareSelect2() {

    FormMessageArea.showMessageArea();

    let resourceTypesPromise = StaticResourceTypes.getResourceTypes().then((response) => {
      $("#RESOURCE_TYPE_ID").select2({
        data: response,
        tags: true,
        createTag: function (params) {
          return undefined;
        }
      });
    });

    let PSBandsPromise = StaticPSBands.getPSBands().then((response) => {
      $("#PS_BAND_ID").select2({
        data: response,
        tags: true,
        createTag: function (params) {
          return undefined;
        }
      });
    });

    let bandsPromise = StaticBands.getBands().then((response) => {
      $("#BAND_ID").select2({
        data: response,
        tags: true,
        createTag: function (params) {
          return undefined;
        }
      });
    });

    const promises = [resourceTypesPromise, PSBandsPromise, bandsPromise];
    Promise.allSettled(promises)
      .then((results) => {
        results.forEach((result) => console.log(result.status));
        FormMessageArea.clearMessageArea();
      });
  }

  listenForSaveResourceRate() {
    var $this = this;
    $(document).on('click', '#' + resourceRateEntry.saveButtonId, function (e) {
      e.preventDefault();
      $('#' + resourceRateEntry.saveButtonId).addClass('spinning').attr('disabled', true);
      var disabledFields = $(':disabled:not(:submit)');
      $(disabledFields).removeAttr('disabled');
      var formData = $('#resourceRateForm').serialize();
      $(disabledFields).attr('disabled', true);
      $.ajax({
        url: "ajax/saveResourceRate.php",
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
            $("#" + resourceRateEntry.formId + " .select").val('').trigger('change');
            $('#InputTIME_PERIOD_START').val('');
            $('#TIME_PERIOD_START').val('');
            $('#InputTIME_PERIOD_END').val('');
            $('#TIME_PERIOD_END').val('');
            $('#DAY_RATE').val('');
            $('#HOURLY_RATE').val('');
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

  listenForFormReset() {
    $(document).on('reset', 'form', function (e) {
      $("#" + resourceRateEntry.formId + " .select").val('').trigger('change');
      $('#DAY_RATE').val('');
      $('#HOURLY_RATE').val('');
      $('#saveResourceRate').val('Submit');
      $('#mode').val('Define');
    });
  }
}

const ResourceRateEntry = new resourceRateEntry();

const StartAndEnd = new startAndEnd(resourceRateEntry.startFieldId, resourceRateEntry.endFieldId);
StartAndEnd.initPickers();
ResourceRateEntry.startAndEnd = StartAndEnd;

export { ResourceRateEntry as default };