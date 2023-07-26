<?php
use itdq\Trace;
use rest\staticResourceTypesRecord;

?>
<div class='container'>
<h2>Define Resource to Type Assignment</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticResourceTypesRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Resource to Type Assignments</h2>

<div style='width: 75%'>
<table id='resourceTypesTable' >
<thead>
<tr><th>Resource Name</th><th>Resource Type</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Resource Name</th><th>Resource Type</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditResourceTypeModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);