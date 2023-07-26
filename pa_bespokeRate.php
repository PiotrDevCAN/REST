<?php
use itdq\Trace;
use rest\staticBespokeRateRecord;

?>
<div class='container'>
<h2>Define Bespoke Rate (Assignment)</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticBespokeRateRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Bespoke Rates</h2>

<div style='width: 75%'>
<table id='bespokeRateTable' >
<thead>
<tr><th>RFS Id</th><th>Resource Request</th><th>Resource Name</th><th>Resource Type</th><th>PS Band</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>RFS Id</th><th>Resource Request</th><th>Resource Name</th><th>Resource Type</th><th>PS Band</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditBespokeRateModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);