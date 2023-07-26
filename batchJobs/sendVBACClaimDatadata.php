<?php

use itdq\Process;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $GLOBALS['Db2Schema'] = 'REST_DEV';
// $GLOBALS['Db2Schema'] = 'REST_UT';
$GLOBALS['Db2Schema'] = 'REST';

$email = $_SESSION['ssoEmail'];
$scriptsDirectory = '/var/www/html/batchJobs/';
$processDirectory = 'processesCLI/'; 
$processFile = 'sendVBACClaimDataProcess.php';
try {
    $cmd = 'php ';
    $cmd .= '-d auto_prepend_file=' . $scriptsDirectory . 'php/siteheader.php ';
    $cmd .= '-d auto_append_file=' . $scriptsDirectory . 'php/sitefooter.php ';
    $cmd .= '-f ' . $scriptsDirectory . $processDirectory. $processFile . ' ' . $email;
    $process = new Process($cmd);
    $pid = $process->getPid();
    echo "Vbac Claim Data Extract Script has succeed to be executed: <b>".$email."</b>".PHP_EOL;
    echo $cmd;
} catch (Exception $exception) {
    echo $exception->getMessage();
    echo "Vbac Claim Data Extract Script has failed to be executed: <b>".$email."</b>";
}
