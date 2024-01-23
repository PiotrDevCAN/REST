/**
 *
 */

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let startAndEnd = await cacheBustImport('./modules/calendars/startAndEnd.js');
let Services = await cacheBustImport('./modules/dataSources/servicesByOrganisation.js');
let StaticOrganisations = await cacheBustImport('./modules/dataSources/staticOrganisationsIds.js');
let StaticServices = await cacheBustImport('./modules/dataSources/staticServices.js');

class entry {

  static formId = 'resourceRequestForm';

  static startFieldId = 'START_DATE';
  static endFieldId = 'END_DATE';

  startAndEnd;

  responseObj;

  constructor() {
    this.prepareSelect2();

    const StartAndEnd = new startAndEnd(entry.startFieldId, entry.endFieldId);
    StartAndEnd.initPickers();
    this.startAndEnd = StartAndEnd;

    this.listenForOrganisationSelect();
    this.listenForRfsSelect();
    this.listenForFormReset();
    this.listenForResourceRequestFormSubmit();
    this.listenForEditResultModalHidden();
  }

  prepareSelect2() {

    FormMessageArea.showMessageArea();

    let organisationsPromise = StaticOrganisations.getOrganisations().then((response) => {
      $("#ORGANISATION").select2({
        data: response,
        tags: true,
        createTag: function (params) {
          return undefined;
        }
      });
    });

    let servicesPromise = StaticServices.getServices().then((response) => {
      $("#SERVICE").select2({
        data: response,
        tags: true,
        createTag: function (params) {
          return undefined;
        }
      });
    });

    const promises = [organisationsPromise, servicesPromise];
    Promise.allSettled(promises)
      .then((results) => {
        results.forEach((result) => console.log(result.status));
        FormMessageArea.clearMessageArea();
      });

    $(".select").select2({
      tags: true,
      createTag: function (params) {
        return undefined;
      }
    });
    // $(".select").select2();
  }

  listenForOrganisationSelect() {
    $('#ORGANISATION').on('select2:select', function (e) {
      FormMessageArea.showMessageArea();
      var serviceSelected = $(e.params.data)[0].text;
      Services.getServices().then((response) => {
        var data = response[serviceSelected];
        if ($('#SERVICE').hasClass("select2-hidden-accessible")) {
          // Select2 has been initialized
          $('#SERVICE').val("").trigger("change");
          $('#SERVICE').empty().select2('destroy').attr('disabled', true);
        }
        $("#SERVICE").select2({
          data: data
        }).attr('disabled', false).val('').trigger('change');

        if (data.length == 2) {
          $("#SERVICE").val(data[1].text).trigger('change');
        }
        FormMessageArea.clearMessageArea();
      });
    });
  }

  listenForRfsSelect() {
    var $this = this;
    $('#RFS').on('select2:select', function (e) {
      FormMessageArea.showMessageArea();
      var rfsSelected = $(e.params.data)[0].text;
      var maxEndDate = null;
      $.ajax({
        url: "ajax/endDateForRfs.php",
        type: 'POST',
        data: {
          rfs: rfsSelected
        },
        success: function (result) {
          try {
            var resultObj = JSON.parse(result);
            if (resultObj.rfsEndDate !== null) {
              maxEndDate = new Date(resultObj.rfsEndDate);
              $this.startAndEnd.updateMaxDate(maxEndDate);
            }
          } catch (e) {
            helper.unlockButton();
            helper.displayTellDevMessageModal(e);
          }
          FormMessageArea.clearMessageArea();
        }
      });
    });
  }

  listenForFormReset() {
    $(document).on('reset', 'form', function (e) {
      $(".select").val('').trigger('change');
      $("#STATUS").val('New').trigger('change');
    });
  }

  listenForResourceRequestFormSubmit() {
    var $this = this;
    $(document).on('submit', '#resourceRequestForm', function (event) {
      event.preventDefault();
      $(':submit').addClass('spinning').attr('disabled', true);
      var url = 'ajax/saveResourceRecord.php';
      var disabledFields = $(':disabled:not(:submit)');
      $(disabledFields).removeAttr('disabled');
      var formData = $("#resourceRequestForm").serialize();
      $(disabledFields).attr('disabled', true);
      $.ajax({
        type: 'post',
        url: url,
        data: formData,
        context: document.body,
        beforeSend: function (data) {
          // do the following before the save is started
        },
        success: function (result) {
          // do what ever you want with the server response if that response is "success"
          try {
            var responseObj = JSON.parse(result);
            $this.responseObj = responseObj;
            var resourceRefTxt = "";
            if (responseObj.resourceReference !== '') {
              resourceRefTxt = "<p><b>Resource Ref: " + responseObj.resourceReference + "</b></p>";
            } else {
              resourceRefTxt = "";
            }
            var savedResponse = responseObj.saveResponse;
            var span = '';
            if (savedResponse) {
              span = "<span>";
            } else {
              span = "<span style='color:red'>";
            }
            var savedResponseTxt = "<p>" + span + " <b>Record Saved: </b>" + savedResponse + "</span></p>";
            var hoursResponseTxt = "<p>" + responseObj.hoursResponse + "</p>";
            var messages = "<p><b>" + responseObj.messages + "</b></p>";
            helper.displaySaveResultModal(resourceRefTxt + savedResponseTxt + hoursResponseTxt + messages);
            $('.spinning').removeClass('spinning').attr('disabled', false);
          } catch (e) {
            helper.unlockButton();
            helper.displayErrorMessageModal("<h2>Json call to save resource record Failed.Tell Piotr</h2><p>" + e + "</p>");
          }
        },
        complete: function () {
          document.getElementById(entry.formId).reset();
        }
      });
    });
  }

  listenForEditResultModalHidden() {
    var $this = this;
    $(document).on('hidden.bs.modal', '#myModal', function (e) {
      // do something…
      if ($this.responseObj.create == true || $this.responseObj.update == true) {
        // reset form
        // $('#resetResourceRequest').click();
        // reload form
        location.reload();
      } else {
        // there must be an issue so show message and summary
        window.close();
      }
      $(':submit').removeClass('spinning').attr('disabled', false);
    });
  }
}

const Entry = new entry();