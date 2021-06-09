<?php
use itdq\JwtSecureSession;

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR . "../itdq/" . PATH_SEPARATOR .  "../vbac/" );

if($_REQUEST['token']!= $_ENV['api_token']){
    // no token no response
    ob_clean();
    http_response_code(400);
    echo json_encode(array('success'=>false,'data'=>null));    
    exit();
}

include ('vendor/autoload.php');
include ('splClassLoader.php');

$sessionConfig = (new \ByJG\Session\SessionConfig($_SERVER['SERVER_NAME']))
->withSecret($_ENV['jwt_token']);

$handler = new JwtSecureSession($sessionConfig);

session_set_save_handler($handler, true);
session_start();
error_log(__FILE__ . "session:" . session_id());

$GLOBALS['Db2Schema'] = strtoupper($_ENV['environment']);
$_SESSION['ssoEmail'] = empty($_SESSION['ssoEmail']) ? 'API Invocation' : $_SESSION['ssoEmail'];
include "connect.php";
