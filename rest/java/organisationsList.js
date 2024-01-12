/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let OrganisationEntry = await cacheBustImport('./organisationEntry.js');

class organisationsList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#organisationTable').DataTable({
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
                "url": "ajax/populateOrganisationTable.php",
                "type": "GET",
            },
            columns: [
                { data: "ORGANISATION", "defaultContent": "" }
            ]
        });
    }
}

const OrganisationsList = new organisationsList();
OrganisationEntry.table = OrganisationsList.table;

export { OrganisationsList as default };