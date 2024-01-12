<?php
use itdq\Trace;
use rest\staticOrganisationServiceRecord;

?>
<div class='container'>
<h2>Define Organisation / Service</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticOrganisationServiceRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);

?>
</div>

<div class='container'>
<h2>Manage Service to Organisation assignment</h2>

<div style='width: 75%'>
<table id='organisationServiceTable' >
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