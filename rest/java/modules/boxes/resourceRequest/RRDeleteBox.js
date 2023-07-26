/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRDeleteBox {

	constructor() {
		// delete record
		this.listenForConfirmDeleteModalShown();
		this.listenForConfirmDeleteModalHidden();
		this.listenForDeleteRecord();
		this.listenForConfirmedDelete();
		// delete record
	}

	listenForConfirmDeleteModalShown() {
		$(document).on('shown.bs.modal', '#confirmDeleteModal', function (e) {
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForConfirmDeleteModalHidden() {
		$(document).on('hidden.bs.modal', '#confirmDeleteModal', function (e) {
			$('#deleteResourceRef').val("");
			$('#deleteMessageBody').html("");
		});
	}

	listenForDeleteRecord() {
		$(document).on('click', '.deleteRecord', function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');
			var resourceReference = $(this).data('reference');
			var platform = $(this).data('platform');
			var type = $(this).data('type');
			var rfs = $(this).data('rfs');

			$('#deleteResourceRef').val(resourceReference);

			var message = "<p>Please confirm you wish to DELETE Resource Reference :<b>" + resourceReference + "</b></p>";
			message += "<div class='container'>";
			message += "<div class='row'>";
			message += "<div class='col-sm-1'><b>RFS</b></div><div class='col-sm-11'>" + rfs + "</div>";
			message += "</div>";
			message += "<div class='row'>";
			message += "<div class='col-sm-1'><b>Platform</b></div><div class='col-sm-11'>" + platform + "</div>";
			message += "</div>";
			message += "<div class='row'>";
			message += "<div class='col-sm-1'><b>Type</b></div><div class='col-sm-11'>" + type + "</div>";
			message += "</div>";
			message += "</div>";

			$('#deleteMessageBody').html(message);
			$('#confirmDeleteResource').attr('disabled', false);
			$('#confirmDeleteModal').modal('show');
			$('#confirmDeleteResource').attr('disabled', false);
		});
	}

	listenForConfirmedDelete() {
		$(document).on('click', '#confirmDeleteResource', function (e) {
			$(this).attr('disabled', true).addClass('spinning');
			var formData = $('#confirmDeleteModalForm').serialize();
			$.ajax({
				url: "ajax/deleteResourceRequest.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						var message = "<h3>Record(s) deleted, you may now close this window</h3>";
						message += "<br/>Feedback from Delete : <small>" + resultObj.messages + "</small>";
						$('#deleteMessageBody').html(message);
						$('#confirmDeleteResource').attr('disabled', true);
						var clickedButtons = $('.spinning');
						clickedButtons.removeClass('spinning');
						clickedButtons.not('#confirmDeleteResource').attr('disabled', false);
						ResourceRequest.table.ajax.reload();
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to delete resource request Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}
}

const resourceRequestDeleteBox = new RRDeleteBox();

export { resourceRequestDeleteBox as default };