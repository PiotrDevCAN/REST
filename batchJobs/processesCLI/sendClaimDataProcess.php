<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use itdq\BlueMail;

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

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
// Set document properties
$spreadsheet->getProperties()->setCreator('REST')
    ->setLastModifiedBy('REST')
    ->setTitle('Claim Data Report')
    ->setSubject('Claim Data Report')
    ->setDescription('Claim Data Report generated by REST')
    ->setKeywords('office 2007 openxml php rest claim data report')
    ->setCategory('Claim Data');
    // Add some data

$now = new DateTime();

try {
    $url = 'https://soiwapi-new.icds.ibm.com/Lloyds/api/claim/soEkCfj8zGNDLZ8yXH2YJjpehd8ijzlS/2021-01-01?plus=CREATED_TMS,WORK_ITEM_ID,CALC_RATE_CHRG_CURR,BP_INTRANET_ID';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
        ),
        CURLOPT_ENCODING => 'gzip'
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        // echo "cURL Error #:" . $err;
        throw new \Exception('Lloyds Claim API CURL call failed');
    } else {
        $responseObj = json_decode($response, true);
        if ($responseObj !== null) {
            DbTable::writeJsonObjSetToXls($responseObj['data'], $spreadsheet);
        }
    }
    
    DbTable::autoFilter($spreadsheet);
    DbTable::autoSizeColumns($spreadsheet);
    DbTable::setRowColor($spreadsheet,'19DDEBF7',1);
    
    $spreadsheet->setActiveSheetIndex(0);
    $spreadsheet->getActiveSheet()->setTitle('Claim Data');
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Xlsx)
    DbTable::autoSizeColumns($spreadsheet);
    // $filePrefix = 'Claim Data - ';
    $fileNameSuffix = $now->format('Ymd_His');
    // $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
    $fileNamePart = 'Claim Data Report.xlsx';
    $scriptsDirectory = '/var/www/html/extracts/';
    $fileName = $scriptsDirectory.$fileNamePart;   

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    // $writer->save('php://output');
    $writer->save($fileName);

    $attachments = array();
    $handle = fopen($fileName, "r", true);
    if ($handle !== false) {
        $applicationForm = fread($handle, filesize($fileName));
        fclose($handle);
        $encodedAttachmentFile = base64_encode($applicationForm);
        $attachments[] = array(
            'filename'=>$fileNamePart,
            'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'data'=>$encodedAttachmentFile,
            'path'=>$fileName
        );
    }

    $subject = 'Claim Data Report : ' . $fileNameSuffix;
    $message = 'Please find attached Claim Data Report XLS';
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC, true, $attachments);    
    // var_dump($result);
    trigger_error('BlueMail::send_mail result: '.serialize($result), E_USER_WARNING);
    
    if (file_exists($fileName)) {
        $deleteOk = unlink($fileName);
        if ($deleteOk) {
            trigger_error("File deleted", E_USER_WARNING);
        } else {
            trigger_error("Problem deleting file", E_USER_WARNING);
        }
    } else {
        trigger_error("File does not exist", E_USER_WARNING);
    }

} catch (Exception $e) {
    $subject = 'Error in: send Claim data ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array('piotr.tajanowicz@kyndryl.com');
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}