<?php
namespace rest\archived;

use itdq\DbTable;
use rest\traits\resourceRequestHoursTableTrait;
use rest\traits\tableTrait;

class archivedResourceRequestHoursTable extends DbTable
{
    use tableTrait, resourceRequestHoursTableTrait;

    // function __construct($table, $pwd = null, $log = true)
    // {
    //     parent::__construct ( $table, $pwd, $log );
    // }
}