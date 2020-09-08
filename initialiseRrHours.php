<?php
use rest\resourceRequestTable;
use rest\allTables;
use rest\resourceRequestHoursTable;

set_time_limit(0);

do_auth();


$sql = " SELECT RESOURCE_REFERENCE, START_DATE, END_DATE, HRS_PER_WEEK FROM " . $GLOBALS['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUESTS;

echo $sql;

$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

$resultSet = db2_exec($GLOBALS['conn'],$sql);

if($resultSet){
    while (($row=db2_fetch_assoc($resultSet))==true){
        print_r($row);
        if(!empty($row['START_DATE']) && !empty($row['END_DATE']) && !empty($row['HRS_PER_WEEK'])){
            $resourceHoursTable->createResourceRequestHours($row['RESOURCE_REFERENCE'],$row['START_DATE'], $row['END_DATE'], $row['HRS_PER_WEEK']);
            echo "<br/>Processed : " . $row['RESOURCE_REFERENCE'];
        } else {
            echo "<br/>Can't Process : " . $row['RESOURCE_REFERENCE'];
        }
    }
} else {
    db2_stmt_error();
    db2_stmt_errormsg();
}




// $endDate = !empty($_POST['ModalEND_DATE']) ? $_POST['ModalEND_DATE'] : $_POST['ModalSTART_DATE'];
// $hours   = !empty($_POST['ModalHRS_PER_WEEK']) ? $_POST['ModalHRS_PER_WEEK'] : 0;


// $resourceHoursSaved = false;
// try {
//     $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference,$_POST['ModalSTART_DATE'],$endDate,$hours );
