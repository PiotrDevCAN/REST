<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use itdq\BlueMail;
use rest\allTables;
use rest\rfsClaimMonthlyTable;

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
    ->setTitle('RFS Report')
    ->setSubject('RFS Report')
    ->setDescription('RFS Report generated by REST')
    ->setKeywords('office 2007 openxml php rest rfs report')
    ->setCategory('RFS');
    // Add some data

$now = new DateTime();

$predicate = " 1=1 ";

try {

    $rfsTable = new rfsClaimMonthlyTable(allTables::$RFS);
    $resultSet = $rfsTable->returnAsJson($predicate, false, true);

    if ($resultSet) {
        DbTable::writeResultSetToXls($resultSet, $spreadsheet);
    }
    
    DbTable::autoFilter($spreadsheet);
    DbTable::autoSizeColumns($spreadsheet);
    DbTable::setRowColor($spreadsheet,'19DDEBF7',1);
    
    $spreadsheet->setActiveSheetIndex(0);
    $spreadsheet->getActiveSheet()->setTitle('RFS');
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Xlsx)
    DbTable::autoSizeColumns($spreadsheet);
    // $filePrefix = 'RFS Report - ';
    $fileNameSuffix = $now->format('Ymd_His');
    // $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
    $fileNamePart = 'RFS Report.xlsx';
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

    $subject = 'RFS Report : ' . $fileNameSuffix;
    $message = 'Please find attached RFS Report XLS';
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC, true, $attachments);    
    
    if (file_exists($fileName)) {
        $deleteOk = unlink($fileName);
        if ($deleteOk) {
            error_log("File deleted");
        } else {
            error_log("Problem deleting file");
        }
    } else {
        error_log("File does not exist");
    }

} catch (Exception $e) {
    $subject = 'Error in: send RFS data ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array('piotr.tajanowicz@kyndryl.com');
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
}