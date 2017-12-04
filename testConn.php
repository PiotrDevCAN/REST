<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

var_dump($_SESSION);

$sql = " SELECT COUNT(*) From REST.INFLIGHT_PROJECTS ";
$rs = db2_exec($_SESSION['conn'],$sql);
var_dump($rs);

if($rs){
    $row = db2_fetch_assoc($rs);
    var_dump($row);
}

$rs = db2_columns($_SESSION['conn'], null, 'REST', 'INFLIGHT_PROJECTS', '%');

var_dump($rs);

if($rs){
    while(($row = db2_fetch_assoc($rs))==true);
    var_dump($row);
}


?>