<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use itdq\DbTable;
use itdq\BlueMail;
use itdq\Loader;
use rest\resourceRequestRecord;
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestDiaryTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit','6144M');

$_ENV['email'] = 'on';

// require_once __DIR__ . '/../../src/Bootstrap.php';
// $helper = new Sample();
// if ($helper->isCli()) {
//     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
//     return;
// }

$noreplemailid = $_ENV['noreplykyndrylemailid'];
$emailAddress = array(
    'philip.bibby@kyndryl.com',
    $_ENV['automationemailid']
);
$emailAddressCC = array();
$emailAddressBCC = array();

try {

    $loader = new Loader();
    $predicate = " END_DATE < CAST( GETDATE() AS Date ) and STATUS != '" . resourceRequestRecord::STATUS_COMPLETED . "' ";
    $date = new DateTime();
    
    $allOpenTicketsPassedEndDate = $loader->load('RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS,$predicate);
    
    if($allOpenTicketsPassedEndDate){
        foreach ($allOpenTicketsPassedEndDate as $resourceReference) {
            resourceRequestDiaryTable::insertEntry("Auto-Closed " . $date->format('d-M-Y'), $resourceReference);
            resourceRequestTable::setRequestStatus($resourceReference,resourceRequestRecord::STATUS_COMPLETED);
            
        }
    }
    
    $subject = 'REST Auto Close';
    $message = 'REST Auto Close script has completed.';
    $message .= '<br>Amount of closed records: ' . count($allOpenTicketsPassedEndDate);
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC);    
    // trigger_error('BlueMail::send_mail result: '.serialize($result), E_USER_WARNING);
    
} catch (Exception $e) {
    $subject = 'Error in: Auto Close ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array('piotr.tajanowicz@kyndryl.com');
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
