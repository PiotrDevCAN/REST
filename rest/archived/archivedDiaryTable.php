<?php
namespace rest\archived;

use itdq\DbTable;
use rest\traits\diaryTableTrait;

class archivedDiaryTable extends DbTable
{
    use diaryTableTrait;

    // function __construct($table, $pwd = null, $log = true)
    // {
    //     parent::__construct ( $table, $pwd, $log );
    // }
}