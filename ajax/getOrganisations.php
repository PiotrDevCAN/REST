<?php

use itdq\Loader;
use rest\allTables;

set_time_limit(0);
ob_start();

$redis = $GLOBALS['redis'];
$key = 'getOrganisations';
$redisKey = md5($key.'_key_'.$_ENV['environment']);
if (!$redis->get($redisKey)) {
    $source = 'SQL Server';

    $predicate=null;
    
    $sql = " SELECT DISTINCT ORG.ORGANISATION ";
    $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS RR ";
    $sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_ORGANISATION . " AS ORG ";
    $sql.= " ON RR.ORGANISATION = ORG.ORGANISATION_ID ";
    $sql.= " WHERE 1=1 AND RR.ORGANISATION is not null ";
    $sql.= " ORDER BY 1 " ;
    $rs = sqlsrv_query($GLOBALS['conn'], $sql);
    
    $data = array();

    if($rs){
        while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $data[trim($row['ORGANISATION'])] = trim($row['ORGANISATION']);
        }
    } else {
        echo $sql;
        echo json_encode(sqlsrv_errors());
        echo json_encode(sqlsrv_errors());
        throw new Exception('Db2 Exec failed in ' . __FILE__);
    }

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