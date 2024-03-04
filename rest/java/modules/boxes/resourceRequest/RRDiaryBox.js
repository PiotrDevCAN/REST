/**
 *
 */

let ModalMessageArea = await cacheBustImport('./modules/helpers/modalMessageArea.js');
let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRDiaryBox {

	constructor() {
		// diary record
		this.listenForDiaryModalShown();
		this.listenForDiaryModalHidden();
		this.listenForBtnDiaryEntry();
		this.listenForSaveDiaryEntry();
		// diary record
	}

	listenForDiaryModalShown() {
		$(document).on('shown.bs.modal', '#diaryModal', function (e) {
			ModalMessageArea.clearMessageArea();
			$('#diary').html('');
			var resourceReference = $('#RESOURCE_REQUEST').val();
			$.ajax({
				url: "ajax/getDiaryForResourceReference.php",
				type: 'POST',
				data: { resourceReference: resourceReference },
				success: function (resultObj) {
					try {
						$('#saveDiaryEntry').attr('disabled', false);
						$('#newDiaryEntry').html('').attr('contenteditable', true);
						$('#diary').html(resultObj.diary);
					} catch (e) {
						helper.unlockButton();
						helper.displayErrorMessageModal("<h2>Json call to get diary for resource reference Failed.Tell Piotr</h2><p>" + e + "</p>");
					}
				}
			});
		});
	}

	listenForDiaryModalHidden() {
		$(document).on('hidden.bs.modal', '#diaryModal', function (e) {
			$('#RESOURCE_REQUEST').val('');
			$('#organisation').val('');
			$('#request').val('');
			$('#rfs').val('');
			$('#diary').html('');
		});
	}

	listenForBtnDiaryEntry() {
		$(document).on('click', '.btnOpenDiary', function (e) {
			ModalMessageArea.showMessageArea();
			$('#RESOURCE_REQUEST').val($(this).data('reference'));
			$('#organisation').val($(this).data('organisation'));
			$('#request').val($(this).data('reference'));
			$('#rfs').val($(this).data('rfs'));
			$('#newDiaryEntry').html('').attr('contenteditable', false);
			$('#saveDiaryEntry').attr('disabled', true);
			$('#diaryModal').modal('show');
		});
	}

	listenForSaveDiaryEntry() {
		$(document).on('click', '#saveDiaryEntry', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			var newDiaryEntry = $('#newDiaryEntry').html();
			var resourceReference = $('#RESOURCE_REQUEST').val();
			$.ajax({
				url: "ajax/saveDiaryEntry.php",
				type: 'POST',
				data: {
					newDiaryEntry: newDiaryEntry,
					resourceReference: resourceReference
				},
				success: function (result) {
					helper.unlockButton();
					ResourceRequest.refreshAndReloadTable();
					$('#RESOURCE_REQUEST').val('');
					$('#newDiaryEntry').html('');
					$('#diaryModal').modal('hide');
				}
			});
		});
	}
}

const resourceRequestDiaryBox = new RRDiaryBox();

export { resourceRequestDiaryBox as default };