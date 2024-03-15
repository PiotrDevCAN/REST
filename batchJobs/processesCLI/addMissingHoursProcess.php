<?php

use itdq\BlueMail;
use rest\allTables;
use rest\resourceRequestHoursTable;
use rest\resourceRequestTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit','3072M');

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

    $resourceRequestTable = new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS_TEST);

    $predicate = $resourceHoursTable->notExistsPredicate('RR');
    
    $sql = $resourceRequestTable->getSelect(null, 'RR');
    $sql .= " WHERE 1=1 " ;
    $sql .= !empty($predicate) ? " AND  $predicate " : null ;
    $sql .= " AND TOTAL_HOURS IS NOT NULL";
    $sql .= " AND HOURS_TYPE IS NOT NULL";
    $sql .= " AND TOTAL_HOURS != '0.00'";

    $rs = sqlsrv_query( $GLOBALS['conn'], $sql );
    if (! $rs) {
        error_log("<BR>" . json_encode(sqlsrv_errors()));
        error_log("<BR>" . json_encode(sqlsrv_errors()) . "<BR>");
        exit ( "Error in: " . __METHOD__ . " running: COMMIT " );
    }

    $hoursResponse = '';
    $recordsToUpdate = 0;
    while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $recordsToUpdate++;
        try {
            $resourceReference = trim($row['RESOURCE_REFERENCE']);
            $startDate = trim($row['START_DATE']);
            $endDate = trim($row['END_DATE']);
            $totalHours = trim($row['TOTAL_HOURS']);
            $hoursType = trim($row['HOURS_TYPE']);
            $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference, $startDate, $endDate, $totalHours, true, $hoursType);
            $hoursResponse .= "<br>" . $weeksCreated . " weeks saved to the Resource Hours table.";
        } catch (Exception $e) {
            $hoursResponse .= "<br>" . $e->getMessage();
        }
    }
    
    $subject = 'Add Missing RR Hours';
    $message = 'Add Missing RR Hours script has completed.';
    $message .= '<br>Amount of updated RR records: ' . $recordsToUpdate;
    $message .= $hoursResponse;
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC);
    
} catch (Exception $e) {
    $subject = 'Error in: Add Missing RR Hours ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array($_ENV['automationemailid']);
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
