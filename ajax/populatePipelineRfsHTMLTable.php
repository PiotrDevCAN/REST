<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;
use itdq\Trace;
use rest\rfsPipelineView;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$length = isset($_POST['length']) ? $_POST['length'] : false;
$start = isset($_POST['start']) ? $_POST['start'] : false;
$draw = isset($_POST['draw']) ? $_POST['draw'] : false;

$columns = isset($_POST['columns']) ? $_POST['columns'] : false;
$ordering = isset($_POST['order']) ? $_POST['order'] : false;
$search = isset($_POST['search']) ? $_POST['search'] : false;

$rfsTable = new rfsPipelineView(allTables::$RFS_PIPELINE);
$data = $rfsTable->returnAsArray();
$message = ob_get_clean();
ob_start();
$response = array("data"=>$data,'message'=>$message);
// $response = array(
//     "draw" => $draw,
//     "recordsTotal" => $data['total'],
//     "recordsFiltered" => $data['total'],
//     "data" => $data['data'],
//     // 'error' => $data['error'],    // Do not include if there is no error.
//     'message' => $message,
//     'sql' => $data['sql']
// );
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

echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);