/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let BusinessUnitEntry = await cacheBustImport('./businessUnitEntry.js');

class businessUnitsList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#businessUnitTable').DataTable({
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
                "url": "ajax/populateBusinessUnitTable.php",
                "type": "GET",
            },
            columns: [
                { data: "BUSINESS_UNIT", "defaultContent": "" }
            ]
        });
    }
}

const BusinessUnitsList = new businessUnitsList();
BusinessUnitEntry.table = BusinessUnitsList.table;

export { BusinessUnitsList as default };