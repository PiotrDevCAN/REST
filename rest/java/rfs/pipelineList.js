/**
 *
 */

let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');
let Rfs = await cacheBustImport('./modules/rfs.js');
let RfsDeleteBox = await cacheBustImport('./modules/boxes/rfs/RfsDeleteBox.js');
let RfsGoLiveBox = await cacheBustImport('./modules/boxes/rfs/RfsGoLiveBox.js');
let RfsEditBox = await cacheBustImport('./modules/boxes/rfs/RfsEditBox.js');
let editRfsPcrBox = await cacheBustImport('./modules/boxes/pcr/editRfsPcrBox.js');
let RfsArchiveBox = await cacheBustImport('./modules/boxes/rfs/RfsArchiveBox.js');

class pipelineList {

	initialisePipelineDataTable() {
		// Setup - add a text input to each footer cell
		$('#rfsTable_id tfoot th').each(function () {
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search ' + title + '" />');
		});
		// Show DataTable
		$('#rfsTable_id').show();
		// DataTable
		Rfs.table = $('#rfsTable_id').DataTable({
			ajax: {
				url: 'ajax/populatePipelineRfsHTMLTable.php',
				type: 'POST',
			},
			autoWidth: true,
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
		// Apply the search
		tableSearch(Rfs.table);
	}

	// function in pipeline list only
	/*
	listenShownModal() {
		var $this = this;
		$(document).on('shown.bs.modal',function(e){
			Rfs.preventDuplicateEntry();
			Rfs.listenForRfsFormSubmit();
			Rfs.refreshReportOnRfsUpdate();
		});
	}
	*/

	buildPipelineReport(getColumnsFromAjax) {
		var $this = this;
		if (getColumnsFromAjax == null) {
			var formData = $('form').serialize();
			$.ajax({
				url: "ajax/createPipelineHTMLTable.php",
				type: 'POST',
				data: formData,
				before: function () {
					$('#rfsTableDiv').html('<h2>Table being built</h2>');
				},
				success: function (result) {
					$('#rfsTable_id').DataTable().destroy();
					$("#rfsTableDiv").html(result);
					$this.initialisePipelineDataTable();
				}
			});
		} else {
			this.initialisePipelineDataTable();
		}
	}
}

const PipelineList = new pipelineList();
// PipelineList.listenShownModal();
PipelineList.buildPipelineReport(false);

const EditRfsPcrBox = new editRfsPcrBox(Rfs);