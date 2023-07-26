/**
 *
 */

let { checkCookie, setCookie, getCookie } = await cacheBustImport('./modules/cookies/cookiesHelper.js');

let rfsSelect = await cacheBustImport('./modules/selects/rfs.js');

let editRfsPcrBox = await cacheBustImport('./modules/boxes/pcr/editRfsPcrBox.js');
let archiveRfsPcrBox = await cacheBustImport('./modules/boxes/pcr/archiveRfsPcrBox.js');
let deleteRecordBox = await cacheBustImport('./modules/boxes/pcr/deleteRecordBox.js');

// let onReject = await cacheBustImport('./modules/tables/onReject.js');
let thenableTable = await cacheBustImport('./modules/tables/thenableRfsPcrTable.js');

class list {

    table;

}

const List = new list();
List.table = thenableTable;

// const onTableInitialized = (table) => {
rfsSelect.listenForSelectChange(thenableTable);
// };
// thenableTable.then(onTableInitialized, onReject);

const EditBandBox = new editRfsPcrBox(List);
const ArchiveRfsPcrBox = new archiveRfsPcrBox(List);
const DeleteRecordBox = new deleteRecordBox(List);

export { List as default };