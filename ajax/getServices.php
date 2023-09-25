<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$redis = $GLOBALS['redis'];
$key = 'getServices';
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
        
    $predicate=null;
    
    $loader = new Loader();
    $data = $loader->load('SERVICE', allTables::$RESOURCE_REQUESTS, null, false);

    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

ob_clean();
echo json_encode($response);