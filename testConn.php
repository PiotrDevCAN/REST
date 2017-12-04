<?php
use itdq\DbTable;
use rest\allTables;

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

// $sql = " SELECT * From REST.INFLIGHT_PROJECTS ";
// $rs = db2_exec($_SESSION['conn'],$sql);
// var_dump($rs);

// if($rs){
//     while(($row = db2_fetch_assoc($rs))==true){
//     echo "<br/>" . var_dump($row);
//     }
// }
echo "<hr/>";

$rs2 = db2_columns($_SESSION['conn'], null, $_SESSION['Db2Schema'], 'INFLIGHT_PROJECTS', '%');
var_dump($rs2);

if($rs2){
    while(($row = db2_fetch_assoc($rs2))==true){
    echo "<br/>" . var_dump($row);
    }
}

echo "<hr/>";

$table = new DbTable(allTables::$INFLIGHT_PROJECTS);

$cols = $table->getDBColumns();

var_dump($cols);


echo "<hr/>";

// $rs3 = db2_tables($_SESSION['conn'], null, $_SESSION['Db2Schema']);

// var_dump($rs3);

// if($rs3){
//     while(($row = db2_fetch_assoc($rs3))==true){
//         echo "<br/>" . var_dump($row);
//     }

// }


?>