/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let BandEntry = await cacheBustImport('./bandEntry.js');

let editBandBox = await cacheBustImport('./modules/boxes/band/editBandBox.js');
let deleteRecordBox = await cacheBustImport('./modules/boxes/band/deleteRecordBox.js');

class bandsList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#bandTable').DataTable({
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
                "url": "ajax/populateBandTable.php",
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

const BandsList = new bandsList();
BandEntry.table = BandsList.table;

const EditBandBox = new editBandBox(BandsList);
const DeleteRecordBox = new deleteRecordBox(BandsList);

export { BandsList as default };