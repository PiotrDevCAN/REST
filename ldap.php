<?php
// connect, bind, and search for $user

$w3php = array(

    // enable debug only during testing
    'debug' => TRUE,

    // location of error documents
    'error_doc' => $_SERVER['DOCUMENT_ROOT'] . "/" .  $site['prefix'] . '/error_doc/',

    // enable or disable logging to syslog of auth attempts
    'log_auth' => FALSE,

    // ldap ssl connection url for authentication (iip authentication, bluepages)
    'ldaps_host' => 'bluepages.ibm.com',

    // attributes returned by default for authenticated users
    'ldap_attr' => array(
        'uid',
        'mail',
        'ismanager',
        'dept',
        'div',
        'employeetype',
        'ibmserialnumber',
        'manager',
        'cn',
        'workloc'
    ),

    // 'ldap_attr' => array('uid', 'mail', 'dept', 'employeetype', 'ibmserialnumber', 'workloc'),
    // base dn to use when doing iip authentication
    'ldap_basedn' => 'ou=bluepages,o=ibm.com'
);

$ds = ldap_connect('nonsense.addr.com');
var_dump($ds);

$ds = ldap_connect($w3php['ldaps_host']);
var_dump($ds);

$filter = "(&(mail=rob.daniel@uk.ibm.com)(objectclass=ibmPerson))";

$sr = ldap_search($ds, $w3php['ldap_basedn'], $filter, $w3php['ldap_attr']);

var_dump($sr);
var_dump($ds);

die('here2');

$entry = ldap_first_entry($ds, $sr);
var_dump($entry);

$user_dn = ldap_get_dn($ds, $entry);
var_dump($user_dn);

$pass='Dc17Ujhy';
$bind = ldap_bind($ds, $user_dn, $pass);
var_dump($bind);

