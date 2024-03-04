<?php
namespace rest;

use itdq\DbTable;
use rest\traits\rfsPipelineTrait;
use rest\traits\tableTrait;

class rfsPipelineTable extends DbTable
{
    use tableTrait, rfsPipelineTrait;

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }
}