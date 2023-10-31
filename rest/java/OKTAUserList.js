/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/buttonCommon.js');
let OKTAUserEntry = await cacheBustImport('./OKTAUserEntry.js');

class OKTAUserList {

    table;

    constructor() {
        // this.initialiseTable();


        // CDIMembersTable
        // adminMembersTable
        // demandMembersTable
        // supplyMembersTable
        // supplyXMembersTable
        // rfsMembersTable
        // rfsAdMembersTable
        // reportsMembersTable

        var $this = this;
        var tables = $('.dataTable');
        tables.each((i, table) => {
            var tableId = $(table).attr('id');
            var groupName = $(table).data('group');
            $this.initialiseTable(tableId, groupName);
        });

    }

    initialiseTable(tableId, groupName) {
        // DataTable
        this.table = $('#'+tableId).DataTable({
            autoWidth: false,
            processing: true,
            responsive: false,
            dom: 'Blfrtip',
            ajax: {
                "url": "ajax/populateOktaGroupMembers.php",
                "type": "POST",
                "data": {
                    "group": groupName
                }
            },
            columns: [
                { data: "NAME", "defaultContent": "" },
                { data: "EMAIL_ADDRESS", "defaultContent": "" },
            ]
        });
    }
}

const OktaUserList = new OKTAUserList();
OKTAUserEntry.table = OktaUserList.table;

export { OktaUserList as default };