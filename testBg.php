<?php
use rest\rfsTable;
use rest\allTables;

// include 'php/ldap.php';

// $resp = employee_in_group('REST_Admin', 'rob.daniel@uk.ibm.com');

// var_dump($resp);

// $resp = employee_in_group('REST_Admin', 'fred.daniel@uk.ibm.com');

// var_dump($resp);

// $GLOBALS['ltcuser']['mail'] = 'rob.daniel@uk.ibm.com';
// $_SESSION['adminBg'] = 'REST_Admin';
// $_SESSION['userBg'] = 'REST_User';
// $_SESSION['pmoBg'] = 'REST_Pmo';
// $_SESSION['itdqBg'] = 'ITDQ';

//     $isAdmin = employee_in_group($_SESSION['adminBg'], $GLOBALS['ltcuser']['mail']);
//     $validUser = employee_in_group($_SESSION['userBg'], $GLOBALS['ltcuser']['mail']);
//     $isItdq = employee_in_group($_SESSION['itdqBg'], $GLOBALS['ltcuser']['mail']);
//     $isPmo = employee_in_group($_SESSION['pmoBg'], $GLOBALS['ltcuser']['mail']);


//     var_dump($isAdmin);
//     var_dump($validUser);
//     var_dump($isItdq);
//     var_dump($isPmo);

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


