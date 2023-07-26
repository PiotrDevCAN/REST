/**
 *
 */

class RfsForm {

	static formId = 'rfsForm';
	responseObj;

	constructor() {
		this.listenForRfsFormSubmit();
	}

	listenForRfsFormSubmit() {
		var $this = this;
		$(document).on('submit', '#' + RfsForm.formId, function (event) {
			event.preventDefault();
			$(':submit').addClass('spinning').attr('disabled', true);
			var url = 'ajax/saveRfsRecord.php';
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
					//	do the following before the save is started
				},
				success: function (result) {
					// 	do what ever you want with the server response if that response is "success"
					try {
						helper.unlockButton();
						$('#editRfsModal').modal('hide');
						var responseObj = JSON.parse(result);
						$this.responseObj = responseObj;
						var rfsIdTxt = "<p><b>RFS ID:</b>" + responseObj.rfsId + "</p>";
						var savedResponse = responseObj.saveResponse;
						var span = "";
						if (savedResponse) {
							span = "<span>";
						} else {
							span = "<span style='color:red'>";
						}
						var savedResponseTxt = "<p>" + span + " <b>Record Saved:</b>" + savedResponse + "</span></p>";
						if (responseObj.messages != null) {
							var messages = "<p>" + responseObj.messages + "</p>";
						}
						var messages = "<p>" + responseObj.messages + "</p>";
						helper.addRfsIdToKnown(responseObj.rfsId);
						helper.displaySaveResultModal(rfsIdTxt + savedResponseTxt + messages);
						$('.spinning').removeClass('spinning').attr('disabled', false);
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to save RFS record Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}
}