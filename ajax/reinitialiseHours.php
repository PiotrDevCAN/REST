<?php
use itdq\DbTable;
use rest\allTables;
use rest\resourceRequestRecord;
use rest\resourceRequestTable;
use rest\resourceRequestHoursRecord;
use rest\resourceRequestHoursTable;

session_start();

set_time_limit(0);

include_once '../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
include_once '../itdq/FormClass.php';
include_once '../itdq/DbRecord.php';
include_once '../itdq/DbTable.php';
include_once '../itdq/log.php';
include_once '../itdq/trace.php';
include_once '../itdq/AllItdqTables.php';
include_once '../itdq/Date.php';

include_once '../rest/resourceRequestTable.php';
include_once '../rest/resourceRequestRecord.php';
include_once '../rest/resourceRequestHoursTable.php';
include_once '../rest/resourceRequestHoursRecord.php';
include_once '../rest/allTables.php';

include_once '../rest/allTables.php';

ob_start();

include_once '../connect.php';

$autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

switch (true) {
    case empty($_POST['ModalSTART_DATE']):
        echo 'No Start Date provided for Reinitialise Hours function';
        $valid = false;
    break;
    case empty($_POST['ModalEND_DATE']):
        echo 'No End Date provided for Reinitialise Hours function';
        $valid = false;
        break;
    case empty($_POST['ModalHRS_PER_WEEK']):
        echo 'No Hrs/Week provided for Reinitialise Hours function';
        $valid = false;
        break;

    default:
        $valid = true;
    break;
}

if($valid){
    $resourceReference = $_POST['ModalResourceReference'];

    $resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);
    $resourceHoursTable->deleteData(" RESOURCE_REFERENCE='" . $resourceReference . "'");


    $endDate = !empty($_POST['ModalEND_DATE']) ? $_POST['ModalEND_DATE'] : $_POST['ModalSTART_DATE'];
    $hours   = !empty($_POST['ModalHRS_PER_WEEK']) ? $_POST['ModalHRS_PER_WEEK'] : 0;


    $resourceHoursSaved = false;
    try {
        $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference,$_POST['ModalSTART_DATE'],$endDate,$hours );
        $hoursResponse = $weeksCreated . " weeks saved to the Resource Hours table.";

        $resourceTable =new resourceRequestTable(allTables::$RESOURCE_REQUESTS);
        $resourceRecord = new resourceRequestRecord();
        $resourceData = $resourceTable->getWithPredicate(" RESOURCE_REFERENCE='" . $resourceReference . "'");
        $resourceRecord->setFromArray($resourceData);
        $resourceRecord->set('START_DATE', $_POST['ModalSTART_DATE']);
        $resourceRecord->set('END_DATE', $_POST['ModalEND_DATE']);
        $resourceRecord->set('HRS_PER_WEEK', $_POST['ModalHRS_PER_WEEK']);
        $rs = $resourceTable->update($resourceRecord);
        db2_commit($_SESSION['conn']);
    } catch (Exception $e) {
        db2_rollback($_SESSION['conn']);
        $hoursResponse = $e->getMessage();
    }

    $resourceHoursTable->commitUpdates();
}

db2_autocommit($_SESSION['conn'],$autoCommit);

$messages = ob_get_clean();
$response = array( 'hoursResponse'=>$hoursResponse, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);