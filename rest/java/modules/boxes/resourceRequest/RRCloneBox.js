/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRCloneBox {

	constructor() {
		// duplicate record
		this.listenForConfirmDuplicationModalShown();
		this.listenForConfirmDuplicationModalHidden();
		this.listenForDuplicateResource();
		this.listenForConfirmedDuplication();
		// duplicate record
	}

	makeResourceDuplication(data, modalId) {
		$.ajax({
			url: "ajax/duplicateResource.php",
			type: 'POST',
			data: data,
			success: function (result) {
				helper.unlockButton();
				$(modalId).modal('hide');

				ResourceRequest.table.ajax.reload();
			}
		});
	}

	listenForConfirmDuplicationModalShown() {
		$(document).on('shown.bs.modal', '#confirmDuplicationModal', function (e) {
			ModalMessageArea.clearMessageArea();
		});
	}

	listenForConfirmDuplicationModalHidden() {
		$(document).on('hidden.bs.modal', '#confirmDuplicationModal', function (e) {

		});
	}

	listenForDuplicateResource() {
		$(document).on('click', '.requestDuplication', function (e) {
			ModalMessageArea.showMessageArea();
			$(this).addClass('spinning').attr('disabled', true);
			$('#confirmDuplicateRR').text($.trim($(this).data('reference')));
			$('#confirmDuplicateRFS').text($.trim($(this).data('rfs')));
			$('#confirmDuplicateType').text($.trim($(this).data('type')));
			$('#confirmDuplicateStart').text($.trim($(this).data('start')));
			$('#confirmDuplicationModal').modal('show');
		});
	}

	listenForConfirmedDuplication() {
		var $this = this;
		$(document).on('click', '#duplicationConfirmed', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			var resourceReference = $('#confirmDuplicateRR').text();
			var data = {
				resourceReference: resourceReference,
				delta: false,
			};
			$this.makeResourceDuplication(data, '#confirmDuplicationModal');
		});
	}
}

const resourceRequestCloneBox = new RRCloneBox();

export { resourceRequestCloneBox as default };