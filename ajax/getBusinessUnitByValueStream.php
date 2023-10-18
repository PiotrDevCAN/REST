<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$redis = $GLOBALS['redis'];
$key = 'getBusinessUnitByValueStream_'.htmlspecialchars($_POST['valueStream']);
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
    
    $predicate = !empty($_POST['valueStream']) ? "VALUE_STREAM = '" . $_POST['valueStream'] . "'" : false ;
    
    $loader = new Loader();
    $data = $loader->load('BUSINESS_UNIT', allTables::$STATIC_VALUE_STREAM, $predicate, FALSE);
    
    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

if (count($data) > 0) {
    foreach($data as $key => $value) {
        $businessUnit = $key;
    }
} else {
    $businessUnit = '';
}

$messages = ob_get_clean();
$response = array('businessUnit'=>$businessUnit,'messages'=>$messages,'source'=>$source);

ob_clean();
echo json_encode($response);