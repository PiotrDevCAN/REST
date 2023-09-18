<?php
use itdq\DbTable;
use rest\allTables;

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

// $sql = " SELECT * From REST.INFLIGHT_PROJECTS ";
// $rs = sqlsrv_query($GLOBALS['conn'],$sql);
// var_dump($rs);

// if($rs){
//    while($row = sqlsrv_fetch_array($rs)){
//    echo "<br/>" . var_dump($row);
//     }
// }
echo "<hr/>";

$rs2 = db2_columns($GLOBALS['conn'], null, $GLOBALS['Db2Schema'], 'INFLIGHT_PROJECTS', '%');
var_dump($rs2);

if($rs2){
    while($row = sqlsrv_fetch_array($rs2)){
    echo "<br/>" . var_dump($row);
    }
}

echo "<hr/>";

$table = new DbTable(allTables::$INFLIGHT_PROJECTS);

$cols = $table->getDBColumns();

var_dump($cols);


echo "<hr/>";


$rs4 = db2_server_info($GLOBALS['conn']);
var_dump($rs4);


echo "<hr/>";


// $rs3 = db2_tables($GLOBALS['conn'], null, $GLOBALS['Db2Schema']);

// var_dump($rs3);

// if($rs3){
//    while($row = sqlsrv_fetch_array($rs3)){
//         echo "<br/>" . var_dump($row);
//     }

// }


?>