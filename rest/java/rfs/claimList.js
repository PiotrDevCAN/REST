/**
 *
 */

var rfsSelect = await cacheBustImport('./modules/selects/rfs.js');
var valueStreamSelect = await cacheBustImport('./modules/selects/valueStream.js')
var businessUnitSelect = await cacheBustImport('./modules/selects/businessUnit.js');
var requestorSelect = await cacheBustImport('./modules/selects/requestor.js');

// let Rfs = await cacheBustImport('./modules/rfs.js');

let onReject = await cacheBustImport('./modules/tables/onReject.js');
let thenableTable = await cacheBustImport('./modules/tables/thenableRfsClaimTable.js');

import thenableTable2 from "./../modules/tables/thenableRfsClaimTable.js";

class claimList {

}

rfsSelect.listenForSelectChange(thenableTable);
valueStreamSelect.listenForSelectChange(thenableTable);
businessUnitSelect.listenForSelectChange(thenableTable);
requestorSelect.listenForSelectChange(thenableTable);