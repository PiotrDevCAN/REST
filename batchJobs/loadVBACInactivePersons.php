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

    $inactivePersonTable->clear(false);

    $responseObj = json_decode($response);
    foreach ($responseObj as $personEntry) {
        $inactivePersonRecord->setFromArray($personEntry);
        $db2result = $inactivePersonTable->insert($inactivePersonRecord);

        if(!$db2result){
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
        }
    }    
}