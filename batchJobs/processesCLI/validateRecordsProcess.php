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
    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

    $swappedStartEndDateRecords = 0;
    $hoursRecords = 0;
    $totalZeroRecords = 0;
    $totalNullRecords = 0;

    $predicate = $resourceHoursTable->notExistsPredicate('RR');
    
    $sql = $resourceRequestTable->getSelect(null, 'RR');
    $sql .= " WHERE 1=1 " ;
    $sql .= !empty($predicate) ? " AND  $predicate " : null ;

    $swappedStartEndDatesSql = $sql;
    $swappedStartEndDatesSql .= " AND START_DATE > END_DATE";

    $rs = sqlsrv_query( $GLOBALS['conn'], $swappedStartEndDatesSql );
    if (! $rs) {
        error_log("<BR>" . json_encode(sqlsrv_errors()));
        error_log("<BR>" . json_encode(sqlsrv_errors()) . "<BR>");
        exit ( "Error in: " . __METHOD__ . " running: COMMIT " );
    }

    $swappedStartEndDateResponce = '';
    while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $swappedStartEndDateRecords++;
        $swappedStartEndDateResponce = trim($row['RESOURCE_REFERENCE']);
        $swappedStartEndDateResponce .= "<br>(" . $resourceReference . ") - Start and End dates are swapped";
    }

    $hoursSql = $sql;
    $hoursSql .= " AND HOURS_TYPE IS NULL";

    $rs = sqlsrv_query( $GLOBALS['conn'], $hoursSql );
    if (! $rs) {
        error_log("<BR>" . json_encode(sqlsrv_errors()));
        error_log("<BR>" . json_encode(sqlsrv_errors()) . "<BR>");
        exit ( "Error in: " . __METHOD__ . " running: COMMIT " );
    }

    $hoursResponse = '';
    while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $hoursRecords++;
        $resourceReference = trim($row['RESOURCE_REFERENCE']);
        $hoursResponse .= "<br>(" . $resourceReference . ") - Hours Type is null";
    }
    
    $totalHoursZeroSql = $sql;
    $totalHoursZeroSql .= " AND TOTAL_HOURS != '0.00'";

    $rs = sqlsrv_query( $GLOBALS['conn'], $totalHoursZeroSql );
    if (! $rs) {
        error_log("<BR>" . json_encode(sqlsrv_errors()));
        error_log("<BR>" . json_encode(sqlsrv_errors()) . "<BR>");
        exit ( "Error in: " . __METHOD__ . " running: COMMIT " );
    }

    $totalZeroResponse = '';
    while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $totalZeroRecords++;
        $resourceReference = trim($row['RESOURCE_REFERENCE']);
        $totalZeroResponse .= "<br>(" . $resourceReference . ") - Total Hours is 0.00";
    }

    $totalHoursSql = $sql;
    $totalHoursSql .= " AND TOTAL_HOURS IS NOT NULL";

    $rs = sqlsrv_query( $GLOBALS['conn'], $totalHoursSql );
    if (! $rs) {
        error_log("<BR>" . json_encode(sqlsrv_errors()));
        error_log("<BR>" . json_encode(sqlsrv_errors()) . "<BR>");
        exit ( "Error in: " . __METHOD__ . " running: COMMIT " );
    }

    $totalNullResponse = '';
    while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $totalNullRecords++;
        $resourceReference = trim($row['RESOURCE_REFERENCE']);
        $totalNullResponse .= "<br>(" . $resourceReference . ") - Total Hours is null";
    }
    
    $subject = 'Validate RR Hours';
    $message = 'Validate RR Hours script has completed.';
    $message .= '<br>Amount of records in section 1 (Start and End dates are swapped): ' . $swappedStartEndDateRecords;
    $message .= $swappedStartEndDateResponce;
    $message .= '<br>Amount of records in section 2 (Hours Type is null): ' . $hoursRecords;
    $message .= $hoursResponse;
    $message .= '<br>Amount of records in section 3 (Total Hours is zero): ' . $totalZeroRecords;
    $message .= $totalZeroResponse;
    $message .= '<br>Amount of records in section 4 (Total Hours is null): ' . $totalNullRecords;
    $message .= $totalNullResponse;
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC);
    
} catch (Exception $e) {
    $subject = 'Error in: Validate RR Hours ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array($_ENV['automationemailid']);
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
