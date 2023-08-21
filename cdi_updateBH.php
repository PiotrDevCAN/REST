<?php

use itdq\DbTable;
use rest\allTables;
use rest\bankHoliday;

// 'Y-m-d';

$startDate = '2022-05-01';
$endDate = '2022-06-30';

$sdate = new \DateTime($startDate);
$edate = new \DateTime($endDate);

$bankHolidays = bankHoliday::bankHolidaysFromStartToEnd($sdate, $edate);

var_dump($bankHolidays);

// $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$BANK_HOLIDAYS;
// $sql .= " SET BH_DATE= '2022-06-02' " ;
// $sql .= " WHERE BH_DATE= '2022-05-30' ";

// echo $sql;

// $rs = sqlsrv_query($GLOBALS['conn'], $sql);

// if(!$rs){
//     DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
//     return false;
// }