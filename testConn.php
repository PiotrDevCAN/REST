<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

echo "<h2>Try to connect</h2>";
include connect.php;

var_dump($_SESSION);
var_dump($expression);

$sql = " SELECT COUNT(*) From REST.INFLIGHT_PROJECTS ";
$rs = db2_exec($_SESSION['conn'],$sql);
var_dump($rs);


$rs = db2_columns($_SESSION['conn'], null, 'REST', 'INFLIGHT_PROJECTS', '%');

var_dump($rs);




?>