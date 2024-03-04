/**
 *
 */

let Rfs = await cacheBustImport('./modules/rfs.js');

class RfsArchiveBox {

	static formId = 'archiveRfsForm';

	constructor() {
		// archive record
		this.listenForArchiveRfs();
		this.listenForConfirmArchiveRfs();
		// archive record
	}

	listenForArchiveRfs() {
		$(document).on('click', '.archiveRfs', function (e) {
			var rfsId = $(this).data('rfsid');
			$('.labelRfsId').html(rfsId);
			$('#ModalArchiveRfsId').val(rfsId);
			$('#archiveRfsModal').modal('show');
		});
	}

	listenForConfirmArchiveRfs() {
		$(document).on('click', '#archiveConfirmedRfs', function (e) {
			$(this).addClass('spinning').attr('disabled');
			var formData = $('#' + RfsArchiveBox.formId).serialize();
			$.ajax({
				url: "ajax/archiveRfs.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						$('#archiveRfsModalBody').html(resultObj.messages);
						setTimeout(function () { $('#archiveRfsModal').modal('hide'); }, 2000);
						helper.unlockButton();
						Rfs.refreshAndReloadTable();
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
		});
	}
}

const RFSArchiveBox = new RfsArchiveBox();

export { RFSArchiveBox as default };