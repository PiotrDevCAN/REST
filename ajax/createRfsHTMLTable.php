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
<tr><?=$RFSheaderCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$RFSheaderCells ;?></tr></tfoot></table>
<?php
