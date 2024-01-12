<?php

use itdq\FormClass;
use itdq\Trace;
use rest\staticBusinessUnitRecord;

?>
<div class='container'>
<h2>Define Business Unit</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticBusinessUnitRecord();
$record->displayForm(FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Business Unit</h2>

<div style='width: 75%'>
<table id='businessUnitTable' >
<thead>
<tr><th>Business Unit</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Business Unit</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);