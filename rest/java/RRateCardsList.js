/**
 *
 */

let rfsSelect = await cacheBustImport('./modules/selects/rfs.js');

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let tableSearch = await cacheBustImport('./modules/functions/tableSearch.js');

let previewBespokeRate = await cacheBustImport('./modules/boxes/rateCard/previewBespokeRateBox.js');

class RRateCardsList {

    table;

    constructor() {
        // rateCardTable_id
        this.initialiseTable();
    }

    initialiseTable() {
        // Setup - add a text input to each footer cell
        $('#rateCardTable_id tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });

        // DataTable
        this.table = $('#rateCardTable_id').DataTable({
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
            order: [[1, 'desc']],
            ajax: {
                "url": "ajax/populateRateCardTable.php",
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

const RateCardsList = new RRateCardsList();

const PreviewBespokeRate = new previewBespokeRate(RateCardsList);

rfsSelect.listenForSelectChange(RateCardsList.table);

export { RateCardsList as default };