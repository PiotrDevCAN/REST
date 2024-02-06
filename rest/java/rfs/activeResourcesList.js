/**
 *
 */

let Rfs = await cacheBustImport('./modules/rfs.js');

class activeResourcesList {

    initialiseTable() {
        // DataTable
        Rfs.table = $('#leaverTable').DataTable({
            autoWidth: false,
            processing: true,
            responsive: false,
            dom: 'Blfrtip',
            // buttons: [
            //     'colvis',
            //     $.extend( true, {}, buttonCommon, {
            //         extend: 'excelHtml5',
            //         exportOptions: {
            //             orthogonal: 'sort',
            //             stripHtml: true,
            //             stripNewLines:false
            //         },
            // 		customize: function( xlsx ) {
            // 			var sheet = xlsx.xl.worksheets['sheet1.xml'];
            // 		}
            //     }),
            //     $.extend( true, {}, buttonCommon, {
            //         extend: 'csvHtml5',
            //         exportOptions: {
            //             orthogonal: 'sort',
            //             stripHtml: true,
            //             stripNewLines:false
            //         }
            //     }),
            //     $.extend( true, {}, buttonCommon, {
            //         extend: 'print',
            //         exportOptions: {
            //             orthogonal: 'sort',
            //             stripHtml: true,
            //             stripNewLines:false
            //         }
            //     })
            // ],
            ajax: {
                "url":"ajax/populateLeaversTable.php",
                "type": "GET",
                // success: function(data, textStatus, jqXHR)
                // {
                //     console.log(data); //*** returns correct json data
                // }
            },
            drawCallback: function( row, data ) {
                $("[data-toggle='toggle']").bootstrapToggle('destroy');
                $("[data-toggle='toggle']").bootstrapToggle({
                    on: 'Enabled',
                    off: 'Disabled'
                });
            },
            columns: [
                { data: "CNUM","defaultContent": "" },
                { data: "WORKER_ID","defaultContent": "" },
                { data: "EMAIL_ADDRESS","defaultContent": "" },
                { data: "NOTES_ID","defaultContent": "" },
                { data: "FIRST_NAME","defaultContent": "" },
                { data: "LAST_NAME","defaultContent": "" },
                { data: "PES_STATUS","defaultContent": "" }
            ]
        });
    }
}

const ActiveResourcesList = new activeResourcesList();
ActiveResourcesList.initialiseTable();