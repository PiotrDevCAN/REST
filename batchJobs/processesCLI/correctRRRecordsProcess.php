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

    $swappedStartEndDateRecords = 0;

    $predicate = $resourceHoursTable->notExistsPredicate('RR');
    
    $sql = "SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . "
    WHERE START_DATE > END_DATE";

    $rs = sqlsrv_query( $GLOBALS['conn'], $sql );
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

    if ($swappedStartEndDateRecords > 0) {
        $updateSql = "UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . "
        SET START_DATE = END_DATE,
        END_DATE = START_DATE
        WHERE START_DATE > END_DATE";

        $rs = sqlsrv_query( $GLOBALS['conn'], $updateSql );
        if (! $rs) {
            error_log("<BR>" . json_encode(sqlsrv_errors()));
            error_log("<BR>" . json_encode(sqlsrv_errors()) . "<BR>");
            exit ( "Error in: " . __METHOD__ . " running: COMMIT " );
        }

        $updateResponce = "<br>RR have been updated.";
    } else {
        $updateResponce = "<br>No update is required.";
    }

    $subject = 'Correct RR Records';
    $message = 'Correct RR Records script has completed.';
    $message .= '<br>Amount of records in section 1 (Start and End dates are swapped): ' . $swappedStartEndDateRecords;
    $message .= $swappedStartEndDateResponce;
    $message .= $updateResponce;
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC);
    
} catch (Exception $e) {
    $subject = 'Error in: Correct RR Records ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array($_ENV['automationemailid']);
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
