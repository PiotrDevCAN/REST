<?php

use itdq\FormClass;
use itdq\Trace;
use rest\staticValueStreamRecord;

?>
<div class='container'>
<h2>Define Value Stream</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticValueStreamRecord();
$record->displayForm(FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Value Stream</h2>

<div style='width: 75%'>
<table id='valueStreamTable' >
<thead>
<tr><th>Value Stream</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Value Stream</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);