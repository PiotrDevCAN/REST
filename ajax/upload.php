<?php
use rest\allTables;
use rest\inflightProjectsTable;
use rest\uploadLogRecord;

set_time_limit(0);

include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';

include_once '../rest/inflightProjectsTable.php';
include_once '../rest/inflightProjectsRecord.php';
include_once '../rest/uploadLogRecord.php';
include_once '../rest/allTables.php';

include_once '../rest/allTables.php';

session_start();

ob_start();

include_once '../connect.php';

$tempFilename = $_FILES ['uploadingFile'] ['tmp_name'];
$newFilename = $_SERVER['DOCUMENT_ROOT'] .   "/" . $_SERVER['environment'] . "/uploads/" . $_FILES ['uploadingFile'] ['name'];

$uploadLog = new uploadLogRecord();
$uploadLog->logUploadStart($_SESSION['ltcuser']['mail'], $_FILES ['uploadingFile'] ['name'], allTables::$INFLIGHT_PROJECTS);

if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $poidsMax = ini_get('post_max_size');
    sprintf("File is too big,(%s bytes). Maximum allowed size here is $poidsMax.", $_SERVER['CONTENT_LENGTH']);
} elseif (! move_uploaded_file ( $tempFilename, $newFilename)) {
    echo "Move Uploaded File returned : False";
    echo "Error Code : " . $_FILES ['uploadingFile'] ['error'] . "<BR/>";
    print_r ( $_FILES );
    switch ($_FILES ['uploadingFile'] ['error']) {
        case 1:
            echo "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
            break;
        case 2:
            echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
            break;
        case 3:
            echo "The uploaded file was only partially uploaded.";
            break;
        case 4:
            echo "No file was uploaded.";
            break;
        case 5:
            echo "unknown error";
            break;
        case 6:
            echo "Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.";
            break;
        case 7:
            echo "Failed to write file to disk. Introduced in PHP 5.1.0.";
            break;
        case 8:
            echo "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.";
            break;
        default:
            echo "An unknown error";
            break;
            break;
    }
    echo "Upload failed - Please contact support team with the details.";
} else {
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $now = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

    echo "<h2>File : " . $_FILES ['uploadingFile'] ['name'] . "</h2>";
    echo "<h4>Successful uploaded to Server @" .  $now->format('H:i:s.u') . "</h4>";

}

try {
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $now = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );


    echo "<h4>Reading XLS began @" .  $now->format('H:i:s.u') . "</h4>";
    $sheetname = 'All ITS Forecast (Hrs)';
    $inputFileType = PHPExcel_IOFactory::identify($newFilename);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objReader->setLoadSheetsOnly($sheetname);

    $objPHPExcel = $objReader->load($newFilename);

    $objPHPExcel->getSecurity()->setLockWindows(true);
    $objPHPExcel->getSecurity()->setLockStructure(true);
    $objPHPExcel->getSecurity()->setWorkbookPassword("Rest1234");
    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

    $inflightProjectTable = new inflightProjectsTable(allTables::$INFLIGHT_PROJECTS);
    $inflightProjectTable->commitUpdates();
    $autoCommit = db2_autocommit($_SESSION['conn'],false);

    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $now = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );


    echo "<h4>Table load began @" .  $now->format('H:i:s.u') . "</h4>";
    $inflightProjectTable->populateFromWorksheet($objWorksheet);
    $inflightProjectTable->commitUpdates();
    db2_autocommit($_SESSION['conn'],$autoCommit);

    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $now = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

    echo "<h4>Table load completed @" .  $now->format('H:i:s.u') . "</h4>";
    $uploadLog->logUploadCompleted();
    db2_commit($_SESSION['conn']);

} catch(Exception $e) {
    die('Error loading file "'.pathinfo($newFilename,PATHINFO_BASENAME).'": '.$e->getMessage());
}

$response = ob_get_clean();

ob_clean();
echo json_encode($response);