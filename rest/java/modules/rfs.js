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
		$(document).on('change', '#ILC_WORK_ITEM, #ILC_WORK_ITEM_WEEKDAY_OVERTIME, #ILC_WORK_ITEM_WEEKEND_OVERTIME', function () {

			var invalid = false;

			var invalidFieldNo_1 = false;
			var invalidFieldNo_2 = false;
			var invalidFieldNo_3 = false;

			var setFieldNo_1 = false;
			var setFieldNo_2 = false;
			var setFieldNo_3 = false;

			var ILC_1Object = document.getElementById('ILC_WORK_ITEM');
			var ILC_1Value = '';
			if (ILC_1Object !== null) {
				ILC_1Value = ILC_1Object.value.trim().toLowerCase();
			}
			var ILC_2Object = document.getElementById('ILC_WORK_ITEM_WEEKDAY_OVERTIME');
			var ILC_2Value = '';
			if (ILC_2Object !== null) {
				ILC_2Value = ILC_2Object.value.trim().toLowerCase();
			}
			var ILC_3Object = document.getElementById('ILC_WORK_ITEM_WEEKEND_OVERTIME');
			var ILC_3Value = '';
			if (ILC_3Object !== null) {
				ILC_3Value = ILC_3Object.value.trim().toLowerCase();
			}

			if (ILC_1Value != '' || ILC_2Value != '' || ILC_3Value != '') {

				// check provided values
				if (ILC_1Value != '') {
					setFieldNo_1 = true;
					if (ILC_1Value.length != 8) {
						invalid = true;
						invalidFieldNo_1 = true;
					}
				}
				if (ILC_2Value != '') {
					setFieldNo_2 = true;
					if (ILC_2Value.length != 8) {
						invalid = true;
						invalidFieldNo_2 = true;
					}
				}
				if (ILC_3Value != '') {
					setFieldNo_3 = true;
					if (ILC_3Value.length != 8) {
						invalid = true;
						invalidFieldNo_3 = true;
					}
				}
			} else {
				invalid = true;
				invalidFieldNo_1 = true;
				invalidFieldNo_2 = true;
				invalidFieldNo_3 = true;
			}

			// compare provided values
			if (invalid === false) {
				if (setFieldNo_1 && setFieldNo_2 && ILC_1Value === ILC_2Value) {
					invalid = true;
					invalidFieldNo_1 = true;
					invalidFieldNo_2 = true;
				}
				if (setFieldNo_1 && setFieldNo_3 && ILC_1Value === ILC_3Value) {
					invalid = true;
					invalidFieldNo_1 = true;
					invalidFieldNo_3 = true;
				}
				if (setFieldNo_2 && setFieldNo_3 && ILC_2Value === ILC_3Value) {
					invalid = true;
					invalidFieldNo_2 = true;
					invalidFieldNo_3 = true;
				}
			}

			// display fields statuses
			var classCss_1 = Rfs.colorWhite;
			if (setFieldNo_1) {
				classCss_1 = Rfs.colorGreen;
				if (invalidFieldNo_1) {
					classCss_1 = Rfs.colorRed;
				}
			} else {
				if (invalidFieldNo_1) {
					classCss_1 = Rfs.colorRed;
				}
			}
			if (ILC_1Object !== null) {
				ILC_1Object.style.backgroundColor = classCss_1;
			}

			var classCss_2 = Rfs.colorWhite;
			if (setFieldNo_2) {
				classCss_2 = Rfs.colorGreen;
				if (invalidFieldNo_2) {
					classCss_2 = Rfs.colorRed;
				}
			} else {
				if (invalidFieldNo_2) {
					classCss_2 = Rfs.colorRed;
				}
			}
			if (ILC_2Object !== null) {
				ILC_2Object.style.backgroundColor = classCss_2;
			}

			var classCss_3 = Rfs.colorWhite;
			if (setFieldNo_3) {
				classCss_3 = Rfs.colorGreen;
				if (invalidFieldNo_3) {
					classCss_3 = Rfs.colorRed;
				}
			} else {
				if (invalidFieldNo_3) {
					classCss_3 = Rfs.colorRed;
				}
			}
			if (ILC_3Object !== null) {
				ILC_3Object.style.backgroundColor = classCss_3;
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