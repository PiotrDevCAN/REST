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
}