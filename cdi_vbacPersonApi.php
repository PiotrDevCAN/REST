<?php
use rest\allTables;
use rest\inactivePersonRecord;
use rest\inactivePersonTable;

set_time_limit(0);

$url = $_ENV['vbac_url'] . '/api/employeesLeft.php?token=' . $_ENV['vbac_api_token'];

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {

    $inactivePersonTable  = new inactivePersonTable(allTables::$INACTIVE_PERSON);
    $inactivePersonRecord = new inactivePersonRecord();

    $clear = isset($_GET['clear']) ? $_GET['clear'] : false;
    if ($clear) {
        $inactivePersonTable->clear();
    }

    $responseObj = json_decode($response);
    $loadCounter = 0;

    $load = isset($_GET['load']) ? $_GET['load'] : false;
    if ($load) {

        $success = true;

        $autoCommit = db2_autocommit($GLOBALS['conn']);
        db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);   
        
        if (count($responseObj) > 0) {
            foreach ($responseObj as $personEntry) {
                $inactivePersonRecord->setFromArray($personEntry);
                $db2result = $inactivePersonTable->insert($inactivePersonRecord);
        
                if(!$db2result){
                    $success = false;

                    echo db2_stmt_error();
                    echo db2_stmt_errormsg();
                } else {
                    $loadCounter++;
                }
            }
        }

        if($success){
            db2_commit($GLOBALS['conn']);
        } else {
            db2_rollback($GLOBALS['conn']);
        }

        db2_autocommit($GLOBALS['conn'],$autoCommit);
    }

    echo '<BR/><B>' . count($responseObj) . ' records read from VBAC api</B>';
    echo '<BR/><B>' . $loadCounter . ' records loaded to REST db</B>';
}