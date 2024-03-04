<?php
namespace rest;

use itdq\DbTable;
use rest\traits\rfsNoneActiveTrait;
use rest\traits\tableTrait;

class rfsNoneActiveTable extends DbTable
{
    use tableTrait, rfsNoneActiveTrait;

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }
}