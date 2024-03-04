/**
 *
 */

let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRListActions {

	static startDateColumnIndex = 24;
	static endDateColumnIndex = 25;
	static statusColumnIndex = 33;

	static resetColumns = [20, 21, 23, 24, 27, 33, 34, 35];

	constructor() {
		// List action buttons
		this.listenForUnallocated();
		this.listenForCompleteable();
		this.listenForPlannedOnly();
		this.listenForActiveOnly();
		this.listenForRemovePassed();
		this.listenForReportAll();
		this.listenForReportReload();
		this.listenForReportReset();
		// List action buttons
	}

	listenForUnallocated() {
		$(document).on('click', '#unallocated', function (e) {
			ResourceRequest.table
				.column(RRListActions.statusColumnIndex).search('New')
				.draw();
		});
	}

	listenForCompleteable() {
		$(document).on('click', '#completeable', function (e) {
			ResourceRequest.table
				.column(RRListActions.statusColumnIndex).search('Assigned')
				.draw();
		});
	}

	listenForPlannedOnly() {
		$(document).on('click', '#plannedOnly', function (e) {
			ResourceRequest.table
				.column(RRListActions.startDateColumnIndex).search('Planned')
				.draw();
		});
	}

	listenForActiveOnly() {
		$(document).on('click', '#activeOnly', function (e) {
			ResourceRequest.table
				.column(RRListActions.startDateColumnIndex).search('Active')
				.draw();
		});
	}

	listenForRemovePassed() {
		$(document).on('click', '#removePassed', function (e) {
			ResourceRequest.table
				.column(RRListActions.statusColumnIndex).search("");
			$.fn.dataTable.ext.search.push(
				function (settings, data, dataIndex) {
					if (data[RRListActions.startDateColumnIndex].includes('Completed')) {
						return false;
					}
					return true;
				}
			);
			ResourceRequest.table.draw();
			$.fn.dataTable.ext.search.pop();
		});
	}

	listenForReportAll() {
		$(document).on('click', '#reportAll', function (e) {
			$.fn.dataTableExt.afnFiltering.pop();
			ResourceRequest.table.columns().visible(true);
			ResourceRequest.table.columns().search("");
			ResourceRequest.table.order([0, "asc"]).draw();
		});
	}

	listenForReportReload() {
		$(document).on('click', '#reportReload', function (e) {
			$.fn.dataTableExt.afnFiltering.pop();
			ResourceRequest.reloadTable();
		});
	}

	listenForReportReset() {
		$(document).on('click', '#reportReset', function (e) {
			$.fn.dataTableExt.afnFiltering.pop();
			ResourceRequest.table.columns().visible(false, false);
			ResourceRequest.table.columns(RRListActions.resetColumns).visible(true);
			ResourceRequest.table.search("").order([0, "asc"]).draw();
		});
	}
}

const resourceRequestListActions = new RRListActions();

export { resourceRequestListActions as default };