<?php
use rest\rfsTable;
use rest\allTables;

$rfsTable = new rfsTable(allTables::$RFS);


$rfs = array('LCOM-REQ-000010','LCOM-REQ-000155','LCYB-REQ-000060','28/12/2017- 1048');

$now = microtime(true);
$today = new DateTime();
for($i=0;$i<count($rfs);$i++){
    $elapsedS = microtime(true);
    $rfsEndDate  =  $rfsTable->rfsMaxEndDate($rfs[$i]);
    $elapsedE = microtime(true);
    echo "<hr/>";
    var_dump($rfsEndDate);
    if($rfsEndDate){
        echo $rfsEndDate < $today ? "<br/>Past" : "<br/>Future";
    } else {
        echo "<br/>No Date";
    }


    echo "<br/>Run took " . ($elapsedE - $elapsedS);
}


