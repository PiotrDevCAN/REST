<?php
use itdq\Trace;
use rest\allTables;
use rest\staticBandRecord;
use rest\staticBandTable;
use rest\staticPSBandRecord;
use rest\staticPSBandTable;
use rest\staticResourceRateRecord;
use rest\staticResourceRateTable;
use rest\staticResourceTypesRecord;
use rest\staticResourceTypesTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

if(isset($data['resourceRateId'])){
    // get Resource Rate
    $resourceRateTable = new staticResourceRateTable(allTables::$RESOURCE_TYPE_RATES);
    $resourceRateRecord = new staticResourceRateRecord();
    $resourceRateRecord->set('ID', $data['resourceRateId']);
    $resourceRateRecord->iterateVisible();
    $resourceRateData = $resourceRateTable->getRecord($resourceRateRecord);

    // get Resource Type
    $staticResourceType = new staticResourceTypesTable(allTables::$STATIC_RESOURCE_TYPE);
    $staticResourceTypeRecord = new staticResourceTypesRecord();
    $staticResourceTypeRecord->set('RESOURCE_TYPE_ID', $resourceRateData['RESOURCE_TYPE_ID']);
    $resourceTypeData = $staticResourceType->getRecord($staticResourceTypeRecord);

    // get PS Band
    $staticPSBand = new staticPSBandTable(allTables::$STATIC_PS_BAND);
    $staticPSBandRecord = new staticPSBandRecord();
    $staticPSBandRecord->set('BAND_ID', $resourceRateData['PS_BAND_ID']);
    $PSBandData = $staticPSBand->getRecord($staticPSBandRecord);

    // get Band
    $staticBand = new staticBandTable(allTables::$STATIC_BAND);
    $staticBandRecord = new staticBandRecord();
    $staticBandRecord->set('BAND_ID', $resourceRateData['BAND_ID']);
    $bandData = $staticBand->getRecord($staticPSBandRecord);

    $rateCardData = array(
        'ID'=>$resourceRateData['ID'],
        'RESOURCE_TYPE_ID'=>$resourceRateData['RESOURCE_TYPE_ID'],
        'RESOURCE_TYPE'=>$resourceTypeData['RESOURCE_TYPE'],
        'PS_BAND_ID'=>$resourceRateData['PS_BAND_ID'],
        'PS_BAND'=>$PSBandData['BAND'],
        'BAND_ID'=>$resourceRateData['BAND_ID'],
        'BAND'=>$bandData['BAND'],
        'TIME_PERIOD_START'=>$resourceRateData['TIME_PERIOD_START'],
        'TIME_PERIOD_END'=>$resourceRateData['TIME_PERIOD_END'],
        'DAY_RATE'=>$resourceRateData['DAY_RATE'],
        'HOURLY_RATE'=>$resourceRateData['HOURLY_RATE']
    );
} else {
    $rateCardData = array();
}

Trace::pageLoadComplete($_SERVER['PHP_SELF']);

$response = $rateCardData;

ob_clean();
echo json_encode($response);