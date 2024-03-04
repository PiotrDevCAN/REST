/**
 *
 */

let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRChangeStatusCompletedBox {

	constructor() {
		// change status completed
		this.listenForStatusModalShown();
		this.listenForStatusModalHidden();
		this.listenForChangeStatus();
		this.listenForChangeStatusCompleted();
		// change status completed
	}

	listenForStatusModalShown() {
		$(document).on('shown.bs.modal', '#statusModal', function (e) {

		});
	}

	listenForStatusModalHidden() {
		$(document).on('hidden.bs.modal', '#statusModal', function (e) {

		});
	}

	listenForChangeStatus() {
		$(document).on('click', '.changeStatus', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$('#statusChangeRR').val($.trim($(this).data('resourcereference')));
			$('#statusChangeRfs').val($.trim($(this).data('rfs')));
			$('#statusChangeService').val($.trim($(this).data('service')));
			$('#statusChangeStart').val($.trim($(this).data('start')));
			$('#statusChangeSub').val($.trim($(this).data('sub')));
			$('#statusModal').modal('show');

			var status = $(this).data('status');
			var statusId = '#statusRadio' + status.replace(' ', '_');

			$(statusId).prop("checked", true).trigger("click");
		});
	}

	listenForChangeStatusCompleted() {
		$(document).on('click', '.changeStatusCompleted', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			var statusChangeRR = $(this).data('resourcereference');
			var statusRadio = 'Completed';
			$.ajax({
				url: "ajax/saveStatusChange.php",
				type: 'POST',
				data: {
					statusChangeRR: statusChangeRR,
					statusRadio: statusRadio
				},
				success: function (result) {
					helper.unlockButton();
					ResourceRequest.refreshAndReloadTable();
				}
			});
		});
	}
}

const resourceRequestChangeStatusCompleted = new RRChangeStatusCompletedBox();

export { resourceRequestChangeStatusCompleted as default };