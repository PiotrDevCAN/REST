/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let ServiceEntry = await cacheBustImport('./serviceEntry.js');

class servicesList {

    table;

    constructor() {
        this.initialiseTable();
    }

    initialiseTable() {
        // DataTable
        this.table = $('#serviceTable').DataTable({
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
                "url": "ajax/populateServiceTable.php",
                "type": "GET",
            },
            columns: [
                { data: "SERVICE", "defaultContent": "" }
            ]
        });
    }
}

const ServicesList = new servicesList();
ServiceEntry.table = ServicesList.table;

export { ServicesList as default };