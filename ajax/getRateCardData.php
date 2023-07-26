<?php
use itdq\Trace;
use rest\allTables;
use rest\staticPSBandRecord;
use rest\staticResourcePSBandsTable;
use rest\staticResourceRateTable;
use rest\staticResourceTraitsRecord;
use rest\staticResourceTraitsTable;
use rest\staticResourceTypeRecord;
use rest\staticResourceTypesTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);

if(isset($data['resourceName'])){
    // get Resource Trait
    $staticResourceTrait = new staticResourceTraitsTable(allTables::$RESOURCE_TRAITS);
    $resourceTraitRecord = new staticResourceTraitsRecord();
    $resourceTraitRecord->set('ID', $data['resourceTraitId']);
    $resourceTraitRecord->iterateVisible();
    $resourceTraitsData = $resourceTraitTable->getRecord($resourceTraitRecord);

    // get Resource Type
    $staticResourceType = new staticResourceTypesTable(allTables::$STATIC_RESOURCE_TYPE);
    $staticResourceTypeRecord = new staticResourceTypeRecord();
    $staticResourceTypeRecord->set('RESOURCE_TYPE_ID', $resourceTraitsData['RESOURCE_TYPE_ID']);
    $resourceTypeData = $staticResourceType->getRecord($staticResourceTypeRecord);

    // get PS Band
    $staticPSBand = new staticResourcePSBandsTable(allTables::$STATIC_PS_BAND);
    $staticPSBandRecord = new staticPSBandRecord();
    $staticPSBandRecord->set('BAND_ID', $resourceTraitsData['PS_BAND_ID']);
    $PSBandData = $staticPSBand->getRecord($staticPSBandRecord);

    // get Day and Hourly Rate
    $staticResourceRate = new staticResourceRateTable(allTables::$RESOURCE_TYPE_RATES);
    $resourceTypeRateData = $staticResourceRate->returnForResourceTypeRate($resourceTypeData['RESOURCE_TYPE_ID'], $PSBandData['PS_BAND_ID']);

    $rateCardData = array(
        'RESOURCE_NAME'=>$data['resourceName'],
        'RESOURCE_TYPE_ID'=>$resourceTypeData['RESOURCE_TYPE_ID'],
        'RESOURCE_TYPE'=>$resourceTypeData['RESOURCE_TYPE'],
        'PS_BAND_ID'=>$PSBandData['PS_BAND_ID'],
        'BAND'=>$PSBandData['BAND'],
        'DAY_RATE'=>$resourceTypeRateData['DAY_RATE'],
        'HOURLY_RATE'=>$resourceTypeRateData['HOURLY_RATE']
    );
} else {
    $rateCardData = array();
}

Trace::pageLoadComplete($_SERVER['PHP_SELF']);

$response = $rateCardData;

ob_clean();
echo json_encode($response);