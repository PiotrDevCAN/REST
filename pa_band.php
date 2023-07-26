<?php
use itdq\Trace;
use rest\staticBandRecord;

?>
<div class='container'>
<h2>Define Band</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticBandRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Bands</h2>

<div style='width: 75%'>
<table id='bandTable' >
<thead>
<tr><th>Band Id</th><th>Band</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Band Id</th><th>Band</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditBandModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);