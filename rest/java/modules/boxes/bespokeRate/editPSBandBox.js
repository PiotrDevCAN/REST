/**
 *
 */

let editBox = await cacheBustImport('./modules/boxes/editPSBandBox.js');

class editPSBandBox extends editBox {

    static ajaxUrl = 'saveBespokeRatePSBand.php';

	constructor(parent) {
		super(parent, editPSBandBox.ajaxUrl);
	}
}

export { editPSBandBox as default };