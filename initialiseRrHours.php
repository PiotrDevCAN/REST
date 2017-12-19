<?php
set_time_limit(0);

do_auth();

print_r($_SESSION);


$sql = " SELECT RESOURCE_REFERENCE, START_DATE, END_DATE, HOURS FROM " . $_SESSION['Db2Schema'] . "." . \rest\allTables::$RESOURCE_REQUESTS;

$resultSet = db2_exec($_SESSION['conn'],$sql);

if(!$resultSet){
    while (($row=db2_fetch_assoc($resultSet))==true){
        print_r($row);
        if(!empty($row['START_DATE']) && !empty($row['END_DATE']) && !empty($row['HOURS'])){
            echo "Process : " . $row['RESOURCE_REFERENCE'];
        } else {
            echo "Can't Process : " . $row['RESOURCE_REFERENCE'];
        }
    }
}




// $endDate = !empty($_POST['ModalEND_DATE']) ? $_POST['ModalEND_DATE'] : $_POST['ModalSTART_DATE'];
// $hours   = !empty($_POST['ModalHRS_PER_WEEK']) ? $_POST['ModalHRS_PER_WEEK'] : 0;


// $resourceHoursSaved = false;
// try {
//     $weeksCreated = $resourceHoursTable->createResourceRequestHours($resourceReference,$_POST['ModalSTART_DATE'],$endDate,$hours );
