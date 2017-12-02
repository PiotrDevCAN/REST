<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\rfsRecord;

set_time_limit(0);

include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';

include_once '../rest/rfsRecord.php';
include_once '../rest/allTables.php';
session_start();

ob_start();
include_once '../connect.php';

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
