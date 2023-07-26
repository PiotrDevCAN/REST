/**
 *
 */

let Rfs = await cacheBustImport('./modules/rfs.js');

class RfsDeleteBox {

	static formId = 'deleteRfsForm';

	constructor() {
		// delete record
		this.listenForDeleteRfs();
		this.listenForConfirmDeleteRfs();
		// delete record
	}

	listenForDeleteRfs() {
		$(document).on('click', '.deleteRfs', function (e) {
			var rfsId = $(this).data('rfsid');
			$('.labelRfsId').html(rfsId);
			$('#ModalDeleteRfsId').val(rfsId);
			$('#deleteRfsModal').modal('show');
		});
	}

	listenForConfirmDeleteRfs() {
		$(document).on('click', '#deleteConfirmedRfs', function (e) {
			$(this).addClass('spinning').attr('disabled');
			var formData = $('#' + RfsDeleteBox.formId).serialize();
			$.ajax({
				url: "ajax/deleteRfs.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						$('#deleteRfsModalBody').html(resultObj.messages);
						setTimeout(function () { $('#deleteRfsModal').modal('hide'); }, 3000);
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

const RFSDeleteBox = new RfsDeleteBox();

export { RFSDeleteBox as default };