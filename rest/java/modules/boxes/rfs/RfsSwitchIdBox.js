/**
 *
 */

let rfsIdValidator = await cacheBustImport('./modules/validators/rfsId.js');
let Rfs = await cacheBustImport('./modules/rfs.js');

class RfsSwitchIdBox {

	constructor() {
		// switch rfs id
		// functions under development
		this.listenForRFS_IDInput();
		this.listenForRFS_IDFocusOut();
		this.listenForSwitchRfsId();
		this.listenForConfirmSwitchRfsId();
		// functions under development
		// switch rfs id
	}

	listenForRFS_IDInput() {
		$(document).on('input', '#switchRFS_ID', async function (e) {
			await rfsIdValidator.validateId(this);
		});
	}

	listenForRFS_IDFocusOut() {
		$(document).on('focusout', '#switchRFS_ID', function (e) {
			Rfs.changeValueStreamIfUnique(this);
		});
	}

	listenForSwitchRfsId() {
		$(document).on('click', '.editRfsId', function (e) {
			e.preventDefault();
			var rfsId = $(this).data('rfsid');
			var isInType = helper.checkIfRfsIsSpecificType(rfsId, 'PLD');
			if (isInType) {
				$('#switchRfsIdModalBody').html("<h5>Please confirm you wish to CHANGE RFS ID: <b>" + rfsId + "</b> and all its associated Resources</h5>" +
					"<form id='switchRfsForm' class=''>" +
					"<br/>" +
					"<div class='form-group' id='originalRFS_IDFormGroup'>" +
					"<label for='originalRFS_ID' class='col-md-3 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='' data-original-title=''>Current RFS ID</label>" +
					"<input class='form-control' type='text' name='originalRFS_ID' value='" + rfsId + "' readonly/>" +
					"</div>" +
					"<div class='form-group' id='RFS_IDFormGroup'>" +
					"<label for='switchRFS_ID' class='col-md-3 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='' data-original-title=''>New RFS ID</label>" +
					"<input class='form-control' type='text' id='switchRFS_ID' name='switchRFS_ID' value='" + rfsId + "' />" +
					"<p id='rfsIdInvalid' style='color: crimson; display: none;'>RFS ID does not meet XXXX-XXX-000000 pattern</p>" +
					"</div>" +
					"</form>");
				$('#switchConfirmedRfsId').attr('disabled', false);
			} else {
				$('#switchRfsIdModalBody').html("<h5>An ID of Rfs PLD type can be changed only</h5");
				$('#switchConfirmedRfsId').attr('disabled', true);
			}
			$('#switchRfsIdModal').modal('show');
			// e.preventDefault();
		});
	}

	listenForConfirmSwitchRfsId() {
		$(document).on('click', '#switchConfirmedRfsId', function (e) {
			$('#switchConfirmedRfsId').addClass('spinning');
			var formData = $('#switchRfsForm').serialize();
			$.ajax({
				url: "ajax/changeRfsId.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						Rfs.table.ajax.reload();
						var resultObj = JSON.parse(result);
						$('#switchRfsIdModalBody').html(resultObj.messages);
						setTimeout(function () { $('#switchRfsIdModal').modal('hide'); }, 2000);
						$('#switchConfirmedRfsId').removeClass('spinning');
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
		});
	}
}

const RFSSwitchIdBox = new RfsSwitchIdBox();

export { RFSSwitchIdBox as default };