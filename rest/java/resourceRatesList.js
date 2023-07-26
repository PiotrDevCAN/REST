/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let resourceRateEntry = await cacheBustImport('./resourceRateEntry.js');

let editResourceRateBox = await cacheBustImport('./modules/boxes/resourceRate/editResourceRateBox.js');
let deleteRecordBox = await cacheBustImport('./modules/boxes/resourceRate/deleteRecordBox.js');

class resourceRatesList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#resourceRateTable').DataTable({
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
                "url": "ajax/populateResourceRateTable.php",
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
                { data: "RESOURCE_TYPE", "defaultContent": "" },
                { data: "PS_BAND", "defaultContent": "" },
                // { data: "BAND", "defaultContent": "" },
                { data: "TIME_PERIOD_START", "defaultContent": "" },
                { data: "TIME_PERIOD_END", "defaultContent": "" },
                { data: "DAY_RATE", "defaultContent": "" },
                { data: "HOURLY_RATE", "defaultContent": "" },
            ]
        });
    }
}

const ResourceRatesList = new resourceRatesList();
resourceRateEntry.table = ResourceRatesList.table;

const EditResourceRateBox = new editResourceRateBox(ResourceRatesList);
const DeleteRecordBox = new deleteRecordBox(ResourceRatesList);

export { ResourceRatesList as default };