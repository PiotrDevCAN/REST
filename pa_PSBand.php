<?php
use itdq\Trace;
use rest\staticPSBandRecord;

?>
<div class='container'>
<h2>Define PS Band</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticPSBandRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage PS Bands</h2>

<div style='width: 75%'>
<table id='PSBandTable' >
<thead>
<tr><th>PS Band Id</th><th>PS Band</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>PS Band Id</th><th>PS Band</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditPSBandModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);