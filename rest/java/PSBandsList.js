/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let PsBandEntry = await cacheBustImport('./PSBandEntry.js');

let editPSBandBox = await cacheBustImport('./modules/boxes/PSBand/editPSBandBox.js');
let deleteRecordBox = await cacheBustImport('./modules/boxes/PSBand/deleteRecordBox.js');

class PSBandsList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#PSBandTable').DataTable({
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
                "url": "ajax/populatePSBandTable.php",
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
                { data: "BAND_ID", "defaultContent": "" },
                { data: "BAND", "defaultContent": "" },
            ]
        });
    }
}

const PsBandsList = new PSBandsList();
PsBandEntry.table = PsBandsList.table;

const EditBandBox = new editPSBandBox(PsBandsList);
const DeleteRecordBox = new deleteRecordBox(PsBandsList);

export { PsBandsList as default };