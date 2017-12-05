<?php
include 'php/ldap.php';

$resp = employee_in_group('REST_Admin', 'rob.daniel@uk.ibm.com');

var_dump($resp);

$resp = employee_in_group('REST_Admin', 'fred.daniel@uk.ibm.com');

var_dump($resp);

$GLOBALS['ltcuser']['mail'] = 'rob.daniel@uk.ibm.com';
$_SESSION['adminBg'] = 'REST_Admin';
$_SESSION['userBg'] = 'REST_User';
$_SESSION['pmoBg'] = 'REST_Pmo';
$_SESSION['itdqBg'] = 'ITDQ';

    $isAdmin = employee_in_group($_SESSION['adminBg'], $GLOBALS['ltcuser']['mail']);
    $validUser = employee_in_group($_SESSION['userBg'], $GLOBALS['ltcuser']['mail']);
    $isItdq = employee_in_group($_SESSION['itdqBg'], $GLOBALS['ltcuser']['mail']);
    $isPmo = employee_in_group($_SESSION['pmoBg'], $GLOBALS['ltcuser']['mail']);


    var_dump($isAdmin);
    var_dump($validUser);
    var_dump($isItdq);
    var_dump($isPmo);