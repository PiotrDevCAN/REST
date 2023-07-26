/**
 *
 */

let Rfs = await cacheBustImport('./modules/rfs.js');

class noMatchBUList {

	initialisenoMatchBUTable() {
		// return false;
		// Setup - add a text input to each footer cell
		$('#noMatchBUTable_id tfoot th').each(function () {
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search ' + title + '" />');
		});
		// Show DataTable
		$('#noMatchBUTable_id').show();
		// DataTable
		Rfs.table = $('#noMatchBUTable_id').DataTable({
			language: {
				//emptyTable: "Please select one or more of :  RFS, Value Stream, Business Unit, Requestor from above"
			},
			ajax: {
				url: 'ajax/populateNoMatchBUsHTMLTable.php',
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
				{ data: "RFS_ID", defaultContent: "", visible: true },
				{ data: "RESOURCE_REFERENCE", defaultContent: "", visible: true },
				{ data: "RESOURCE_NAME", defaultContent: "", visible: true },
				{ data: "RFS_BUSINESS_UNIT", defaultContent: "", visible: true },
				{ data: "INDIVIDUAL_BUSINESS_UNIT", defaultContent: "", visible: true },
				{ data: "DIARY", defaultContent: "", visible: true },
				{ data: "MONTH_01" ,defaultContent: "",visible:true},
				{ data: "MONTH_02" ,defaultContent: "",visible:true},
				{ data: "MONTH_03" ,defaultContent: "",visible:true},
				{ data: "MONTH_04" ,defaultContent: "",visible:true},
				{ data: "MONTH_05" ,defaultContent: "",visible:true},
				{ data: "MONTH_06" ,defaultContent: "",visible:true},
			]
		});
		// Rfs.table.columns([1, 2, 3, 4]).visible(false, false);
		// Rfs.table.columns.adjust().draw(false);
		Rfs.applySearch();

		// set rows roles
		// $('#noMatchBUTable_id tbody tr').each(function () {
		// 	$(this).attr('role', 'row');
		// 	$(this).prop('role', 'row');
		// });
	}

    buildNoMatchBUReport(getColumnsFromAjax) {
		var $this = this;
		if (getColumnsFromAjax == null) {
			var formData = $('form').serialize();
			$.ajax({
				url: "ajax/createNoMatchBUsHTMLTable.php",
				type: 'POST',
				data: formData,
				before: function () {
					$('#noMatchBUTableDiv').html('<h2>Table being built</h2>');
				},
				success: function (result) {
					$('#noMatchBUTable_id').DataTable().destroy();
					$("#noMatchBUTableDiv").html(result);
					$this.initialisenoMatchBUTable();
				}
			});
		} else {
			this.initialisenoMatchBUTable();
		}
	}
}

const NoMatchBUList = new noMatchBUList();
NoMatchBUList.buildNoMatchBUReport(false);