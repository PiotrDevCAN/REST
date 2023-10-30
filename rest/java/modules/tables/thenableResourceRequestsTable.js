/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');

const thenableResourceRequestsTable = {

	table: null,

	// pass onFulfilled and onReject callback functions while execution of then method
	then: function (onFulfilled, onReject) {

		// Setup - add a text input to each footer cell
		$('#resourceRequestsTable_id tfoot th').each(function () {
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search ' + title + '" />');
		});
		// Show DataTable
		$('#resourceRequestsTable_id').show();

		$('#resourceRequestsTable_id')
			.on('error.dt', function () {
				onReject(new Error('DT failed'));
			});

		// DataTable
		this.table = $('#resourceRequestsTable_id').DataTable({
			initComplete: (settings, json) => {
				console.log('DataTables has finished its initialisation.');
				onFulfilled(this.table);
			},
			language: {
				emptyTable: "Please select Organisation, Business Unit and/or RFS from dropdowns above"
			},
			ajax: {
				url: 'ajax/populateResourceRequestHTMLTable.php',
				data: function (d) {
					// d.pipelineLiveArchive = $("input:radio[name=pipelineLiveArchive]:checked").val();
					d.pipelineLiveArchive = $('input[name="pipelineLiveArchive"]').val();
					d.archiveLive = $('#archiveLive').prop('checked');
					d.rfsid = $('#selectRfs option:selected').val();
					d.organisation = $('#selectOrganisation option:selected').val();
					d.businessunit = $('#selectBusinessUnit option:selected').val();
				},
				type: 'POST',
			},
			autoWidth: false,
			deferRender: true,
			processing: true,
			responsive: true,
			colReorder: true,
			dom: 'Blfrtip',
			buttons: [
				'colvis',
				$.extend(true, {}, buttonCommon, {
					extend: 'excelHtml5',
					exportOptions: {
						orthogonal: 'sort',
						stripHtml: true,
						stripNewLines: false
					},
					filename: 'REST_Export',
					customize: function (xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
					}
				}),
				$.extend(true, {}, buttonCommon, {
					extend: 'csvHtml5',
					exportOptions: {
						orthogonal: 'sort',
						stripHtml: true,
						stripNewLines: false
					},
					filename: 'REST_Export',
				}),
				$.extend(true, {}, buttonCommon, {
					extend: 'print',
					exportOptions: {
						orthogonal: 'sort',
						stripHtml: true,
						stripNewLines: false
					}
				})
			],
			columns: [
				{ data: "RFS_ID", defaultContent: "", visible: false },
				{ data: "PRN", defaultContent: "", visible: false },
				{ data: "PROJECT_TITLE", defaultContent: "", visible: false },
				{ data: "PROJECT_CODE", defaultContent: "", visible: false },
				{ data: "REQUESTOR_NAME", defaultContent: "", visible: false },
				{ data: "REQUESTOR_EMAIL", defaultContent: "", visible: false },
				{ data: "VALUE_STREAM", defaultContent: "", visible: false },
				{ data: "BUSINESS_UNIT", defaultContent: "", visible: false },
				{ data: "ILC_WORK_ITEM", defaultContent: "", visible: false },
				{ data: "ILC_WORK_ITEM_WEEKDAY_OVERTIME", defaultContent: "", visible: false },
				{ data: "ILC_WORK_ITEM_WEEKEND_OVERTIME", defaultContent: "", visible: false },
				{ data: "RFS_START_DATE", defaultContent: "", visible: false },
				{ data: "RFS_END_DATE", defaultContent: "", visible: false },
				{ data: "RFS_TYPE", defaultContent: "", visible: false },
				{ data: "RFS_STATUS", defaultContent: "", visible: false },
				{ data: "ARCHIVE", defaultContent: "", visible: false },
				{ data: "RFS_CREATOR", defaultContent: "", visible: false },
				{ data: "RFS_CREATED_TIMESTAMP", defaultContent: "", visible: false },
				{ data: "LINK_TO_PGMP", defaultContent: "", visible: false },
				{ data: "RESOURCE_REFERENCE", defaultContent: "", visible: false },
				{ data: "RFS", defaultContent: "", visible: true, render: { _: 'display', sort: 'sort' } },
				{ data: "ORGANISATION", defaultContent: "", visible: true, render: { _: 'display', sort: 'sort' }, },
				{ data: "SERVICE", defaultContent: "", visible: false },
				{ data: "DESCRIPTION", defaultContent: "", visible: true },
				{ data: "START_DATE", defaultContent: "", visible: true, render: { _: 'display', sort: 'sort' }, },
				{ data: "END_DATE", defaultContent: "", visible: false, render: { _: 'display', sort: 'sort' }, },
				{ data: "TOTAL_HOURS", defaultContent: "", visible: false, render: { _: 'display', sort: 'sort' }, },
				{ data: "RESOURCE_NAME", defaultContent: "", visible: true, render: { _: 'display', sort: 'sort' }, },
				{ data: "RESOURCE_EMAIL_ADDRESS", defaultContent: "", visible: false },
				{ data: "RESOURCE_KYN_EMAIL_ADDRESS", defaultContent: "", visible: false },
				{ data: "RR_CREATOR", defaultContent: "", visible: false },
				{ data: "RR_CREATED_TIMESTAMP", defaultContent: "", visible: false },
				{ data: "CLONED_FROM", defaultContent: "", visible: false },
				{ data: "STATUS", defaultContent: "", visible: true },
				{ data: "RATE_TYPE", defaultContent: "", visible: true },
				{ data: "HOURS_TYPE", defaultContent: "", visible: true },
				{ data: "RR", defaultContent: "", visible: false },
				// { data: "MONTH_01"         ,defaultContent: "",visible:true},
				// { data: "MONTH_02"         ,defaultContent: "",visible:true},
				// { data: "MONTH_03"         ,defaultContent: "",visible:true},
				// { data: "MONTH_04"         ,defaultContent: "",visible:true},
				// { data: "MONTH_05"         ,defaultContent: "",visible:true},
				// { data: "MONTH_06"         ,defaultContent: "",visible:true},
			]
		});

		// Apply the search
		$(this.table.column(16).header()).text('RFS:RR');
		// Apply the search
		tableSearch(this.table);
	}
};

export { thenableResourceRequestsTable as default };