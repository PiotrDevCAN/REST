<?php
include 'php/ldap.php';



$resp = employee_in_group('REST_Admin', 'rob.daniel@uk.ibm.com');

var_dump($resp);