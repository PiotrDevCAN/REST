<?php
use rest\allTables;
use rest\rfsTable;
use rest\rfsRecord;
use itdq\Trace;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);

$length = isset($_POST['length']) ? $_POST['length'] : false;
$start = isset($_POST['start']) ? $_POST['start'] : false;
$draw = isset($_POST['draw']) ? $_POST['draw'] : false;

$rfsTable = new rfsTable(allTables::$RFS);

// $rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : null;
// $valueStream  = !empty($_POST['valuestream']) ? trim($_POST['valuestream']) : null;
// $businessUnit = !empty($_POST['businessunit']) ? trim($_POST['businessunit']) : null;
// $requestor    = !empty($_POST['requestor']) ? trim($_POST['requestor']) : null;

$rfsId = !empty($_POST['rfsid']) ? $_POST['rfsid'] : 'All';
$valueStream  = !empty($_POST['valuestream']) ? trim($_POST['valuestream']) : 'All';
$businessUnit = !empty($_POST['businessunit']) ? trim($_POST['businessunit']) : 'All';
$requestor    = !empty($_POST['requestor']) ? trim($_POST['requestor']) : 'All';

$rfsId = $rfsId=='All' ? null : $rfsId;
$valueStream = $valueStream=='All' ? null : $valueStream;
$businessUnit = $businessUnit=='All' ? null : $businessUnit;
$requestor = $requestor=='All' ? null : $requestor;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) ? " AND RFS_ID='" . db2_escape_string($rfsId) . "' " : null;
$predicate .= ! empty($valueStream) ? " AND VALUE_STREAM='" . db2_escape_string($valueStream) . "' " : null;
$predicate .= ! empty($businessUnit) ? " AND BUSINESS_UNIT='" . db2_escape_string($businessUnit) . "' " : null;
$predicate .= ! empty($requestor) ? " AND lower(REQUESTOR_EMAIL)='" . db2_escape_string(strtolower($requestor)) . "' " : null;

// merge all preducates
$predicate .= $rfsTable->prepareSearchPredicate() . $rfsTable->prepareOrderingPredicate();

// if (empty($rfsId) && empty($valueStream) && empty($requestor) && empty($businessUnit)) {
//     $response = array(
//         'messages' => 'No Drop Down Selection Made By User',
//         "data" => array()
//     );
// } else {

    $data = $rfsTable->returnNoneActiveReportAsArray($predicate, false, $length, $start);
    $message = ob_get_clean();
    ob_start();

    $response = array(
        "draw" => $draw,
        "recordsTotal" => $data['total'],
        "recordsFiltered" => $data['total'],
        "data" => $data['data'],
        // 'error' => $data['error'],    // Do not include if there is no error.
        'message' => $message,
        'sql' => $data['sql']
    );
// }

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