<?php

use rest\allTables;
use rest\rfsRecord;

$pipelineLiveArchive = !empty($_GET['pipelineLiveArchive']) ? trim($_GET['pipelineLiveArchive']) : 'live';
$organisation = trim($_GET['organisation']);

$resourceRequestTable = $pipelineLiveArchive=='archive'  ? allTables::$ARCHIVED_RESOURCE_REQUESTS : allTables::$RESOURCE_REQUESTS;

$sql = " Select distinct RFS ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $resourceRequestTable . " AS R ";
$sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
$sql.= " ON R.RFS = RFS.RFS_ID ";
$sql.= " WHERE 1=1 and RFS.RFS_ID is not null and R.RFS is not null ";
$sql.= !empty($_GET['term']) ? " AND UPPER(R.RFS) like '%" . htmlspecialchars(strtoupper(trim($_GET['term']))) . "%' " : null;
$sql.= $pipelineLiveArchive == 'live' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' " : null;
$sql.= $pipelineLiveArchive == 'pipeline' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " : null;
$sql.= $pipelineLiveArchive == 'archive' ? " AND ARCHIVE is not null " : " AND ARCHIVE is null " ;
$sql.= (!empty($organisation) && ($organisation!='All' )) ? " AND ORGANISATION='" . htmlspecialchars(trim($organisation)) . "' " : null;
$sql.= " ORDER BY 1 " ;
$rs = sqlsrv_query($GLOBALS['conn'], $sql);
$data = array();

if($rs){
    $data[] = array('id'=>'All','text'=>'All'); 
    while(($row = sqlsrv_fetch_array($rs))==true){
        $data[] = array('id'=>trim($row['RFS']),'text'=>trim($row['RFS']));        
    }
} else {
    echo $sql;
    echo json_encode(sqlsrv_errors());
    echo json_encode(sqlsrv_errors());
    throw new Exception('Db2 Exec failed in ' . __FILE__);
}

$response = array('results'=>$data);

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

echo json_encode($response);