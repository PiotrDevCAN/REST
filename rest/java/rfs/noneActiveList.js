/**
 *
 */

let Rfs = await cacheBustImport('./modules/rfs.js');

class noneActiveList {

	initialiseNoneActiveTable() {
		// Setup - add a text input to each footer cell
		$('#noneActiveTable_id tfoot th').each(function () {
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search ' + title + '" />');
		});
		// Show DataTable
		$('#noneActiveTable_id').show();
		// DataTable
		Rfs.table = $('#noneActiveTable_id').DataTable({
			language: {
				//emptyTable: "Please select one or more of :  RFS, Value Stream, Business Unit, Requestor from above"
			},
			ajax: {
				url: 'ajax/populateNoneActiveHTMLTable.php',
				type: 'POST',
				data: function (d) {
					//     d.rfsid = $('#selectRfs option:selected').val();
					//     d.valuestream = $('#selectValueStream option:selected').val();
					//     d.businessunit = $('#selectBusinessUnit option:selected').val();
					//     d.requestor = $('#selectRequestor option:selected').val();
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
		Rfs.table.columns([1, 2, 3, 4, 5, 8, 9, 10, 19, 20, 21]).visible(false, false);
		Rfs.table.columns.adjust().draw(false);
		Rfs.applySearch();

		// set rows roles
		$('#noneActiveTable_id tbody tr').each(function () {
			$(this).attr('role', 'row');
			$(this).prop('role', 'row');
		});
	}

    buildNoneActiveReport(getColumnsFromAjax) {
		var $this = this;
		if (getColumnsFromAjax == null) {
			var formData = $('form').serialize();
			$.ajax({
				url: "ajax/createNoneActiveHTMLTable.php",
				type: 'POST',
				data: formData,
				before: function () {
					$('#noneActiveTableDiv').html('<h2>Table being built</h2>');
				},
				success: function (result) {
					$('#noneActiveTable_id').DataTable().destroy();
					$("#noneActiveTableDiv").html(result);
					$this.initialiseNoneActiveTable();
				}
			});
		} else {
			this.initialiseNoneActiveTable();
		}
	}
}

const NoneActiveList = new noneActiveList();
NoneActiveList.buildNoneActiveReport(false);