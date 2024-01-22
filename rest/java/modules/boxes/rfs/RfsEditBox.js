/**
 *
 */

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let rfsIdValidator = await cacheBustImport('./modules/validators/rfsId.js');
let StaticValueStreams = await cacheBustImport('./modules/dataSources/staticValueStreamsIds.js');
let Rfs = await cacheBustImport('./modules/rfs.js');

class RfsEditBox {

	static formId = 'rfsForm';
	responseObj;

	constructor() {
		// edit record
		// RFS_ID filed is readonly
		// this.listenForRFS_IDInput();
		// this.listenForRFS_IDFocusOut();
		this.listenForRfsFormSubmit();
		this.listenForEditRfs();
		this.listenForEditRfsModalShown();
		this.listenForEditRfsModalHidden();
		// edit record
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

	listenForRfsFormSubmit() {
		var $this = this;
		$(document).on('submit', "#" + RfsEditBox.formId, function (event) {
			event.preventDefault();
			$(':submit').addClass('spinning').attr('disabled', true);
			var url = 'ajax/saveRfsRecord.php';
			var disabledFields = $(':disabled:not(:submit)');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#" + RfsEditBox.formId).serialize();
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

	listenForEditRfs() {
		$(document).on('click', '.editRfs', function (e) {
			e.preventDefault();
			FormMessageArea.showMessageArea();
			$(this).addClass('spinning').attr('disabled', true);
			$(this).prev('td.details-control').trigger('click');

			var rfsId = $(this).data('rfsid');
			// var URL = "pd_newRfs.php?rfs=" + rfsId;
			// var child = window.open(URL, "_blank");
			// child.onunload = function(){ console.log('Child window closed'); Rfs.table.ajax.reload(); };
			$.ajax({
				url: "ajax/getEditRfsForm.php",
				type: 'POST',
				data: { rfsId: rfsId },
				success: function (resultObj) {
					try {
						helper.unlockButton();
						$('#editRfsModalBody').html(resultObj.form);
						FormMessageArea.clearMessageArea();
						$('#editRfsModal').modal('show');
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
		});
	}

	listenForEditRfsModalShown() {
		$(document).on('shown.bs.modal', '#editRfsModal', function (e) {
			$("input[type='radio'][name='RFS_STATUS']").attr('disabled', true);
			var selectedValueStream = $("#originalVALUE_STREAM").val();
			StaticValueStreams.getValueStreams().then((response) => {
				$("#VALUE_STREAM").select2({
					data: response,
					tags: true,
					createTag: function (params) {
						return undefined;
					}
				})
					.val(selectedValueStream)
					.trigger('change');
			});
		});
	}

	listenForEditRfsModalHidden() {
		$(document).on('hidden.bs.modal', '#editRfsModal', function (e) {
			Rfs.table.ajax.reload();
		});
	}
}

const RFSEditBox = new RfsEditBox();

export { RFSEditBox as default };