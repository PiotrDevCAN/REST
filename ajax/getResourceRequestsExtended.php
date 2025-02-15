<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$redis = $GLOBALS['redis'];
$key = 'getResourceRequestsExtended';
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
        
    $predicate=null;
    
    $loader = new Loader();
    $data = $loader->loadIndexed('RESOURCE_REFERENCE','RESOURCE_REFERENCE',allTables::$RESOURCE_REQUESTS,$predicate);

    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

$messages = ob_get_clean();
$response = array('data'=>$data,'messages'=>$messages,'count'=>count($data),'source'=>$source);

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