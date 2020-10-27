<?php
// URL : https://soiwapi-new.icds.ibm.com/Lloyds/api/claim/soEkCfj8zGNDLZ8yXH2YJjpehd8ijzlS

$url = "https://soiwapi-new.icds.ibm.com/Lloyds/api/claim/soEkCfj8zGNDLZ8yXH2YJjpehd8ijzlS";

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

// var_dump($response);


// var_dump($responseObj);


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
    
   //  echo $response;
}