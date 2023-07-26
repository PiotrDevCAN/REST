/**
 *
 */

class RRForm {

    static formId = 'resourceRequestForm';
    responseObj;

    constructor() {
        this.listenForResourceRequestFormSubmit();
    }

    listenForResourceRequestFormSubmit() {
        var $this = this;
        $(document).on('submit', '#' + RfsForm.formId, function (event) {
            event.preventDefault();
            $(':submit').addClass('spinning').attr('disabled', true);
            var url = 'ajax/saveResourceRecord.php';
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $("#" + RfsForm.formId).serialize();
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
                }
            });
        });
    }
}