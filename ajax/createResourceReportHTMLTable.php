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

include_once '../rest/resourceRequestTable.php';
include_once '../rest/resourceRequestRecord.php';
include_once '../rest/rfsRecord.php';
include_once '../rest/allTables.php';
session_start();

ob_start();
include_once '../connect.php';

$RRheaderCells = resourceRequestRecord::htmlHeaderCells($_POST['START_DATE'],$_POST['END_DATE']);
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
