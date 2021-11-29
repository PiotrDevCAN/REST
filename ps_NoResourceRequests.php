<?php

use itdq\Trace;
use itdq\Loader;
use rest\allTables;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);
$loader = new Loader();
$allRfs = $loader->load('RFS_ID',allTables::$RFS, " ARCHIVE is null ");
$allValueStream = $loader->load('VALUE_STREAM',allTables::$RFS, " ARCHIVE is null ");
$allBusinessUnits = $loader->load('BUSINESS_UNIT',allTables::$RFS, " ARCHIVE is null ");
$allRequestor = $loader->load('REQUESTOR_EMAIL',allTables::$RFS, " ARCHIVE is null ");

// $defaultForPipelineLive = $_SESSION['isRfs'] ? null : ' checked ';
// $canSeeLive = $_SESSION['isRfs'] ? ' disabled ' : null;
?>
<div class='container'>

<h3>Requests With None Active Resource</h3>

</div>

<hr/>

<div class='container-fluid'>
<div id='noneActiveTableDiv'>
</div>
</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<style type="text/css">

.dataTables_wrapper .dataTables_processing {
  background-color:#006699;
  height: 60px;
}
td.dataTables_empty {
  color:white;
	text-align: center;
	font-size: 20px;
	background-color:#006699;
}

</style>
<script type='text/javascript'>

$(document).ready(function() {
	
	$(".select").select2();

  var rfs = new Rfs();
  rfs.buildNoneActiveReport();
  rfs.listenForSelectRequestor();
  rfs.listenForSelectValueStream();
  rfs.listenForSelectBusinessUnit();
  rfs.listenForSelectRfs();
});
</script>