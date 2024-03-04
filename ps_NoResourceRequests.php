<?php

use itdq\Trace;
use rest\rfsNoneActiveTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<div class='container'>

<h3>Requests With None Active Resource</h3>

</div>

<hr/>

<div class='container-fluid'>
<div id='noneActiveTableDiv'>
<?php
  rfsNoneActiveTable::buildHTMLTable('noneActive');
?>
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