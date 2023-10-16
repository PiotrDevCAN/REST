/**
 *
 */

class Rfs {

	static colorWhite = '#ffffff';
	static colorRed = 'LightPink';
	static colorGreen = 'LightGreen';

	table;

	constructor() {
		// common elements
		this.listenForREQUESTOR_EMAILChange();
		this.listenForILC_WORK_ITEMChange();
		// common elements
	}

	async changeValueStreamIfUnique(el) {
		var newRfsId = $(el).val().trim();
		if (newRfsId !== '') {
			newRfsId = newRfsId.toUpperCase().replace(/_|\s/g, '-');
			$(el).val(newRfsId);
			var allreadyExists = await helper.checkIfRfsIdExists(newRfsId);
			if (allreadyExists) { // comes back with Position in array(true) or false is it's NOT in the array.
				// console.log('RFS already defined');
			} else {
				var selectOptionVal = '';
				var rfsId = newRfsId.substr(0, 4);
				$('#VALUE_STREAM > option').each(function () {
					var valueStream = $(this).text().toUpperCase().substr(0, 4);
					if (rfsId == valueStream) {
						if (selectOptionVal == '') {
							selectOptionVal = $(this).val(); // we've found a match, lets save it and check it's unique.
						} else {
							selectOptionVal = '';  // We found a 2nd match, so can't pre-select
							return false;
						}
					}
				});
				if (selectOptionVal != '') {
					$('#VALUE_STREAM').val(selectOptionVal).trigger('change');
				}
			}
		}
	}

	listenForREQUESTOR_EMAILChange() {
		$(document).on('change', '#REQUESTOR_EMAIL', function () {
			var email = $('#REQUESTOR_EMAIL').val().trim().toLowerCase();

			var IBMRegex = RegExp('ibm.com$');
			var oceanRegex = RegExp('ocean.ibm.com$');
			var kyndrylRegex = RegExp('kyndryl.com$');

			var ibmEmailAddress = IBMRegex.test(email);
			var oceanEmailAddress = oceanRegex.test(email);
			var kyndrylEmailAddress = kyndrylRegex.test(email);

			if (kyndrylEmailAddress) {
				$("input[name='Submit']").attr('disabled', false);
				$('#REQUESTOR_EMAIL').css("background-color", "LightGreen");
				$("#IBMNotAllowed").hide();
			} else {
				$('input[name="Submit"]').attr('disabled', true);
				$('#REQUESTOR_EMAIL').css("background-color", "LightPink");
				$("#IBMNotAllowed").hide();
				if (ibmEmailAddress || oceanEmailAddress) {
					if ($('#REQUESTOR_EMAIL').val() !== $("#originalREQUESTOR_EMAIL").val()) {
						$("#IBMNotAllowed").show();
					}
				}
			}
		});
	}

	listenForILC_WORK_ITEMChange() {
		alert('listenForILC_WORK_ITEMChange');
		$(document).on('change', '#ILC_WORK_ITEM', function () {

			var invalid = false;

			var ILC_1Object = document.getElementById('ILC_WORK_ITEM');
			var ILC_1Value = '';
			if (ILC_1Object !== null) {
				ILC_1Value = ILC_1Object.value.trim().toLowerCase();
			}

			// check provided values
			if (ILC_1Value != '') {
				if (ILC_1Value.length != 9) {
					invalid = true;
				}
			}

			// display fields statuses
			var classCss_1 = Rfs.colorWhite;
			if (invalid) {
				classCss_1 = Rfs.colorRed;
			} else {
				classCss_1 = Rfs.colorGreen;
			}
			if (ILC_1Object !== null) {
				ILC_1Object.style.backgroundColor = classCss_1;
			}

			// display final message
			if (invalid) {
				$("#invalidILCWorkItems").show();
				$('input[name="Submit"]').attr('disabled', true);
			} else {
				$("#invalidILCWorkItems").hide();
				$("input[name='Submit']").attr('disabled', false);
			}
		});
	}

	destroyRfsReport() {
		$('#rfsTable_id').DataTable().destroy();
	}
}

const rfs = new Rfs();

export { rfs as default };