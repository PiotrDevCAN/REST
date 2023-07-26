/**
 *
 */

class RfsPcrForm {

    static formId = 'rfsPcrForm';
    responseObj;

    constructor() {
        this.listenForRfsPcrFormSubmit();
        this.listenForFormReset();
    }

    listenForRfsPcrFormSubmit() {
        var $this = this;
        $(document).on('submit', '#rfsPcrForm', function (event) {
            event.preventDefault();
            $(':submit').addClass('spinning').attr('disabled', true);
            var url = 'ajax/saveRfsPcrRecord.php';
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $("#rfsPcrForm").serialize();
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
                        var rfsIdTxt = "<p><b>RFS ID: </b>" + responseObj.rfsId + "</p>";
						var pcrIdTxt = "<p><b>PCR ID: </b>" + responseObj.pcrId + "</p>";
						var pcrNumberTxt = "<p>PCR Number: " + responseObj.pcrNumber + "</p>";
						var pcrStartDateTxt = "<p>PCR Start Date: " + responseObj.pcrStartDate + "</p>";
						var pcrEndDateTxt = "<p>PCR End Date: " + responseObj.pcrEndDate + "</p>";
						var pcrAmountTxt = "<p>PCR Amount: " + responseObj.pcrAmount + "</p>";
                        var savedResponse = responseObj.saveResponse;
                        var span = '';
                        if (savedResponse) {
                            span = "<span>";
                        } else {
                            span = "<span style='color:red'>";
                        }
                        var savedResponseTxt = "<p>" + span + " <b>Record Saved: </b>" + savedResponse + "</span></p>";
                        var messages = "<p><b>" + responseObj.messages + "</b></p>";
                        helper.addRfsPcrIdToKnown(responseObj.pcrNumber);
                        helper.displaySaveResultModal(rfsIdTxt + pcrIdTxt + pcrNumberTxt + pcrStartDateTxt + pcrEndDateTxt + pcrAmountTxt + savedResponseTxt + messages);
						$('.spinning').removeClass('spinning').attr('disabled', false);
                    } catch (e) {
                        helper.unlockButton();
                        helper.displayErrorMessageModal("<h2>Json call to save RFS record Failed.Tell Piotr</h2><p>" + e + "</p>");
                    }
                }
            });
        });
    }

    listenForFormReset() {
        $(document).on('reset', 'form', function (e) {
            $(".select").val('').trigger('change');
        });
    }
}