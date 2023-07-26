/**
 *
 */

let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');

const thenableRfsClaimTable = {

	table: null,

	// pass onFulfilled and onReject callback functions while execution of then method
	then: function (onFulfilled, onReject) {

		// Setup - add a text input to each footer cell
		$('#claimTable_id tfoot th').each(function () {
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search ' + title + '" />');
		});
		// Show DataTable
		$('#claimTable_id').show();

		$('#claimTable_id')
			.on('error.dt', function () {
				onReject(new Error('DT failed'));
			});

		// DataTable
		this.table = $('#claimTable_id').DataTable({
			initComplete: (settings, json) => {
				console.log('DataTables has finished its initialisation.');
				onFulfilled(this.table);
			},
			language: {
				emptyTable: "Please select one or more of :  RFS, Value Stream, Business Unit, Requestor from above"
			},
			ajax: {
				url: 'ajax/populateClaimHTMLTable.php',
				type: 'POST',
				data: function (d) {
					d.rfsid = $('#selectRfs option:selected').val();
					d.valuestream = $('#selectValueStream option:selected').val();
					d.businessunit = $('#selectBusinessUnit option:selected').val();
					d.requestor = $('#selectRequestor option:selected').val();
				},
			},
			autoWidth: true,
			deferRender: true,
			responsive: true,
			processing: true,
			colReorder: true,
			dom: 'Blfrtip',
			buttons: [
				'colvis',
				'excelHtml5',
				'csvHtml5',
				'print'
			],
			columns: [
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				{ defaultContent: "", visible: true, render: { _: 'display', sort: 'sort' }, },
				{ defaultContent: "", visible: false, render: { _: 'display', sort: 'sort' }, },
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
			],
		});
		this.table.columns([1, 2, 3, 4, 5, 8, 9, 10, 19, 20, 21]).visible(false, false);
		this.table.columns.adjust().draw(false);
		// Apply the search
		tableSearch(this.table);
	}
};

export { thenableRfsClaimTable as default };