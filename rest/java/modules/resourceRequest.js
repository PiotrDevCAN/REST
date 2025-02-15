/**
 *
 */

class ResourceRequest {

	table;
	resourceNamesForSelect2 = [];
	ModalendEarlyPicker;

	disableCache = false;

	constructor() {

	}

	showMessageArea() {
		$('#messageArea').html("<div class='col-sm-4'></div><div class='col-sm-4'><h4>Form loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4><br/><small>This may take a few seconds</small></div><div class='col-sm-4'></div>");
	}

	clearMessageArea() {
		$('#messageArea').html("");
	}

	listenForGlobalModalShown() {
		this.clearMessageArea();
	}

	listenForGlobalModalHidden() {
		$(document).on('shown.bs.modal', function (e) {
			helper.unlockButton();
		});
	}

	reloadTable() {
		this.disableCache = false;
		this.table.ajax.reload();
	}

	refreshAndReloadTable() {
		this.disableCache = true;
		this.table.ajax.reload();
	}
}

const resourceRequest = new ResourceRequest();
resourceRequest.listenForGlobalModalShown();
resourceRequest.listenForGlobalModalHidden();

export { resourceRequest as default };