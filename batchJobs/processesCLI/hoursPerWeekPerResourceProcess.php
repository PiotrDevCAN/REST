<?php

use itdq\BlueMail;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestHoursTable;

ini_set('memory_limit', '4096M');

if (isset($argv[1])) {
    
    $toEmailParam = trim($argv[1]);
    $filePrefix = 'HrsPerWeekPerResource_';

    // require_once __DIR__ . '/../../src/Bootstrap.php';
    // $helper = new Sample();
    // if ($helper->isCli()) {
    //     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    //     return;
    // }

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    // Set document properties
    $spreadsheet->getProperties()->setCreator('REST')
        ->setLastModifiedBy('REST')
        ->setTitle('REST - Hours Per Week Per Resource')
        ->setSubject('Full Person Table')
        ->setDescription('Hours Per Week Per Resource - REST')
        ->setKeywords('office 2007 openxml php vbac tracker')
        ->setCategory('Resource Extract');
        // Add some data

    $now = new DateTime();
    
    $sheet = 1;

    set_time_limit(0);

    $resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    $rsOnly = true;
    
    $rs = $resourceRequestHoursTable->returnHrsPerWeek(null, $rsOnly);

    if($rs){
        $recordsFound = DbTable::writeResultSetToXls($rs, $spreadsheet);
        if($recordsFound){
            DbTable::autoFilter($spreadsheet);
            DbTable::autoSizeColumns($spreadsheet);
            DbTable::setRowColor($spreadsheet,'105abd19',1);
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setTitle('Person Table');
            DbTable::autoSizeColumns($spreadsheet);
            $fileNameSuffix = $now->format('Ymd_His');
            $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
            $scriptsDirectory = '/var/www/html/extracts/';
            $fileName = $scriptsDirectory.$fileNamePart;

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            // $writer->save('php://output');
            $writer->save($fileName);

            // $excelOutput = ob_get_clean();

            $toEmail = array($toEmailParam);
            $subject = 'The Hours per week per resource extract';
            
            $extractRequestEmail = 'Hello &&requestor&&,
            <br/>
            <br/>Please find the attached Hours per week per resource extract.
            <br/>File name: &&fileName&&
            <hr>
            <br/>Many thanks for your cooperation
            <br>REST Team';
            
            $extractRequestEmailPattern = array('/&&requestor&&/','/&&fileName&&/');

            $replacements = array($toEmailParam, $fileNamePart);
            $emailBody = preg_replace($extractRequestEmailPattern, $replacements, $extractRequestEmail);

            $pesTaskid = $_ENV['noreplyemailid'];

            if (file_exists($fileName)) {
                // throw new \Exception("The file $fileName exists");
            } else {
                // throw new \Exception("The file $fileName does not exist");
            }

            $pesAttachments = array();
            $handle = fopen($fileName, "r", true);
            if ($handle !== false) {
                $applicationForm = fread($handle, filesize($fileName));
                fclose($handle);
                $encodedAttachmentFile = base64_encode($applicationForm);
                $pesAttachments[] = array(
                    'filename'=>$fileNamePart,
                    'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'data'=>$encodedAttachmentFile,
                    'path'=>$fileName
                );
            }

            $sendResponse = BlueMail::send_mail($toEmail, $subject, $emailBody, $pesTaskid, array(), array(), false, $pesAttachments);

            var_dump($sendResponse);

            // if ($handle !== false) {
            //     unlink($fileName);
            // }

        }
    }
} else {
    throw new \Exception('Recipient email address was missing.');
}