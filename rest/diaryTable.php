<?php
namespace rest;

use itdq\DbTable;
use rest\traits\diaryTableTrait;

class diaryTable extends DbTable
{
    use diaryTableTrait;

    // function __construct($table, $pwd = null, $log = true)
    // {
    //     parent::__construct ( $table, $pwd, $log );
    // }
}