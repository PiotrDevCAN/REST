/**
 *
 */

let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');

const thenableRfsPcrTable = {

	table: null,

	// pass onFulfilled and onReject callback functions while execution of then method
	then: function (onFulfilled, onReject) {

		// Setup - add a text input to each footer cell
		$('#rfsPcrTable_id tfoot th').each(function () {
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search ' + title + '" />');
		});
		// Show DataTable
		$('#rfsPcrTable_id').show();

		$('#rfsPcrTable_id')
			.on('error.dt', function () {
				onReject(new Error('DT failed'));
			});

		// DataTable
		this.table = $('#rfsPcrTable_id').DataTable({
			initComplete: (settings, json) => {
				console.log('DataTables has finished its initialisation.');
				onFulfilled(this.table);
			},
			language: {
				emptyTable: "Please select one or more of :  RFS from above"
			},
			ajax: {
				url: 'ajax/populateRfsPcrHTMLTable.php',
				type: 'POST',
				data: function (d) {
					d.rfsid = $('#selectRfs option:selected').val();
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
		});
		this.table.columns.adjust().draw(false);
		// Apply the search
		tableSearch(this.table);
	}
};

export { thenableRfsPcrTable as default };