<?php

use rest\allTables;
use rest\rfsTable;

$rfsId = !empty($_GET['rfsid']) ? $_GET['rfsid'] : null;
$valueStream  = !empty($_GET['valuestream'])   ? trim($_GET['valuestream']) : null;
$businessUnit = !empty($_GET['businessunit']) ? trim($_GET['businessunit']) : null;
$requestor    = !empty($_GET['requestor'])    ? trim($_GET['requestor']) : null;

$predicate = " 1=1 ";
$predicate .= ! empty($rfsId) && $rfsId !=='All'  ? " AND RFS_ID='" . db2_escape_string($rfsId) . "' " : null;
$predicate .= ! empty($valueStream) && $valueStream!=='All' ? " AND VALUE_STREAM='" . db2_escape_string($valueStream) . "' " : null;
$predicate .= ! empty($businessUnit) && $businessUnit!=='All' ? " AND BUSINESS_UNIT='" . db2_escape_string($businessUnit) . "' " : null;
$predicate .= ! empty($requestor) && $requestor !=='All' ? " AND lower(REQUESTOR_EMAIL)='" . db2_escape_string(strtolower($requestor)) . "' " : null;

$rfsTable = new rfsTable(allTables::$RFS);
$data = $rfsTable->returnClaimReportAsJson($predicate);

$messages = ob_get_clean();

$success = empty($messages);

header('Content-Type: application/json');
echo json_encode(array('success'=>$success,'data'=>$data,'messages'=>$messages));