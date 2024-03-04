<?php
namespace rest;

use DateInterval;
use Exception;
use itdq\DateClass;
use itdq\DbTable;
use rest\traits\resourceRequestTableTrait;
use rest\traits\tableTrait;

class resourceRequestTable extends DbTable
{
    use tableTrait, resourceRequestTableTrait;

    protected $rfsMaxEndDate;

    static function archivedInLast12MthsPredicate($tableAbbrv = null) {
        $predicate = "(";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE >= DATEADD(month, -12, CURRENT_TIMESTAMP)";
        $predicate.= " AND ";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE <= CURRENT_TIMESTAMP"; 
        $predicate.= ")";
        return $predicate;
    }

    static function archivedSince2022Predicate($tableAbbrv = null) {
        $predicate = "(";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE >= '2022-01-01'";
        $predicate.= " AND ";
        $predicate.= !empty($tableAbbrv) ? $tableAbbrv ."." : null ;
        $predicate.= "RFS_END_DATE <= CURRENT_TIMESTAMP"; 
        $predicate.= ")";
        return $predicate;
    }
}