/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let BusinessUnitsValuesStreamsEntry = await cacheBustImport('./businessUnitsValuesStreamsEntry.js');

class businessUnitsValuesStreamsList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#valueStreamTable').DataTable({
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
                "url": "ajax/populateValueStreamTable.php",
                "type": "GET",
            },
            columns: [
                { data: "VALUE_STREAM", "defaultContent": "" },
                { data: "BUSINESS_UNIT", "defaultContent": "" }
            ]
        });
    }
}

const BusinessUnitsValuesStreamsList = new businessUnitsValuesStreamsList();
BusinessUnitsValuesStreamsEntry.table = BusinessUnitsValuesStreamsList.table;

export { BusinessUnitsValuesStreamsList as default };