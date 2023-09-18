<?php
use rest\allTables;
use rest\resourceRequestHoursTable;

set_time_limit(0);
// ob_start();
if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
  if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
      ob_start("ob_gzhandler");
  } else {
      ob_start("ob_html_compress");
  }
} else {
  ob_start("ob_html_compress");
}

$start = microtime(True);

$resourceHoursTable = new resourceRequestHoursTable(allTables::$RESOURCE_REQUEST_HOURS);

$resourceHoursRs  = $resourceHoursTable->getRsWithPredicate(" RESOURCE_REFERENCE='" . trim($_POST['resourceReference']) . "' ORDER BY YEAR ASC, WEEK_NUMBER ASC ");
// $resourceTotalHrs = $resourceHoursTable->getTotalHoursForRequest($_POST['resourceReference']);

$monthColours = array(1=>'#bdbdbd',2=>'#eeeeee',3=>'#bdbdbd',4=>'#eeeeee',5=>'#bdbdbd',6=>'#eeeeee',7=>'#bdbdbd',8=>'#eeeeee',9=>'#bdbdbd',10=>'#eeeeee',11=>'#bdbdbd',12=>'#eeeeee',);
$claimMonths = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec',);

$data = array();

while($row = sqlsrv_fetch_array($resourceHoursRs)){
  $week = $row['DATE'];
  $hours = $row['HOURS'];
  $wef = $row['WEEK_ENDING_FRIDAY'];

  $stripe = $monthColours[$row['CLAIM_MONTH']];
  $claimMonth = $claimMonths[$row['CLAIM_MONTH']];
  $weekObj = new DateTime($wef);
  $weekFormatted = $weekObj->format('\W\e\e\k W - dS M y');
  
  $data[] = array(
    'week'=>$week,
    'hours'=>$hours,
    'wef'=>$wef,
    'stripe'=>$stripe,
    'claimMonth'=>$claimMonth,
    'weekFormatted'=>$weekFormatted
  );
}

$end = microtime(true);
$elapsed = ($end-$start);
$result = array(
  'data'=>$data,
  'start'=>$start,
  'end'=>$end,
  'elapsed'=>$elapsed
);
echo json_encode($result);

