<?php
use rest\allTables;
use rest\activeResourceRecord;
use rest\activeResourceTable;

set_time_limit(0);

// Starting clock time in seconds
$start_time = microtime(true);

$url = $_ENV['vbac_url'] . '/api/squadTribePlus.php?token=' . $_ENV['vbac_api_token'] . '&withProvClear=true&plus=P.EMAIL_ADDRESS,P.PES_STATUS,SQUAD_NAME,TRIBE_NAME';

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
var_dump($http_code);

curl_close($curl);

// End clock time in seconds
$end_time = microtime(true);
  
// Calculate script execution time
$execution_time = ($end_time - $start_time);

echo " Execution time of script PART 1 = ".$execution_time." sec";

// Starting clock time in seconds
$start_time = microtime(true);

if ($err) {
    echo "cURL Error #:" . $err;
} else {

    $activeResourceTable  = new activeResourceTable(allTables::$ACTIVE_RESOURCE);
    $activeResourceRecord = new activeResourceRecord();

    $activeResourceTable->clear(false);

    $responseObj = json_decode($response);
    var_dump(count($responseObj));
    if (count($responseObj) > 0) {
        foreach ($responseObj as $personEntry) {
            $activeResourceRecord->setFromArray($personEntry);
            $db2result = $activeResourceTable->insert($activeResourceRecord);
    
            if(!$db2result){
                echo db2_stmt_error();
                echo db2_stmt_errormsg();
            }
        }
    }
    // echo count($responseObj) . ' records read from VBAC api';
}

// End clock time in seconds
$end_time = microtime(true);
  
// Calculate script execution time
$execution_time = ($end_time - $start_time);

echo " Execution time of script PART 2 = ".$execution_time." sec";