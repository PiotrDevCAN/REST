<?php
use rest\allTables;
use rest\rfsRecord;

$pipelineLiveArchive = trim($_GET['pipelineLiveArchive']);


$sql = " Select distinct RFS ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$RESOURCE_REQUESTS . " AS R ";
$sql.= " LEFT JOIN " . $GLOBALS['Db2Schema'] . "." . allTables::$RFS . " AS RFS ";
$sql.= " ON R.RFS = RFS.RFS_ID ";
$sql.= " WHERE 1=1 and RFS.RFS_ID is not null and R.RFS is not null ";
$sql.= !empty($_GET['term']) ? " AND UPPER(R.RFS) like '%" . db2_escape_string(strtoupper(trim($_GET['term']))) . "%' " : null;
$sql.= $pipelineLiveArchive == 'live' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_LIVE . "' " : null;
$sql.= $pipelineLiveArchive == 'pipeline' ? " AND RFS_STATUS='" . rfsRecord::RFS_STATUS_PIPELINE . "' " : null;
$sql.= $pipelineLiveArchive == 'archive' ? " AND ARCHIVE is not null " : " AND ARCHIVE is null " ;
$sql.= " ORDER BY 1 " ;
$rs = db2_exec($GLOBALS['conn'], $sql);
$data = array();

if($rs){
    $data[] = array('id'=>'All','text'=>'All'); 
    while(($row=db2_fetch_assoc($rs))==true){
        $data[] = array('id'=>trim($row['RFS']),'text'=>trim($row['RFS']));        
    }
} else {
    echo $sql;
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
    throw new Exception('Db2 Exec failed in ' . __FILE__);
}

$response = array('results'=>$data);
echo json_encode($response);