<?php
namespace rest;

use itdq\DbTable;
use rest\traits\claimReportTrait;
use rest\traits\tableTrait;

class rfsClaimMonthlyTable extends DbTable
{
    use tableTrait, claimReportTrait;

    function __construct($table, $pwd = null, $log = true)
    {
        parent::__construct ( $table, $pwd, $log );
    }
}