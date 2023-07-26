/**
 *
 */

let startAndEnd = await cacheBustImport('./modules/calendars/startAndEnd.js');
let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let rfsIdValidator = await cacheBustImport('./modules/validators/rfsId.js');
let Rfs = await cacheBustImport('./modules/rfs.js');

class entry {

	static formId = 'rfsForm';

	static startFieldId = 'RFS_START_DATE';
	static endFieldId = 'RFS_END_DATE';

	startAndEnd;

	responseObj;

	constructor() {

	}

	prepareSelect2() {

		FormMessageArea.showMessageArea();

		$(".select").select2({
			tags: true,
			createTag: function (params) {
				return undefined;
			}
		});
		// $(".select").select2();

		FormMessageArea.clearMessageArea();
	}

	listenForRFS_IDInput() {
		$(document).on('input', '#RFS_ID', async function (e) {
			await rfsIdValidator.validateId(this);
		});
	}

	listenForRFS_IDFocusOut() {
		$(document).on('focusout', '#RFS_ID', function (e) {
			Rfs.changeValueStreamIfUnique(this);
		});
	}

	listenForValueStreamChange() {
		var $this = this;
		$(document).on('change', '#VALUE_STREAM', function () {
			FormMessageArea.showMessageArea();
			var valueStream = $('#VALUE_STREAM option:selected').val();
			$.ajax({
				url: "ajax/getBusinessUnitByValueStream.php",
				type: 'POST',
				data: { valueStream: valueStream },
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						$('#BUSINESS_UNIT').val(resultObj.businessUnit);
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to get business unit for value stream Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
					FormMessageArea.clearMessageArea();
				}
			});
		});
	}

	listenForFormReset() {
		$(document).on('reset', 'form', function (e) {
			$(".select").val('').trigger('change');
			$("#RFS_ID").val('').trigger('input');
		});
	}

	listenForEditResultModalHidden() {
		var $this = this;
		$(document).on('hidden.bs.modal', '#myModal', function (e) {
			// do something…
			if ($this.responseObj.create == true || $this.responseObj.update == true) {
				// reset form
				// $('#resetRfs').click();
				// $('#RFS_ID').css("background-color", "#ffffff");
				// reload form
				location.reload();
			} else {
				window.close();
			}
			helper.unlockButton();
		});
	}

	listenForRfsFormSubmit() {
		var $this = this;
		$(document).on('submit', '#' + entry.formId, function (event) {
			event.preventDefault();
			$(':submit').addClass('spinning').attr('disabled', true);
			var url = 'ajax/saveRfsRecord.php';
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#" + entry.formId).serialize();
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
						var savedResponse = responseObj.saveResponse;
						var span = '';
						if (savedResponse) {
							span = "<span>";
						} else {
							span = "<span style='color:red'>";
						}
						var savedResponseTxt = "<p>" + span + " <b>Record Saved: </b>" + savedResponse + "</span></p>";
						var messages = "<p><b>" + responseObj.messages + "</b></p>";
						helper.addRfsIdToKnown(responseObj.rfsId);
						helper.displaySaveResultModal(rfsIdTxt + savedResponseTxt + messages);
						$('.spinning').removeClass('spinning').attr('disabled', false);
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to save RFS record Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				},
				complete: function () {
					document.getElementById(entry.formId).reset();
				}
			});
		});
	}
}

const Entry = new entry();
Entry.prepareSelect2();

const StartAndEnd = new startAndEnd(entry.startFieldId, entry.endFieldId);
StartAndEnd.initPickers();
Entry.startAndEnd = StartAndEnd;

Entry.listenForRFS_IDInput();
Entry.listenForRFS_IDFocusOut();
Entry.listenForValueStreamChange();
Entry.listenForFormReset();
Entry.listenForRfsFormSubmit();
Entry.listenForEditResultModalHidden();

$('#LINK_TO_PGMP').attr('required', false);

$('.typeahead').bind('typeahead:select', function (ev, suggestion) {
	console.log(suggestion);
	$('.tt-menu').hide();
	$('#REQUESTOR_NAME').val(suggestion.value);
});