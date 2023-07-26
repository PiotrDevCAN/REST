<?php
namespace rest;

use itdq\DbTable;
use rest\traits\resourceRequestDiaryTableTrait;

class resourceRequestDiaryTable extends DbTable
{
    use resourceRequestDiaryTableTrait;

    // function __construct($table, $pwd = null, $log = true)
    // {
    //     parent::__construct ( $table, $pwd, $log );
    // }
}