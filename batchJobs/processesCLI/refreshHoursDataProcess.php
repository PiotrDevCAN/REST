<?php

use itdq\BlueMail;
use itdq\Loader;
use rest\allTables;
use rest\resourceRequestHoursTable;

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
    // 'philip.bibby@kyndryl.com',
    'piotr.tajanowicz@kyndryl.com',
    $_ENV['automationemailid']
);
$emailAddressCC = array();
$emailAddressBCC = array();

try {

    $loader = new Loader();
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS_TEST);

    // clear the hours table
    $resourceHoursTable->clear();

    $predicate = " NOT EXISTS (
        SELECT RESOURCE_REFERENCE
        FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUEST_HOURS_TEST . " AS RRH
        WHERE RR.RESOURCE_REFERENCE = RRH.RESOURCE_REFERENCE
    )";
    
    $sql  = " SELECT 
        RR.RESOURCE_REFERENCE,
        RR.START_DATE,
        RR.END_DATE,
        RR.TOTAL_HOURS,
        RR.HOURS_TYPE ";
    $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
    $sql .= " WHERE 1=1 " ;
    $sql .= !empty($predicate) ? " AND  $predicate " : null ;
    $sql .= " AND TOTAL_HOURS IS NOT NULL";
    $sql .= " AND HOURS_TYPE IS NOT NULL";

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
            $hoursResponse = $e->getMessage();
        }
    }
    
    $subject = 'Refresh RR Hours';
    $message = 'Refresh RR Hours script has completed.';
    $message .= '<br>Amount of updated RR records: ' . $recordsToUpdate;
    $message .= '<br>' . $hoursResponse;
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC);
    
} catch (Exception $e) {
    $subject = 'Error in: Refresh RR Hours ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array('piotr.tajanowicz@kyndryl.com');
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
