<?php
//used to verify and process login
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

include realpath(dirname(__FILE__))."/../class/include.php";
$auth = new Auth();
if($auth->verifyResponse($_GET))
{
    header("Location: ".$_GET['state']);
	exit();
} else {
    echo "<pre>";
    var_dump($_GET);
    die('verify response returned false');
}
?>
