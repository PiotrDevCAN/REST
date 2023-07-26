<?php
namespace rest\archived;

use itdq\DbTable;
use rest\traits\resourceRequestDiaryTableTrait;

class archivedResourceRequestDiaryTable extends DbTable
{
    use resourceRequestDiaryTableTrait;

    // function __construct($table, $pwd = null, $log = true)
    // {
    //     parent::__construct ( $table, $pwd, $log );
    // }
}