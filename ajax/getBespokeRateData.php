<?php
use itdq\Trace;
use rest\allTables;
use rest\staticBespokeRateRecord;
use rest\staticBespokeRateTable;
use rest\staticPSBandRecord;
use rest\staticPSBandTable;
use rest\staticResourceRateTable;
use rest\staticResourceTypeRecord;
use rest\staticResourceTypeTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

if(isset($data['bespokeRateId'])){
    // get Bespoke Rate
    $bespokeRateTable = new staticBespokeRateTable(allTables::$BESPOKE_RATES);
    $bespokeRateRecord = new staticBespokeRateRecord();
    $bespokeRateRecord->set('BESPOKE_RATE_ID', $data['bespokeRateId']);
    $bespokeRateRecord->iterateVisible();
    $bespokeRateData = $bespokeRateTable->getRecord($bespokeRateRecord);

    // get Resource Type
    $staticResourceType = new staticResourceTypeTable(allTables::$STATIC_RESOURCE_TYPE);
    $staticResourceTypeRecord = new staticResourceTypeRecord();
    $staticResourceTypeRecord->set('RESOURCE_TYPE_ID', $bespokeRateData['RESOURCE_TYPE_ID']);
    $resourceTypeData = $staticResourceType->getRecord($staticResourceTypeRecord);

    // get PS Band
    $staticPSBand = new staticPSBandTable(allTables::$STATIC_PS_BAND);
    $staticPSBandRecord = new staticPSBandRecord();
    $staticPSBandRecord->set('BAND_ID', $bespokeRateData['PS_BAND_ID']);
    $PSBandData = $staticPSBand->getRecord($staticPSBandRecord);

    // get Day and Hourly Rate
    $staticResourceRate = new staticResourceRateTable(allTables::$RESOURCE_TYPE_RATES);
    $resourceTypeRateData = $staticResourceRate->returnForResourceTypeRate($bespokeRateData['RESOURCE_TYPE_ID'], $bespokeRateData['PS_BAND_ID']);

    $bespokeRateData = array(
        'BESPOKE_RATE_ID'=>$bespokeRateData['BESPOKE_RATE_ID'],
        'RFS_ID'=>$bespokeRateData['RFS_ID'],
        'RESOURCE_REFERENCE'=>$bespokeRateData['RESOURCE_REFERENCE'],
        'RESOURCE_TYPE_ID'=>$bespokeRateData['RESOURCE_TYPE_ID'],
        'RESOURCE_TYPE'=>$resourceTypeData['RESOURCE_TYPE'],
        'PS_BAND_ID'=>$bespokeRateData['PS_BAND_ID'],
        'BAND'=>$PSBandData['BAND'],
        'DAY_RATE'=>$resourceTypeRateData['DAY_RATE'],
        'HOURLY_RATE'=>$resourceTypeRateData['HOURLY_RATE']
    );
} else {
    $bespokeRateData = array();
}

Trace::pageLoadComplete($_SERVER['PHP_SELF']);

$response = $bespokeRateData;

ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

header('Content-Type: application/json');
echo json_encode($response);