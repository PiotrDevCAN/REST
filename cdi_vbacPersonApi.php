<?php
// URL : https://vbac-ut.dal1a.ciocloud.nonprod.intranet.ibm.com/api/employeesLeft.php?token=soEkCfj8zGNDLZ8yXH2YJjpehd8ijzlS";

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
    $responseObj = json_decode($response);
    echo $responseObj->status;
    foreach ($responseObj->data as $claimEntry) {
        echo "<pre>";
        print_r($claimEntry);
        echo "</pre>";
    }
}