/**
 *
 */

let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

let rfsSelect = await cacheBustImport('./modules/selects/rfs.js');
let valueStreamSelect = await cacheBustImport('./modules/selects/valueStream.js');
let businessUnitSelect = await cacheBustImport('./modules/selects/businessUnit.js');
let requestorSelect = await cacheBustImport('./modules/selects/requestor.js');

let Rfs = await cacheBustImport('./modules/rfs.js');

let RfsEditBox = await cacheBustImport('./modules/boxes/rfs/RfsEditBox.js');
let editRfsPcrBox = await cacheBustImport('./modules/boxes/pcr/editRfsPcrBox.js');
let RfsArchiveBox = await cacheBustImport('./modules/boxes/rfs/RfsArchiveBox.js');
let RfsDeleteBox = await cacheBustImport('./modules/boxes/rfs/RfsDeleteBox.js');
let RfsGoLiveBox = await cacheBustImport('./modules/boxes/rfs/RfsGoLiveBox.js');
// let RfsSwitchIdBox = await cacheBustImport('./modules/boxes/rfs/RfsSwitchIdBox.js');
// let RfsExtendBox = await cacheBustImport('./modules/boxes/rfs/RfsExtendBox.js');
// let RfsSlipTimeBox = await cacheBustImport('./modules/boxes/rfs/RfsSlipTimeBox.js');

let onReject = await cacheBustImport('./modules/tables/onReject.js');
let thenableTable = await cacheBustImport('./modules/tables/thenableRfsTable.js');

class rfsList {

	listenForLiveArchiveEvents(table) {
		var $this = this;
		$(document).on('change', 'input[name^="pipelineLiveArchive"]', function (e) {
			var value = $(this).val();
			setCookie('pipelineLiveArchiveChecked', value, 7);
            table.ajax.reload();
		});
	}
}

const RfsList = new rfsList();

Rfs.table = thenableTable;

RfsList.listenForLiveArchiveEvents(thenableTable);

// const onTableInitialized = (table) => {
	rfsSelect.listenForSelectChange(thenableTable);
	valueStreamSelect.listenForSelectChange(thenableTable);
	businessUnitSelect.listenForSelectChange(thenableTable);
	requestorSelect.listenForSelectChange(thenableTable);
// };
// thenableTable.then(onTableInitialized, onReject);

const EditRfsPcrBox = new editRfsPcrBox(Rfs);