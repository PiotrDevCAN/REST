<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use itdq\DbTable;
use itdq\BlueMail;

use rest\allTables;
use rest\activeResourceRecord;
use rest\activeResourceTable;
use rest\rfsRecord;

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
$emailAddressTO = array(
    'philip.bibby@kyndryl.com',
    $_ENV['automationemailid']
);
$emailAddressCC = array();
$emailAddressBCC = array();

try {

    $url = $_ENV['vbac_url'] . '/api/squadTribePlus.php?token=' . $_ENV['vbac_api_token'] . '&onlyactive=false&withProvClear=true&plus=P.CNUM,P.EMAIL_ADDRESS,P.KYN_EMAIL_ADDRESS,P.FIRST_NAME,P.LAST_NAME,P.PES_STATUS,SQUAD_NAME,TRIBE_NAME,P.WORK_STREAM,P.CIO_ALIGNMENT,P.WORKER_ID';

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        // CURLOPT_HEADER => 1,
        CURLOPT_HEADER => FALSE,
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_FAILONERROR => true,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
        ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); //check if 504 return.
    
    curl_close($curl);
    
    if ($err) {
        // echo "cURL Error #:" . $err;
        throw new \Exception('Load vBAC Active Resources CURL call failed');
    } else {
    
        $activeResourceTable  = new activeResourceTable(allTables::$ACTIVE_RESOURCE);
        $activeResourceRecord = new activeResourceRecord();
    
        $activeResourceTable->clear(false);
    
        $responseArr = json_decode($response, true);
        $insertCounter = 0;
        $failedCounter = 0;
        if (count($responseArr) > 0) {

            // start transaction
            if (sqlsrv_begin_transaction($GLOBALS['conn']) === false ) {
                die( print_r( sqlsrv_errors(), true ));
            }

            // define query
            $sql = "INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$ACTIVE_RESOURCE . " ( CNUM, EMAIL_ADDRESS, KYN_EMAIL_ADDRESS, WORKER_ID, FIRST_NAME, LAST_NAME, NOTES_ID, PES_STATUS, CIO_ALIGNMENT, STATUS, TRIBE_NAME, SQUAD_NAME, TRIBE_NAME_MAPPED )";
            $sql .= " VALUES (?, ?, ?, ?, ? ,?, ?, ?, ?, ?, ? ,?, ?)";
            
            $cnum = '';
            $emailAddress = '';
            $kynEmailAddress = '';
            $workerId = '';
            $firstName = '';
            $lastName = '';
            $notesId = '';
            $pesStatus = '';
            $cioAlignment = '';
            $status = '';
            $tribeName = '';
            $squadName = '';
            $mappedTribeName = '';

            // prepare query for later use
            $stmt = sqlsrv_prepare($GLOBALS['conn'], $sql, array(
                &$cnum,
                &$emailAddress,
                &$kynEmailAddress,
                &$workerId,
                &$firstName,
                &$lastName,
                &$notesId,
                &$pesStatus,
                &$cioAlignment,
                &$status,
                &$tribeName,
                &$squadName,
                &$mappedTribeName
            ));
            if(!$stmt){
                die( print_r( sqlsrv_errors(), true));
            }

            // iterate through read entries
            foreach ($responseArr as $key => $personEntry) {

                $cnum = trim($personEntry['CNUM']);
                if (empty($cnum)) {
                    $cnum = 'unknown cnum';
                }
                $emailAddress = trim($personEntry['EMAIL_ADDRESS']);
                $kynEmailAddress = trim($personEntry['KYN_EMAIL_ADDRESS']);
                $workerId = trim($personEntry['WORKER_ID']);
                $firstName = trim($personEntry['FIRST_NAME']);
                $lastName = trim($personEntry['LAST_NAME']);
                $notesId = trim($personEntry['NOTES_ID']);
                $pesStatus = trim($personEntry['PES_STATUS']);
                $cioAlignment = trim($personEntry['CIO_ALIGNMENT']);
                $status = trim($personEntry['INT_STATUS']);
                $tribeName = trim($personEntry['TRIBE_NAME']);
                $squadName = trim($personEntry['SQUAD_NAME']);
                $mappedTribeName = array_key_exists($personEntry['TRIBE_NAME'], rfsRecord::$tribeNameMapping) ? rfsRecord::$tribeNameMapping[$personEntry['TRIBE_NAME']] : trim($personEntry['TRIBE_NAME']);
                
                // exacute prepared statement with new data
                $rs = sqlsrv_execute($stmt);
                if(!$rs){
                    $failedCounter++;
                    sqlsrv_rollback($GLOBALS['conn']);
                } else {
                    $insertCounter++;
                    sqlsrv_commit($GLOBALS['conn']);
                }
            }
        }
        
        $subject = 'Load VBAC Active Records';
        $message = 'Employees records have been loaded';
        $message .= '<br> Amount of records read from vBAC: ' . count($responseArr);
        $message .= '<br> Amount of records imported to REST: ' . $insertCounter;
        $message .= '<br> Amount of records failed to import to REST: ' . $failedCounter;
        $message .= '<br> VBAC Data source: ' . $url;
        $result = BlueMail::send_mail($emailAddressTO, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC);    
    }

} catch (Exception $e) {
    $subject = 'Error in: Load VBAC Active Resources ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array('piotr.tajanowicz@kyndryl.com');
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
