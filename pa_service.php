<?php
use itdq\Trace;
use rest\staticOrganisationRecord;

?>
<div class='container'>
<h2>Define Service</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$organisationRecord = new staticOrganisationRecord();
$organisationRecord->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Services</h2>

<div style='width: 75%'>
<table id='organisationTable' >
<thead>
<tr><th>Organisation</th><th>Service</th><th>Status</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Organisation</th><th>Service</th><th>Status</th></tr>
</tfoot>
</table>
</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
?>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);