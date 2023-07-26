/**
 *
 */

let ResourceRequest = await cacheBustImport('./modules/resourceRequest.js');

class RRListActions {

	static startDateColumnIndex = 21;
	static endDateColumnIndex = 24;
	static statusColumnIndex = 28;

	constructor() {
		// List action buttons
		this.listenForResetReport();
		this.listenForUnallocated();
		this.listenForCompleteable();
		this.listenForPlannedOnly();
		this.listenForActiveOnly();
		this.listenForRemovePassed();
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
				.column(RRListActions.statusColumnIndex).search('Planned')
				.draw();
		});
	}

	listenForActiveOnly() {
		$(document).on('click', '#activeOnly', function (e) {
			ResourceRequest.table
				.column(RRListActions.statusColumnIndex).search('Active')
				.draw();
		});
	}

	listenForRemovePassed() {
		$(document).on('click', '#removePassed', function (e) {
			ResourceRequest.table
				.column(RRListActions.statusColumnIndex).search("");
			$.fn.dataTable.ext.search.push(
				function (settings, data, dataIndex) {
					if (data[RRListActions.statusColumnIndex].includes('Completed')) {
						return false;
					}
					return true;
				}
			);
			ResourceRequest.table.draw();
			$.fn.dataTable.ext.search.pop();
		});
	}

	listenForResetReport() {
		$(document).on('click', '#resetReport', function (e) {
			ResourceRequest.table.columns().visible(true, false);
			ResourceRequest.table.columns([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 19, 22, 23, 25, 26, 27, 29, 30, 31]).visible(false, false);
			ResourceRequest.table.columns
				.adjust()
				.column(RRListActions.startDateColumnIndex).search("")
				.column(RRListActions.endDateColumnIndex).search("")
				.column(RRListActions.statusColumnIndex).search("")
				.draw(false);
		});
	}
}

const resourceRequestListActions = new RRListActions();

export { resourceRequestListActions as default };