/**
 *
 */

let Rfs = await cacheBustImport('./modules/rfs.js');

class RfsGoLiveBox {

	constructor() {
		// go live
		this.listenForGoLiveRfs();
		this.listenForConfirmGoLiveRfs();
		this.listenForGoLiveRfsModal();
		this.listenForplREQUESTOR_EMAILChange();
		// go live
	}

	listenForConfirmGoLiveRfs() {
		$(document).on('submit', '#goLiveRfsForm', function (e) {
			$('#confirmGoLiveRfs').addClass('spinning').attr('disabled', true);
			var rfsid = $('#goLiveRfsId').val();
			var requestorName = $('#plREQUESTOR_NAME').val();
			var requestorEmail = $('#plREQUESTOR_EMAIL').val();
			$.ajax({
				url: "ajax/goLiveRfs.php",
				type: 'POST',
				data: {
					rfsid: rfsid,
					requestorName: requestorName,
					requestorEmail: requestorEmail
				},
				success: function (result) {
					try {
						var resultObj = JSON.parse(result);
						Rfs.table.ajax.reload();
						$('#goLiveRfsId').val('');
						$('#plREQUESTOR_NAME').val('');
						$('#plREQUESTOR_EMAIL').val('');
						helper.unlockButton();
						$('#goLiveRfsModal').modal('hide');
					} catch (e) {
						helper.unlockButton();
						helper.displayTellDevMessageModal(e);
					}
				}
			});
		});
	}

	listenForGoLiveRfs() {
		$(document).on('click', '.goLiveRfs', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			$('#confirmGoLiveRfs').attr('disabled', false);
			$('#goLiveRfsId').val($(this).data('rfsid'));
			$('#REQUESTOR_NAME').val('');
			$('#REQUESTOR_EMAIL').val('');
			$('#goLiveRfsModal').modal('show');
		});
	}

	listenForGoLiveRfsModal() {
		$(document).on('hide.bs.modal', '#goLiveRfsModal', function (e) {
			helper.unlockButton();
		});
	}

	listenForplREQUESTOR_EMAILChange() {
		$(document).on('change', '#plREQUESTOR_EMAIL', function () {
			var oceanRegex = RegExp('ocean.ibm.com$');
			var regex = RegExp('ibm.com$');
			var email = $('#plREQUESTOR_EMAIL').val().trim().toLowerCase();
			var oceanEmailAddress = oceanRegex.test(email);
			var ibmEmailAddress = regex.test(email);
			if (oceanEmailAddress) {
				$("#confirmGoLiveRfs").attr('disabled', false);
				$('#plREQUESTOR_EMAIL').css("background-color", "LightGreen");
				$("#plIBMNotAllowed").hide();
			} else {
				$("#confirmGoLiveRfs").attr('disabled', true);
				$('#plREQUESTOR_EMAIL').css("background-color", "LightPink");
				$("#plIBMNotAllowed").hide();
				if (ibmEmailAddress) {
					$("#plIBMNotAllowed").show();
				}
			}
		});
	}
}

const RFSGoLiveBox = new RfsGoLiveBox();

export { RFSGoLiveBox as default };