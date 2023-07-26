<?php
use itdq\Trace;
use rest\staticResourcePSBandsRecord;

?>
<div class='container'>
<h2>Define Resource to PS Band Assignment</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticResourcePSBandsRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Resource to PS Band Assignments</h2>

<div style='width: 75%'>
<table id='resourcePSBandsTable' >
<thead>
<tr><th>Resource Name</th><th>PS Band</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Resource Name</th><th>PS Band</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditResourcePSBandModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);