<?php
namespace rest\archived;

use itdq\DbTable;
use rest\traits\rfsTableTrait;
use rest\traits\tableTrait;

class archivedRfsTable extends DbTable
{
    use tableTrait, rfsTableTrait;
    
    // function __construct($table, $pwd = null, $log = true)
    // {
    //     parent::__construct ( $table, $pwd, $log );
    // }
}