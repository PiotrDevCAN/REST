<?php
// w3php site config file
// Edit this file to control site wide defaults

// # these settings control aspects of the site
// # content (look and feel)
$_SESSION['SITE_NAME'] = 'rest';
$_SESSION['country'] = 'E4'; // Used in Connect.php

if(!isset($_SERVER['environment'])){
    echo "<pre>";
    var_dump($_SERVER);
    die('environment not set');
}

$site = array(

    // url to send feedback too. See meta tags below as well
    'feedback_uri' => 'mailto:daniero@uk.ibm.com',

    // base location of css, js, and images
    'assets' => '/ui',

    // prefix to use if your site is not located at /
    // do not include the trailing slash
    'prefix' => '/rest', // CHANGE THIS WHEN STARTING NEW APP

    'dateFormat' => 'yyyy-mm-dd', // Determines the date format for Date Picker
    'dateStart' => '2017-01-01',

    'Db2Schema' => strtoupper($_SERVER['environment']),  // DB2 Schema name for the app
    'prefix' => $_SERVER['environment'], // DB2 Schema name for the app
    'dirPrefix' => $_SERVER['environment'],
    'csvPrefix' => $_SERVER['environment'],

    'cdiBg'     => 'ventus_cdi',
    'adminBg'   => 'ventus_rest_admin',
    'demandBg'  => 'ventus_rest_demand',
    'supplyBg'  => 'ventus_rest_supply',
    'rfsBg'     => 'ventus_rest_rfs',
    'reportsBg' => 'ventus_rest_ro',

    'nullBg' => null,

    'email' => false,
    'emailId' => 'DoNotReply_rest@uk.ibm.com',

    'SITE_NAME' => $_SERVER['environment'],
    'iconDirectory' => 'ICON'



) // Sets the start date for the Date Pickr
;

// # These settings are used for the meta tags on each page. These are
// # all mandatory for Intranet sites. A full description of meta tags
// # and allowed content is at:
// # http://w3.ibm.com/standards/intranet/design/v8/checklist.html#codehtml

$meta = array(

    // description of web site
    'description' => 'Aurora Resourcing Tool',

    // keywords for w3 search
    'keywords' => 'resourcing tool',

    // web site owner, can be different from feedback owner
    'owner' => 'MDBLAKE@uk.ibm.com',

    // The Feedback Meta Tag will be used to automatically route
    // feedback email received through the central Intranet Feedback
    // Form (w3.ibm.com/feedback) to the correct handler, without human
    // intervention.
    'feedback' => 'rob.daniel@uk.ibm.com',

    // security class for this web site
    'security' => 'IBM internal use only',

    // robots control for indexing
    'robots' => 'index,follow',

    // ibm.country associates this site with a country
    // or list of countries, ibm.com Search uses the this tag
    'ibm.country' => 'US',

    // dc.date shows the last time a page was updated
    // and is set automatically by w3php
    'dc.date' => FALSE,

    // the ISO language code of this web site
    'dc.language' => 'en-US',

    // the effective copyright dates of this web sites content
    'dc.rights' => 'Copyright (c) 2004-2015 by IBM Corporation'
);

// # these settings control various aspects of the
// # the way w3php runs
$w3php = array(

    // enable debug only during testing
    'debug' => TRUE,

    // location of error documents
    'error_doc' => $_SERVER['DOCUMENT_ROOT'] . "/" .  $site['prefix'] . '/error_doc/',

    // enable or disable logging to syslog of auth attempts
    'log_auth' => FALSE,

    // ldap ssl connection url for authentication (iip authentication, bluepages)
    'ldaps_host' => 'bluepages.ibm.com:636',

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

foreach ($site as $key => $value) {
    $GLOBALS['site'][$key] = trim($value);
    $_SESSION[$key] = trim($value);
}