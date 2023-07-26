/**
 *
 */

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let formatResourceName = await cacheBustImport('./modules/functions/formatResourceName.js');
let VBACActiveResources = await cacheBustImport('./modules/dataSources/vbacActiveResources.js');
let StaticResourceTypes = await cacheBustImport('./modules/dataSources/staticResourceTypes.js');

// data services
let getPSBandByResourceType = await cacheBustImport('./modules/dataSources/data/PSBandByResourceType.js');

class resourceTraitEntry {

  static formId = 'resourceTribesForm';
  static saveButtonId = 'saveResourceTribe';
  static resetButtonId = 'resetResourceTribe';

  table;
  responseObj;

  constructor() {
    this.prepareSelect2();
    this.listenForResourceTypeSelect();
    this.listenForSaveResourceTrait();
    this.listenForFormReset();
  }

  prepareSelect2() {

    FormMessageArea.showMessageArea();

    let VBACResourcesPromise = VBACActiveResources.getActiveResources().then((response) => {
      $('#RESOURCE_NAME').select2({
        data: response,
        templateResult: formatResourceName
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

    $("#PS_BAND_OVERRIDE").select2();

    const promises = [VBACResourcesPromise, resourceTypesPromise];
    Promise.allSettled(promises)
      .then((results) => {
        results.forEach((result) => console.log(result.status));
        FormMessageArea.clearMessageArea();
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

  listenForSaveResourceTrait() {
    var $this = this;
    $(document).on('click', '#' + resourceTraitEntry.saveButtonId, function (e) {
      e.preventDefault();
      var form = $('#' + resourceTraitEntry.formId);
      var formValid = form[0].checkValidity();
      if (formValid) {
        $('#' + resourceTraitEntry.saveButtonId).addClass('spinning').attr('disabled', true);
        var disabledFields = $(':disabled:not(:submit)');
        $(disabledFields).removeAttr('disabled');
        var formData = $('#' + resourceTraitEntry.formId).serialize();
        $(disabledFields).attr('disabled', true);
        $.ajax({
          url: "ajax/saveResourceTrait.php",
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
              $("#" + resourceTraitEntry.formId + " .select").val('').trigger('change');
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
      $("#" + resourceTraitEntry.formId + " .select").val('').trigger('change');
      $('#saveResourceTribe').val('Submit');
      $('#mode').val('Define');
    });
  }
}

const ResourceTraitEntry = new resourceTraitEntry();

export { ResourceTraitEntry as default };