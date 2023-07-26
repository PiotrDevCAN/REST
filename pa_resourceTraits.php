<?php
use itdq\Trace;
use rest\staticResourceTraitsRecord;

?>
<div class='container'>
<h2>Define Resource Traits (Assignment)</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticResourceTraitsRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Resource Traits</h2>

<div style='width: 75%'>
<table id='resourceTraitTable' >
<thead>
<tr><th>Trait Id</th><th>Resource Name</th><th>Resource Type</th><th>PS Band</th><th>Overrides PS Band</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Trait Id</th><th>Resource Name</th><th>Resource Type</th><th>PS Band</th><th>Overrides PS Band</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
	include_once 'includes/modalDeleteResultModal.html';
	include_once 'includes/modalEditResourceTraitModal.html';
	include_once 'includes/modalDeleteAssignment.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);