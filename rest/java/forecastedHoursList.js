/**
 *
 */

let rfsSelect = await cacheBustImport('./modules/selects/rfs.js');

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');

class forecastedHoursList {

    table;

    constructor() {
        // forecastedHoursTable_id
        this.initialiseTable();
    }

    initialiseTable() {
        // Setup - add a text input to each footer cell
        $('#forecastedHoursTable_id tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });

        // DataTable
        this.table = $('#forecastedHoursTable_id').DataTable({
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
                "url": "ajax/populateForecastedHoursTable.php",
                "type": "GET",
            },
            drawCallback: function (row, data) {
                $("[data-toggle='toggle']").bootstrapToggle('destroy');
                $("[data-toggle='toggle']").bootstrapToggle({
                    on: 'Enabled',
                    off: 'Disabled'
                });
            }
        });
        // Apply the search
        tableSearch(this.table);
    }
}

const ForecastedHoursList = new forecastedHoursList();

rfsSelect.listenForSelectChange(ForecastedHoursList.table);

export { ForecastedHoursList as default };