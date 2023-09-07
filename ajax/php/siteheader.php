<?php
use itdq\JwtSecureSession;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);

include ('includes/obHtmlCompress.php');
include ('vendor/autoload.php');
include ('splClassLoader.php');

$sessionConfig = (new \ByJG\Session\SessionConfig($_SERVER['SERVER_NAME']))
->withTimeoutHours(24)
->withSecret($_ENV['jwt_token'])
->replaceSessionHandler();

$handler = new JwtSecureSession($sessionConfig);
// session_set_save_handler($handler, true);

// session_start();

error_log(__FILE__ . "session:" . session_id());

$GLOBALS['Db2Schema'] = strtoupper($_ENV['environment']);
$GLOBALS['Db2Schema'] = str_replace('_LOCAL', '_DEV', $GLOBALS['Db2Schema']);

include "connect.php";
