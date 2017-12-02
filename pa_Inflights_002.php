<?php
use itdq\Trace;
use rest\inflightProjectsTable;
use rest\allTables;
use rest\inflightProjectsRecord;

set_time_limit(0);

include_once 'connect.php';

do_auth($_SESSION['userBg']);

Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<div class='container-fluid'>
<?php


//$inflightProjectsTable = new inflightProjectsTable(allTables::$INFLIGHT_PROJECTS);
//$inflightProjectsTable->buildHTMLTable();

?>
<div class='table-responsive'>
<table id='inflightProjectsTable_id' class="table table-striped table-bordered" cellspacing="0" width="100%">
<thead>
<?=inflightProjectsRecord::htmlHeaderRow();?>
</thead>
<tbody>
<?=inflightProjectsRecord::htmlHeaderRow();?>
<?=inflightProjectsRecord::htmlHeaderRow();?>
<?=inflightProjectsRecord::htmlHeaderRow();?>
</tbody>
<tfoot>
<?=inflightProjectsRecord::htmlHeaderRow();?>
</tfoot>

</table>
</div>


</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<script>
$(document).ready( function () {
    $('#inflightProjectsTable_id').DataTable({
    });
} );

</script>
