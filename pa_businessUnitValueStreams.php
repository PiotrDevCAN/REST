<?php

use itdq\FormClass;
use itdq\Trace;
use rest\staticValueStreamBusinessUnitRecord;

?>
<div class='container'>
<h2>Define Value Stream / Business Unit</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticValueStreamBusinessUnitRecord();
$record->displayForm(FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Business Unit to Value Streams assignment</h2>

<div style='width: 75%'>
<table id='valueStreamBusinessUnitTable' >
<thead>
<tr><th>Value Stream</th><th>Business Unit</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Value Stream</th><th>Business Unit</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);