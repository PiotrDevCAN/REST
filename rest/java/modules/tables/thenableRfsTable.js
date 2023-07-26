/**
 *
 */

let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');

const thenableRfsTable = {

	table: null,

	// pass onFulfilled and onReject callback functions while execution of then method
	then: function (onFulfilled, onReject) {

		// Setup - add a text input to each footer cell
		$('#rfsTable_id tfoot th').each(function () {
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search ' + title + '" />');
		});
		// Show DataTable
		$('#rfsTable_id').show();

		$('#rfsTable_id')
			.on('error.dt', function () {
				onReject(new Error('DT failed'));
			});

		// DataTable
		this.table = $('#rfsTable_id').DataTable({
			initComplete: (settings, json) => {
				console.log('DataTables has finished its initialisation.');
				onFulfilled(this.table);
			},
			language: {
				emptyTable: "Please select one or more of :  RFS, Value Stream, Business Unit, Requestor from above"
			},
			ajax: {
				url: 'ajax/populateRfsHTMLTable.php',
				type: 'POST',
				data: function (d) {
					d.rfsid = $('#selectRfs option:selected').val();
					d.valuestream = $('#selectValueStream option:selected').val();
					d.businessunit = $('#selectBusinessUnit option:selected').val();
					d.requestor = $('#selectRequestor option:selected').val();
					d.pipelineLiveArchive = $("input:radio[name=pipelineLiveArchive]:checked").val();
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
		this.table.columns([15]).visible(false, false);
		this.table.columns.adjust().draw(false);
		// Apply the search
		tableSearch(this.table);
	}
};

export { thenableRfsTable as default };