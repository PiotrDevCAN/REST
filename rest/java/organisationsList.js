/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let OrganisationEntry = await cacheBustImport('./organisationEntry.js');

class organisationsList {

    table;

    constructor() {
        this.initialiseTable();
        this.listenForToggleStatus();
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
            drawCallback: function (row, data) {
                $("[data-toggle='toggle']").bootstrapToggle('destroy');
                $("[data-toggle='toggle']").bootstrapToggle({
                    on: 'Enabled',
                    off: 'Disabled'
                });
            },
            columns: [
                { data: "ORGANISATION", "defaultContent": "" },
                { data: "SERVICE", "defaultContent": "" },
                {
                    data: "STATUS",
                    render: { _: 'display', sort: 'sort' },
                }
            ]
        });
    }

    listenForToggleStatus() {
        var $this = this;
        $(document).on('change', 'input.toggle', function (e) {
            var status = $(this).data('status');
            var organisation = $(this).data('organisation');
            var service = $(this).data('service');
            $.ajax({
                url: "ajax/updateOrganisationStatus.php",
                type: 'POST',
                data: {
                    currentStatus: status,
                    ORGANISATION: organisation,
                    SERVICE: service
                },
                success: function (result) {
                    try {
                        var resultObj = JSON.parse(result);
                        var success = resultObj.success;
                        var messages = resultObj.messages;
                        if (success) {
                            messages = 'Status Update successful';
                        }
                        helper.displaySaveResultModal(messages);
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                    } catch (e) {
                        helper.unlockButton();
                        helper.displayTellDevMessageModal(e);
                    }
                }
            });
        });
    }
}

const OrganisationsList = new organisationsList();
OrganisationEntry.table = OrganisationsList.table;

export { OrganisationsList as default };