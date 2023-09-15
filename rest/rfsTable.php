<?php
namespace rest;

use itdq\DbTable;
use rest\traits\rfsTableTrait;
use rest\traits\tableTrait;

class rfsTable extends DbTable
{
    use tableTrait, rfsTableTrait;

    const NOT_ARCHIVED = 'RFS.ARCHIVE is null';
    const ARCHIVED = 'RFS.ARCHIVE is not null';
    const ARCHIVED_IN_LAST_12_MTHS = '(RFS.ARCHIVE >= DATEADD(month, -12, CURRENT_TIMESTAMP) AND RFS.ARCHIVE <= CURRENT_TIMESTAMP)';

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }

    static function buildHTMLNoMatchBUsTable($tableId = 'rfs'){
        $nextMonthObj = new \DateTime();
        $thisMonthObj = new \DateTime();
        $thisYear = $thisMonthObj->format('Y');
        $thisMonth = $thisMonthObj->format('m');
        $thisMonthObj->setDate($thisYear, $thisMonth, 01);
        $thisMonthsClaimCutoff = self::getThisMonthsClaimCutoff($thisMonthObj->format('d-m-Y'));

        $nextMonthObj >= $thisMonthsClaimCutoff ? $nextMonthObj = self::addTime($nextMonthObj, 0, 1, 0) : null;
        $monthLabels = array();

        for ($i = 0; $i < 6; $i++) {
            $monthLabels[] = $nextMonthObj->format('M_y');
            $nextMonthObj = self::addTime($nextMonthObj, 0, 1, 0);
        }
        ?>
        <table id='<?=$tableId;?>Table_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
        <thead>
            <tr>
                <td>RFS ID</td>
                <td>Resource Ref</td>
                <td>Resource Name</td>
                <td>RFS Business Unit</td>
                <td>Individual Business Unit</td>
                <td>Diary</td>
                <?php 
                foreach ($monthLabels as $label) {
                    ?><th><?=$label?></th><?php 
                }
                ?>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td>RFS ID</td>
                <td>Resource Ref</td>
                <td>Resource Name</td>
                <td>RFS Business Unit</td>
                <td>Individual Business Unit</td>
                <td>Diary</td>
                <?php 
                foreach ($monthLabels as $label) {
                    ?><th><?=$label?></th><?php 
                }
                ?>
            </tr>
        </tfoot>
        </table>
        <?php
    }
}