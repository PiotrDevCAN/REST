<?php
use itdq\Trace;
use rest\staticResourceRateRecord;

?>
<div class='container'>
<h2>Define Resource Rate</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticResourceRateRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Resource Rates</h2>

<div style='width: 75%'>
<table id='resourceRateTable' >
<thead>
<tr><th>Resource Type</th><th>PS Band</th><th>Time Period Start</th><th>Time Period End</th><th>Day Rate</th><th>Hourly Rate</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Resource Type</th><th>PS Band</th><th>Time Period Start</th><th>Time Period End</th><th>Day Rate</th><th>Hourly Rate</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditResourceRateModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);