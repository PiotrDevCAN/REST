<?php

use rest\allTables;
use rest\activeResourceRecord;
use rest\activeResourceTable;

set_time_limit(0);

$url = $_ENV['vbac_url'] . '/api/employeesLeft.php?token=' . $_ENV['vbac_api_token'];

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    // CURLOPT_HEADER => 1,
    CURLOPT_HEADER => FALSE,
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_FAILONERROR => true,
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); //check if 504 return.

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {

    $activeResourceTable  = new activeResourceTable(allTables::$ACTIVE_RESOURCE);
    $activeResourceRecord = new activeResourceRecord();

    $activeResourceTable->clear(false);

    $responseObj = json_decode($response);
    if (count($responseObj) > 0) {
        foreach ($responseObj as $personEntry) {
            $activeResourceRecord->setFromArray($personEntry);
            $db2result = $activeResourceTable->insert($activeResourceRecord);
    
            if(!$db2result){
                echo json_encode(sqlsrv_errors());
                echo json_encode(sqlsrv_errors());
            }
        }
    }
    echo count($responseObj) . ' records read from VBAC api';
}