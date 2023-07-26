/**
 *
 */

let rfsIdValidator = await cacheBustImport('./modules/validators/rfsId.js');
let Rfs = await cacheBustImport('./modules/rfs.js');

class RfsExtendBox {

	constructor() {
		// duplicate rfs
		this.listenForRFS_IDInput();
		this.listenForRFS_IDFocusOut();
		this.listenForExtendRfs();
		this.listenForConfirmExtendRfs();
		// duplicate rfs
	}

	listenForRFS_IDInput() {
		$(document).on('input', '#extendRFS_ID', async function (e) {
			await rfsIdValidator.validateId(this);
		});
	}

	listenForRFS_IDFocusOut() {
		$(document).on('focusout', '#extendRFS_ID', function (e) {
			Rfs.changeValueStreamIfUnique(this);
		});
	}

	listenForExtendRfs() {
		$(document).on('click', '.extendRfs', function (e) {
			var rfsId = $(this).data('rfsid');
			var isInType = helper.checkIfRfsIsSpecificType(rfsId, 'RFS');
			if (isInType) {
				var PLDRfsId = rfsId.toUpperCase().replace(/-RFS-|\s/g, '-PLD-');
				$('#extendRfsModalBody').html("<h5>Please confirm you wish to EXTEND RFS ID: <b>" + rfsId + "</b> and all its associated Resources</h5>" +
					"<form id='extendRfsForm' class=''>" +
					"<br/>" +
					"<div class='form-group' id='originalRFS_IDFormGroup'>" +
					"<label for='originalRFS_ID' class='col-md-3 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='' data-original-title=''>Current RFS ID</label>" +
					"<input class='form-control' type='text' name='originalRFS_ID' value='" + rfsId + "' readonly/>" +
					"</div>" +
					"<div class='form-group' id='RFS_IDFormGroup'>" +
					"<label for='extendRFS_ID' class='col-md-3 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='' data-original-title=''>New RFS ID</label>" +
					"<input class='form-control' type='text' id='extendRFS_ID' name='extendRFS_ID' value='" + PLDRfsId + "' />" +
					"<p id='rfsIdInvalid' style='color: crimson; display: none;'>RFS ID does not meet XXXX-XXX-000000 pattern</p>" +
					"</div>" +
					"</form>");
				$('#extendConfirmedRfs').attr('disabled', false);
			} else {
				$('#extendRfsModalBody').html("<h5>An ID of Rfs RFS type can be changed only</h5");
				$('#extendConfirmedRfs').attr('disabled', true);
			}
			$('#extendRfsModal').modal('show');
		});
	}

	listenForConfirmExtendRfs() {
		$(document).on('click', '#extendConfirmedRfs', function (e) {
			$('#extendConfirmedRfs').addClass('spinning');
			var formData = $('#extendRfsForm').serialize();
			$.ajax({
				url: "ajax/changeRfsId.php",
				type: 'POST',
				data: formData,
				success: function (result) {
					try {
						Rfs.table.ajax.reload();
						var resultObj = JSON.parse(result);
						$('#extendRfsModalBody').html(resultObj.messages);
						setTimeout(function () { $('#extendRfsModal').modal('hide'); }, 2000);
						$('#extendConfirmedRfs').removeClass('spinning');
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
		});
	}
}

const RFSExtendBox = new RfsExtendBox();

export { RFSExtendBox as default };