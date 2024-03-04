<?php
namespace rest;

use itdq\DbTable;
use rest\traits\rfsNoMatchBUsTrait;
use rest\traits\tableTrait;

class rfsNoMatchBUsTable extends DbTable
{
    use tableTrait, rfsNoMatchBUsTrait;

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }
}