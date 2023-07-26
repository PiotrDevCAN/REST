<?php
use itdq\Trace;
use rest\staticResourceTypeRecord;

?>
<div class='container'>
<h2>Define Resource Type</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticResourceTypeRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Resource Types</h2>

<div style='width: 75%'>
<table id='resourceTypeTable' >
<thead>
<tr><th>Resource Type Id</th><th>Resource Type</th><th>Hours per Day</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Resource Type Id</th><th>Resource Type</th><th>Hours per Day</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditTypeModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);