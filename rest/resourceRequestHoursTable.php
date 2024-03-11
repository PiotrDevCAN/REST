<?php
namespace rest;

use itdq\DbTable;
use rest\traits\resourceRequestHoursTableTrait;
use rest\traits\tableTrait;

class resourceRequestHoursTable extends DbTable
{
    use tableTrait, resourceRequestHoursTableTrait;

    // function __construct($table, $pwd = null, $log = true)
    // {
    //     parent::__construct ( $table, $pwd, $log );
    // }

    function notExistsPredicate($tableAbbrv = 'RR') {

        $predicate = " NOT EXISTS (
            SELECT RESOURCE_REFERENCE
            FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName . " AS RRH
            WHERE " . $tableAbbrv . ".RESOURCE_REFERENCE = RRH.RESOURCE_REFERENCE
        )";
        return $predicate;
    }
}