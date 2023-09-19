<?php
ob_start();
$sql1 = " SELECT * FROM REST_UT.DIARY ";

echo "<br/> $sql1";

$rs1 = sqlsrv_query($GLOBALS['conn'], $sql1);
$rowCounter = 0 ;
$preFetch = microtime(true);
while($row = sqlsrv_fetch_array($rs1)){
    $rowCounter++;
    echo ($rowCounter % 100) == 0 ?  "<br/>varchar Row:$rowCounter Elapsed: " . (microtime(true)-$preFetch) : null;
    ob_flush();
    flush();
}

echo "<hr/>";

$sql2 = " SELECT DIARY_REFERENCE, CAST(ENTRY as VARCHAR(1024)) as ENTRY, CREATOR, CREATED FROM REST_UT.DIARY_CLOB ";

echo "<br/> $sql2";

$rs1 = sqlsrv_query($GLOBALS['conn'], $sql2);
$rowCounter = 0 ;
$preFetch = microtime(true);
while($row = sqlsrv_fetch_array($rs1)){
    $rowCounter++;
    echo ($rowCounter % 100) == 0 ?  "<br/>clob Row:$rowCounter Elapsed: " . (microtime(true)-$preFetch) : null;
    ob_flush();
    flush();
}

echo "<hr/>";

$sql3 = " SELECT DIARY_REFERENCE, ENTRY, CREATOR, CREATED FROM REST_UT.DIARY_CLOB ";

echo "<br/> $sql3";

$rs1 = sqlsrv_query($GLOBALS['conn'], $sql3);
$rowCounter = 0 ;
$preFetch = microtime(true);
while($row = sqlsrv_fetch_array($rs1)){
    $rowCounter++;
    echo ($rowCounter % 100) == 0 ?  "<br/>clob Row:$rowCounter Elapsed: " . (microtime(true)-$preFetch) : null;
    ob_flush();
    flush();
}