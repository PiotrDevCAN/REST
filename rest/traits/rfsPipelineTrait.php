<?php
namespace rest\traits;

use rest\rfsPipelineRecord;

trait rfsPipelineTrait
{
    use tableTrait;

    static function buildHTMLTable($tableId = 'rfs'){
        $headerCells = rfsPipelineRecord::htmlHeaderCellsStatic();
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead><tr><?=$headerCells ;?></tr></thead>
        <tbody></tbody>
        <tfoot><tr><?=$headerCells ;?></tr></tfoot>
        </table>
        <?php
    }
}