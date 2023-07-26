/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');

class deleteRecordBox {

	static formId = 'deleteAssignmentForm';
	static modalId = 'deleteAssignmentModal';
	static modalBodyId = 'deleteAssignmentModalBody';
	static deleteButtonId = 'deleteRecord';
	static confirmButtonId = 'deleteConfirmedAssignment';
	static AssignmentId = 'modalDeleteAssignmentId';

	table;
	ajaxUrl;

	constructor(parent, ajaxUrl) {
		// delete assignment
		this.table = parent.table;
		this.ajaxUrl = ajaxUrl;

		this.listenForDeleteAssignmentModalShown();
		this.listenForDeleteAssignmentModalHidden();
		this.listenForDeleteAssignment();
		this.listenForConfirmedDelete();
		// delete assignment
	}

	listenForDeleteAssignmentModalShown() {
		var $this = this;
		$(document).on('shown.bs.modal', '#' + deleteRecordBox.modalId, function (e) {
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForDeleteAssignmentModalHidden() {
		$(document).on('hidden.bs.modal', '#' + deleteRecordBox.modalId, function (e) {
			$('#' + deleteRecordBox.AssignmentId).val('');
		});
	}

	listenForDeleteAssignment() {
		$(document).on('click', '.' + deleteRecordBox.deleteButtonId, function (e) {
			ModalMessageArea.showMessageArea();
			$(this).attr('disabled', true).addClass('spinning');
			var assignmentId = $(this).data('id');
			$('#' + deleteRecordBox.AssignmentId).val(assignmentId);
			$('#' + deleteRecordBox.modalId).modal('show');
			helper.unlockButton();
		});
	}

	listenForConfirmedDelete() {
		var $this = this;
		$(document).on('click', '#' + deleteRecordBox.confirmButtonId, function (e) {
			$(this).attr('disabled', true).addClass('spinning');
			var formData = $('#' + deleteRecordBox.formId).serialize();
			$.ajax({
				url: "ajax/" + $this.ajaxUrl,
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						helper.unlockButton();
						$('#' + deleteRecordBox.modalId).modal('hide');
						var resultObj = JSON.parse(result);
						// var success = resultObj.success;
						var messages = resultObj.messages;
						var message = '';
						// var message = 'Something went wrong.';
						// if (success) {
							message = "<h3>Record deleted, you may now close this window</h3>";
							message += "<br/>Feedback from server : <small>" + messages + "</small>";
						// }
						helper.displayDeleteResultModal(message);
						$('.spinning').removeClass('spinning').attr('disabled', false);
						$this.table.ajax.reload();
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
		});
	}
}

export { deleteRecordBox as default };