/**
 *
 */

let organisationSelect = await cacheBustImport('./modules/selects/organisation.js');
let businessUnitSelect = await cacheBustImport('./modules/selects/businessUnit.js');
let rfsSelect = await cacheBustImport('./modules/selects/rfs.js');

let BankHolidays = await cacheBustImport('./modules/dataSources/bankHolidays.js');
let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');

let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');
let RRListActions = await cacheBustImport('./modules/boxes/resourceRequest/RRListActions.js');

let RREditBox = await cacheBustImport('./modules/boxes/resourceRequest/RREditBox.js');
let RRCloneBox = await cacheBustImport('./modules/boxes/resourceRequest/RRCloneBox.js');
let RRArchiveBox = await cacheBustImport('./modules/boxes/resourceRequest/RRArchiveBox.js');
let RRDeleteBox = await cacheBustImport('./modules/boxes/resourceRequest/RRDeleteBox.js');

let RRResourceNameBox = await cacheBustImport('./modules/boxes/resourceRequest/RRResourceNameBox.js');
let RRHoursBox = await cacheBustImport('./modules/boxes/resourceRequest/RRHoursBox.js');
let RRDiaryBox = await cacheBustImport('./modules/boxes/resourceRequest/RRDiaryBox.js');
let RRIndicateCompletedBox = await cacheBustImport('./modules/boxes/resourceRequest/RRIndicateCompletedBox.js');
let RRChangeStatusCompletedBox = await cacheBustImport('./modules/boxes/resourceRequest/RRChangeStatusCompletedBox.js');

let editResourceTraitBox = await cacheBustImport('./modules/boxes/resourceRequest/RREditResourceTraitBox.js');

let onReject = await cacheBustImport('./modules/tables/onReject.js');
let thenableTable = await cacheBustImport('./modules/tables/thenableResourceRequestsTable.js');

class resourceRequestsList {

	constructor() {
		$('#pleaseWaitMessage').html('Please wait while resource list is fetched');
	}
}

const RRList = new resourceRequestsList();
// removed functions
// var allowPast = true;
// RRList.initialiseDateSelect(allowPast); // This was causing the use of up and down arrows to change the Date Field on the form which we didn't want.
// removed functions
// RRList.buildResourceReport(false).then(function (table) {
// 	console.log('Success, resource request list');
// 	organisationSelect.listenForSelectChange(table);
// 	businessUnitSelect.listenForSelectChange(table);
// 	rfsSelect.listenForSelectChange(table);
// });

ResourceRequest.table = thenableTable;

// const onTableInitialized = (table) => {
	organisationSelect.listenForSelectChange(thenableTable);
	businessUnitSelect.listenForSelectChange(thenableTable);
	rfsSelect.listenForSelectChange(thenableTable);
// };
// thenableTable.then(onTableInitialized, onReject);

const EditResourceTraitBox = new editResourceTraitBox(thenableTable);

$('[data-toggle="tooltip"]').tooltip();