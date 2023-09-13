<?php
use rest\allTables;
use rest\activeResourceRecord;
use rest\activeResourceTable;

set_time_limit(0);

$url = $_ENV['vbac_url'] . '/api/squadTribePlus.php?token=' . $_ENV['vbac_api_token'] . '&withProvClear=true&plus=P.EMAIL_ADDRESS,P.PES_STATUS,SQUAD_NAME,TRIBE_NAME';

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
    
    // $rootDir = (stripos($_ENV['environment'], 'dev') || stripos($_ENV['environment'], 'local')) ? '/' : '/';
    $rootDir = '/';
    $fileName = 'InactiveVBACPersons.csv';

    $target_dir = $rootDir . "downloads/" . $_ENV['environment'];
    $target_file = $target_dir . "_" .  $fileName;

    $file = fopen($target_file,"w");

    $responseObj = json_decode($response, true);
    if (count($responseObj) > 0) {
        foreach ($responseObj as $key => $personEntry) {
            fputcsv($file, $personEntry);
        }
    }

    fclose($file);
}