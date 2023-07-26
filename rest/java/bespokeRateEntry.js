/**
 *
 */

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let RFSs = await cacheBustImport('./modules/dataSources/staticRFSs.js');
let formatResourceRequest = await cacheBustImport('./modules/functions/formatResourceRequest.js');
let StaticResourceTypes = await cacheBustImport('./modules/dataSources/staticResourceTypes.js');

// data services
let getResponceRequestsExtendedByRfs = await cacheBustImport('./modules/dataSources/data/responceRequestsExtendedByRfs.js');
let getPSBandByResourceType = await cacheBustImport('./modules/dataSources/data/PSBandByResourceType.js');

class bespokeRateEntry {

  static formId = 'bespokeRateForm';
  static saveButtonId = 'saveBespokeRate';
  static resetButtonId = 'resetBespokeRate';

  table;
  responseObj;

  constructor() {
    this.prepareSelect2();
    this.listenForRfsSelect();
    this.listenForResourceTypeSelect();
    this.listenForSaveBespokeRate();
    this.listenForFormReset();
  }

  prepareSelect2() {

    FormMessageArea.showMessageArea();

    let RFSsPromise = RFSs.getRFSs().then((response) => {
      $("#RFS_ID").select2({
        data: response,
        tags: true,
        createTag: function (params) {
          return undefined;
        }
      });
    });

    let resourceTypesPromise = StaticResourceTypes.getResourceTypes().then((response) => {
      $("#RESOURCE_TYPE_ID").select2({
        data: response,
        tags: true,
        createTag: function (params) {
          return undefined;
        }
      });
    });

    const promises = [RFSsPromise, resourceTypesPromise];
    Promise.allSettled(promises)
      .then((results) => {
        results.forEach((result) => console.log(result.status));
        FormMessageArea.clearMessageArea();
      });
  }

  listenForRfsSelect() {
    $('#RFS_ID').on('select2:select', function (e) {
      FormMessageArea.showMessageArea();
      var rfsSelected = $(e.params.data)[0].id;
      getResponceRequestsExtendedByRfs(rfsSelected).then((response) => {
        if ($('#RESOURCE_REFERENCE').hasClass("select2-hidden-accessible")) {
          // Select2 has been initialized
          $('#RESOURCE_REFERENCE').val("").trigger("change");
          $('#RESOURCE_REFERENCE').empty().select2('destroy').attr('disabled', true);
        }
        var data = response;
        if (typeof (data) !== 'undefined') {
          $("#RESOURCE_REFERENCE").select2({
            data: data,
            templateResult: formatResourceRequest
          }).attr('disabled', false).val('').trigger('change');
        }
        FormMessageArea.clearMessageArea();
      });
    });
  }

  listenForResourceTypeSelect() {
    $('#RESOURCE_TYPE_ID').on('select2:select', function (e) {
      FormMessageArea.showMessageArea();
      var resourceTypeIdSelected = $(e.params.data)[0].id;
      getPSBandByResourceType(resourceTypeIdSelected).then((response) => {
        if ($('#PS_BAND_ID').hasClass("select2-hidden-accessible")) {
          // Select2 has been initialized
          $('#PS_BAND_ID').val("").trigger("change");
          $('#PS_BAND_ID').empty().select2('destroy').attr('disabled', true);
        }
        var data = response;
        if (typeof (data) !== 'undefined') {
          $("#PS_BAND_ID").select2({
            data: data
          }).attr('disabled', false).val('').trigger('change');
        }
        FormMessageArea.clearMessageArea();
      });
    });
  }

  listenForSaveBespokeRate() {
    var $this = this;
    $(document).on('click', '#' + bespokeRateEntry.saveButtonId, function (e) {
      e.preventDefault();
      var form = $('#' + bespokeRateEntry.formId);
      var formValid = form[0].checkValidity();
      if (formValid) {
        $('#' + bespokeRateEntry.saveButtonId).addClass('spinning').attr('disabled', true);
        var disabledFields = $(':disabled:not(:submit)');
        $(disabledFields).removeAttr('disabled');
        var formData = $('#' + bespokeRateEntry.formId).serialize();
        $(disabledFields).attr('disabled', true);
        $.ajax({
          url: "ajax/saveBespokeRate.php",
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
              $('#ID').val('');
              $("#" + bespokeRateEntry.formId + " .select").val('').trigger('change');
              $('.spinning').removeClass('spinning').attr('disabled', false);
              $this.table.ajax.reload();
            } catch (e) {
              helper.unlockButton();
              helper.displayTellDevMessageModal(e);
            }
          }
        });
        // e.preventDefault();
      } else {
        form[0].reportValidity();
        $(".spinning").removeClass("spinning");
        console.log("invalid fields follow");
        console.log($(form).find(":invalid"));
      }
    });
  }

  listenForFormReset() {
    $(document).on('reset', 'form', function (e) {
      $('#ID').val('');
      $("#" + bespokeRateEntry.formId + " .select").val('').trigger('change');
      $('#saveBespokeRate').val('Submit');
      $('#mode').val('Define');
    });
  }
}

const BespokeRateEntry = new bespokeRateEntry();

export { BespokeRateEntry as default };