<?php
use itdq\Trace;
use rest\staticServiceRecord;

?>
<div class='container'>
<h2>Define Service</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$serviceRecord = new staticServiceRecord();
$serviceRecord->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Services</h2>

<div style='width: 75%'>
<table id='serviceTable' >
<thead>
<tr><th>Service</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Service</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);