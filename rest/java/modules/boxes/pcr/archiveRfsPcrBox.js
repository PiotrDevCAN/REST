/**
 *
 */

let Rfs = await cacheBustImport('./modules/rfs.js');

class archiveRfsPcrBox {

	constructor() {
		// archive record
		this.listenForArchiveRfs();
		this.listenForConfirmArchiveRfs();
		// archive record
	}

	listenForArchiveRfs() {
		$(document).on('click', '.archiveRfsPcr', function (e) {
			var rfsId = $(this).data('rfsid');
			var rfsPcrId = $(this).data('pcrid');
			$('.labelPcrId').html(rfsPcrId);
			$('#ModalArchivePcrId').val(rfsPcrId);
			$('#archiveRfsPcrModal').modal('show');
		});
	}

	listenForConfirmArchiveRfs() {
		$(document).on('click', '#archiveConfirmedRfs', function (e) {
			$(this).addClass('spinning').attr('disabled');
			var formData = $('#rfsPcrArchiveForm').serialize();
			$.ajax({
				url: "ajax/archiveRfsPcr.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						$('#archiveRfsPcrModalBody').html(resultObj.messages);
						setTimeout(function () { $('#archiveRfsPcrModal').modal('hide'); }, 2000);
						helper.unlockButton();
						Rfs.table.ajax.reload();
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
		});
	}
}

export { archiveRfsPcrBox as default };