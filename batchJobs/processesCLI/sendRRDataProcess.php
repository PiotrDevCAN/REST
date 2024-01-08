<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use itdq\BlueMail;
use rest\allTables;
use rest\resourceRequestHoursTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit','2048M');

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
    ->setTitle('Resource Request Report - Generated from DEV instance')
    ->setSubject('Resource Request Report')
    ->setDescription('Resource Request Report generated by REST')
    ->setKeywords('office 2007 openxml php rest resource request report')
    ->setCategory('Resource Request');
    // Add some data

$now = new DateTime();

try {

    $resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    $resultSet = $resourceRequestHoursTable->returnHrsPerWeekForExtractWithArchived(true);

    if ($resultSet) {
        DbTable::writeResultSetToXls($resultSet, $spreadsheet);
    }
    
    DbTable::autoFilter($spreadsheet);
    DbTable::autoSizeColumns($spreadsheet);
    DbTable::setRowColor($spreadsheet,'19DDEBF7',1);
    
    $spreadsheet->setActiveSheetIndex(0);
    $spreadsheet->getActiveSheet()->setTitle('Resource Request');
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Xlsx)
    DbTable::autoSizeColumns($spreadsheet);
    // $filePrefix = 'Resource Request - with archived since January 2022 - ';
    $fileNameSuffix = $now->format('Ymd_His');
    // $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
    $fileNamePart = 'Resource Request Report.xlsx';
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

    $subject = 'Resource Request Report : ' . $fileNameSuffix;
    $message = 'Please find attached Resource Request Report XLS';
    $message .= 'List of recent changes:<br>';
    $message .= '<ul>';
    $message .= '<li>Following fields has been attached to the report: Start Date, Description, RFS Type, Project Title and Requestor Name</li>';
    $message .= '</ul>';
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
    $subject = 'Error in: send RR data ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array('piotr.tajanowicz@kyndryl.com');
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}