/**
 *
 */

let deleteBox = await cacheBustImport('./modules/boxes/deleteRecordBox.js');

class deleteRecordBox extends deleteBox {

	static ajaxUrl = 'deleteResourceType.php';

	constructor(parent) {
		super(parent, deleteRecordBox.ajaxUrl);
	}
}

export { deleteRecordBox as default };