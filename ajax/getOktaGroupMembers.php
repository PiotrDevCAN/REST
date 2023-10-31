<?php

use itdq\OKTAGroups;

set_time_limit(0);
ob_start();


// $cdiUsers = $OKTAGroups->getGroupMembers($GLOBALS['site']['cdiBgAz']);
// $adminUsers = $OKTAGroups->getGroupMembers($GLOBALS['site']['adminBgAz']);
// $demandUsers = $OKTAGroups->getGroupMembers($GLOBALS['site']['demandBgAz']);
// $supplyUsers = $OKTAGroups->getGroupMembers($GLOBALS['site']['supplyBgAz']);
// $supplyXUsers = $OKTAGroups->getGroupMembers($GLOBALS['site']['supplyXBgAz']);
// $rfsUsers = $OKTAGroups->getGroupMembers($GLOBALS['site']['rfsBgAz']);
// $rfsAdUsers =$OKTAGroups->getGroupMembers($GLOBALS['site']['rfsADBgAz']);
// $reportsUsers = $OKTAGroups->getGroupMembers($GLOBALS['site']['reportsBgAz']);


$redis = $GLOBALS['redis'];
$key = 'getOktaGroupMembers';
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
    
    $OKTAGroups = new OKTAGroups();
    $data = $OKTAGroups->getGroupMembers($GLOBALS['site']['cdiBgAz']);
    
    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

$messages = ob_get_clean();
$response = array('data'=>$data,'messages'=>$messages,'count'=>count($data),'source'=>$source);

ob_clean();
echo json_encode($response);