<?php
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestHoursTable;

set_time_limit(0);
ini_set('memory_limit', '3072M');

// require_once __DIR__ . '/../../src/Bootstrap.php';
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}
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

try {

    $part1 = isset($_GET['part_1']) ? $_GET['part_1'] : false;
    $part2 = isset($_GET['part_2']) ? $_GET['part_2'] : false;

    if ($part1) {
        $start_time = microtime(true);

        $resourceRequestHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
        $rsOnly = true;
        $rs = $resourceRequestHoursTable->returnHrsPerWeek(null, $rsOnly);
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
    
        echo " Execution time: " . $execution_time . " seconds";

        if ($part2) {
            $start_time = microtime(true);
            if($rs){
                $recordsFound = DbTable::writeResultSetToXls($rs, $spreadsheet);
                if($recordsFound){
                    DbTable::autoFilter($spreadsheet);
                    DbTable::autoSizeColumns($spreadsheet);
                    DbTable::setRowColor($spreadsheet,'105abd19',1);
                    $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->setTitle('Person Table');
                    DbTable::autoSizeColumns($spreadsheet);
                    $now = new DateTime();
                    $fileNameSuffix = $now->format('Ymd_His');

                    // ob_clean();
                    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    // header('Content-Disposition: attachment;filename="HrsPerWeekPerResource_' . $fileNameSuffix . '.xlsx"');
                    // header('Cache-Control: max-age=0');
                    // // If you're serving to IE 9, then the following may be needed
                    // header('Cache-Control: max-age=1');
                    // // If you're serving to IE over SSL, then the following may be needed
                    // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                    // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                    // header('Pragma: public'); // HTTP/1.0
                    // $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    // $writer->save('php://output');
                    // exit;
                }
            }
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);

            echo " Execution time: " . $execution_time . " seconds";
        }
    }
} catch (Exception $e) {

//    ob_clean();

    echo "<br/><br/><br/><br/><br/>";

    echo $e->getMessage();
    echo $e->getLine();
    echo $e->getFile();
    echo "<h1>Problem found. See above.</h1>";
}