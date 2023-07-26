/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');
let BespokeRateEntry = await cacheBustImport('./bespokeRateEntry.js');

let editBespokeRateBox = await cacheBustImport('./modules/boxes/bespokeRate/editBespokeRateBox.js');
// let editResourceTypeBox = await cacheBustImport('./modules/boxes/bespokeRate/editResourceTypeBox.js');
// let editPSBandBox = await cacheBustImport('./modules/boxes/bespokeRate/editPSBandBox.js');
let deleteRecordBox = await cacheBustImport('./modules/boxes/bespokeRate/deleteRecordBox.js');

class bespokeRateList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // Setup - add a text input to each footer cell
        $('#bespokeRateTable tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });

        // DataTable
        this.table = $('#bespokeRateTable').DataTable({
            autoWidth: false,
            processing: true,
            responsive: false,
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
                    }
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
            ajax: {
                "url": "ajax/populateBespokeRateTable.php",
                "type": "GET",
            },
            drawCallback: function (row, data) {
                $("[data-toggle='toggle']").bootstrapToggle('destroy');
                $("[data-toggle='toggle']").bootstrapToggle({
                    on: 'Enabled',
                    off: 'Disabled'
                });
            },
            columns: [
                { data: "RFS_ID", "defaultContent": "" },
                { data: "RESOURCE_REFERENCE", "defaultContent": "" },
                { data: "RESOURCE_NAME", "defaultContent": "" },
                { data: "RESOURCE_TYPE", "defaultContent": "" },
                { data: "PS_BAND", "defaultContent": "" },
            ]
        });
        // Apply the search
        tableSearch(this.table);
    }
}

const BespokeRateList = new bespokeRateList();
BespokeRateEntry.table = BespokeRateList.table;

const EditBespokeRateBox = new editBespokeRateBox(BespokeRateList);
// const EditResourceTypeBox = new editResourceTypeBox(BespokeRateList);
// const EditPSBandBox = new editPSBandBox(BespokeRateEntry);
const DeleteRecordBox = new deleteRecordBox(BespokeRateList);

export { BespokeRateList as default };