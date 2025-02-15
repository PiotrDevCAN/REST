<?php

use itdq\Process;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$email = $_SESSION['ssoEmail'];
$scriptsDirectory = '/var/www/html/batchJobs/';
$processDirectory = 'processesCLI/'; 
$processFile = 'hoursPerWeekPerResourceProcess.php';
try {
    $cmd = 'php ';
    $cmd .= '-d auto_prepend_file=' . $scriptsDirectory . 'php/siteheader.php ';
    $cmd .= '-d auto_append_file=' . $scriptsDirectory . 'php/sitefooter.php ';
    $cmd .= '-f ' . $scriptsDirectory . $processDirectory. $processFile . ' ' . $email;
    $process = new Process($cmd);
    $pid = $process->getPid();
    echo "Hours per week per resource Extract Script has succeed to be executed: <b>".$email."</b>";
    // echo $cmd;
} catch (Exception $exception) {
    echo $exception->getMessage();
    echo "Hours per week per resource Extract Script has failed to be executed: <b>".$email."</b>";
}
