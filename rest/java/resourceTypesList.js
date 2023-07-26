/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let ResourceTypeEntry = await cacheBustImport('./resourceTypeEntry.js');

let editResourceTypeBox = await cacheBustImport('./modules/boxes/resourceType/editResourceTypeBox.js');
let deleteRecordBox = await cacheBustImport('./modules/boxes/resourceType/deleteRecordBox.js');

class resourceTypesList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#resourceTypeTable').DataTable({
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
                "url": "ajax/populateResourceTypeTable.php",
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
                { data: "RESOURCE_TYPE_ID", "defaultContent": "" },
                { data: "RESOURCE_TYPE", "defaultContent": "" },
                { data: "HRS_PER_DAY", "defaultContent": "" },
            ]
        });
    }
}

const ResourceTypesList = new resourceTypesList();
ResourceTypeEntry.table = ResourceTypesList.table;

const EditResourceTypeBox = new editResourceTypeBox(ResourceTypesList);
const DeleteRecordBox = new deleteRecordBox(ResourceTypesList);

export { ResourceTypesList as default };