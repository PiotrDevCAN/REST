/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRArchiveBox {

	constructor() {
		// archive record
		this.listenForConfirmArchiveModalShown();
		this.listenForConfirmArchiveModalHidden();
		this.listenForArchiveRecord();
		this.listenForConfirmedArchive();
		// archive record
	}

	listenForConfirmArchiveModalShown() {
		$(document).on('shown.bs.modal', '#confirmArchiveModal', function (e) {
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForConfirmArchiveModalHidden() {
		$(document).on('hidden.bs.modal', '#confirmArchiveModal', function (e) {
			$('#archiveResourceRef').val("");
			$('#archiveMessageBody').html("");
		});
	}

	listenForArchiveRecord() {
		$(document).on('click', '.archiveRecord', function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');
			var resourceReference = $(this).data('reference');
			var platform = $(this).data('platform');
			var type = $(this).data('type');
			var rfs = $(this).data('rfs');

			$('#archiveResourceRef').val(resourceReference);

			var message = "<p>Please confirm you wish to ARCHIVE Resource Reference :<b>" + resourceReference + "</b></p>";
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

			$('#archiveMessageBody').html(message);
			$('#confirmArchiveResource').attr('disabled', false);
			$('#confirmArchiveModal').modal('show');
			$('#confirmArchiveResource').attr('disabled', false);
		});
	}

	listenForConfirmedArchive() {
		$(document).on('click', '#confirmArchiveResource', function (e) {
			$(this).attr('disabled', true).addClass('spinning');
			var formData = $('#confirmArchiveModalForm').serialize();
			$.ajax({
				url: "ajax/archiveResourceRequest.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						var message = "<h3>Record(s) archived, you may now close this window</h3>";
						message += "<br/>Feedback from Archive : <small>" + resultObj.messages + "</small>";
						$('#archiveMessageBody').html(message);
						$('#confirmArchiveResource').attr('disabled', true);
						var clickedButtons = $('.spinning');
						clickedButtons.removeClass('spinning');
						clickedButtons.not('#confirmArchiveResource').attr('disabled', false);
						ResourceRequest.refreshAndReloadTable();
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to archive resource request Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}
}

const resourceRequestArchiveBox = new RRArchiveBox();

export { resourceRequestArchiveBox as default };