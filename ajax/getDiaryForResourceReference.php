<?php
use itdq\Trace;
use rest\resourceRequestDiaryTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

ob_start();

$redis = $GLOBALS['redis'];
$key = 'getDiaryForResourceReference_'.htmlspecialchars($_POST['resourceReference']);
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';
    
    $data = resourceRequestDiaryTable::getFormattedDiaryForResourceRequest($_POST['resourceReference']);

    $redis->set($redisKey, json_encode($data));
    $redis->expire($redisKey, REDIS_EXPIRE);
} else {
    $source = 'Redis Server';
    $data = json_decode($redis->get($redisKey), true);
}

$messages = ob_get_clean();
ob_start();
$response = array('diary'=>$data,'messages'=>$messages,'source'=>$source);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);
ob_clean();
echo json_encode($response);