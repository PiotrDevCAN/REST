<?php
use rest\rfsTable;
use rest\allTables;

// include 'php/ldap.php';

// $resp = employee_in_group('REST_Admin', 'rob.daniel@uk.ibm.com');

// var_dump($resp);

// $resp = employee_in_group('REST_Admin', 'fred.daniel@uk.ibm.com');

// var_dump($resp);

// $_SESSION['ssoEmail'] = 'rob.daniel@uk.ibm.com';
// $_SESSION['adminBg'] = 'REST_Admin';
// $_SESSION['userBg'] = 'REST_User';
// $_SESSION['pmoBg'] = 'REST_Pmo';
// $_SESSION['itdqBg'] = 'ITDQ';

//     $isAdmin = employee_in_group($_SESSION['adminBg'], $_SESSION['ssoEmail']);
//     $validUser = employee_in_group($_SESSION['userBg'], $_SESSION['ssoEmail']);
//     $isItdq = employee_in_group($_SESSION['itdqBg'], $_SESSION['ssoEmail']);
//     $isPmo = employee_in_group($_SESSION['pmoBg'], $_SESSION['ssoEmail']);


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


