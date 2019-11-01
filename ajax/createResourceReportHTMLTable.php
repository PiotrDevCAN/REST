<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\rfsRecord;

set_time_limit(0);
ob_start();
$RRheaderCells = resourceRequestRecord::htmlHeaderCells($_POST['START_DATE']);
$RFSheaderCells = rfsRecord::htmlHeaderCells();

ob_clean();
?>
<table id='resourceRequestsTable_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr><?=$RFSheaderCells . $RRheaderCells ;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$RFSheaderCells . $RRheaderCells ;?></tr></tfoot></table>
<?php
