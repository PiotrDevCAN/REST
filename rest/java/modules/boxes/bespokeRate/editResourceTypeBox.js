/**
 *
 */

let editBox = await cacheBustImport('./modules/boxes/editResourceTypeBox.js');

class editResourceTypeBox extends editBox {

    static ajaxUrl = 'saveBespokeRateResourceType.php';

	constructor(parent) {
		super(parent, editResourceTypeBox.ajaxUrl);
	}
}

export { editResourceTypeBox as default };