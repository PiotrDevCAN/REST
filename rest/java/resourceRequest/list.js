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

let onReject = await cacheBustImport('./modules/tables/onReject.js');
// let thenableTable = await cacheBustImport('./modules/tables/thenableResourceRequestsTable.js');
import { default as thenableTable } from '/rest/java/modules/tables/thenableResourceRequestsTable.js';

let VBACActiveResources = await cacheBustImport('./modules/dataSources/vbacActiveResources.js');

class resourceRequestsList {

	constructor() {
		$('#pleaseWaitMessage').html('Please wait while resource list is fetched');
	}
}

const RRList = new resourceRequestsList();

const promises = [];

// check for vBAC employees
let resourceNamesPromise = VBACActiveResources.getActiveResources();
promises.push(resourceNamesPromise);

// Promise.allSettled(promises)
Promise.all(promises)
	.then((results) => {
		// results.forEach((result) => console.log(result.status));
		const onTableInitialized = (obj, table) => {
			ResourceRequest.table = table;
			organisationSelect.listenForSelectChange(table);
			businessUnitSelect.listenForSelectChange(table);
			rfsSelect.listenForSelectChange(table);
		};
		thenableTable.then(onTableInitialized, onReject);
		$('[data-toggle="tooltip"]').tooltip();

	})
	.catch((err) => {
		console.log("error:", err);
	});