/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');
let ResourceTraitEntry = await cacheBustImport('./resourceTraitEntry.js');

let editResourceTraitBox = await cacheBustImport('./modules/boxes/resourceTrait/editResourceTraitBox.js');
let deleteRecordBox = await cacheBustImport('./modules/boxes/resourceTrait/deleteRecordBox.js');

class resourceTraitsList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // Setup - add a text input to each footer cell
        $('#resourceTraitTable tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });

        // DataTable
        this.table = $('#resourceTraitTable').DataTable({
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
                "url": "ajax/populateResourceTraitsTable.php",
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
                { data: "ID", "defaultContent": "" },
                { data: "RESOURCE_NAME", "defaultContent": "" },
                { data: "RESOURCE_TYPE", "defaultContent": "" },
                { data: "PS_BAND", "defaultContent": "" },
                { data: "PS_BAND_OVERRIDE", "defaultContent": "" },
            ]
        });
        // Apply the search
        tableSearch(this.table);
    }
}

const ResourceTraitsList = new resourceTraitsList();
ResourceTraitEntry.table = ResourceTraitsList.table;

const EditResourceTraitBox = new editResourceTraitBox(ResourceTraitsList);
const DeleteRecordBox = new deleteRecordBox(ResourceTraitsList);

export { ResourceTraitsList as default };