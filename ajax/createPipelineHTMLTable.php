<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\rfsRecord;

set_time_limit(0);
ob_start();

$RFSheaderCells = rfsRecord::htmlHeaderCells();

ob_clean();
?>
<table id='rfsTable_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr><th>RFS ID</th><th>Title</th><th>Resource Req.</th><th>From</th><th>To</th><th>CIO</th><th>Link to PGMP</th></tr>
</thead>
<tbody>
</tbody>
<tfoot><tr><th>RFS ID</th><th>Title</th><th>Resource Req.</th><th>From</th><th>To</th><th>CIO</th><th>Link to PGMP</th></tr></tfoot></table>
<?php
