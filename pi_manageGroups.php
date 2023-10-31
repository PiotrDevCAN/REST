<?php

use itdq\OKTAGroups;
use itdq\OKTAUsers;
use itdq\Trace;
use rest\staticOKTAGroupRecord;

?>
<div class='container'>
<h2>Define Okta Group employee record</h2>
<?php

// Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticOKTAGroupRecord();
$record->displayForm(itdq\FormClass::$modeDEFINE);
?>
</div>

<div class='container'>
	<h2>Manage Okta Groups Assignment</h2>
	
	<ul class="nav nav-pills">
		<li class="active"><a data-toggle="pill" href="#cdi-tab">CDI</a></li>
		<li><a data-toggle="pill" href="#admin-tab">Admin</a></li>
		<li><a data-toggle="pill" href="#demand-tab">Demand</a></li>
		<li><a data-toggle="pill" href="#supply-tab">Supply</a></li>
		<li><a data-toggle="pill" href="#supply-x-tab">Supply X</a></li>
		<li><a data-toggle="pill" href="#rfs-tab">RFS</a></li>
		<li><a data-toggle="pill" href="#rfs-ad-tab">RFS AD</a></li>
		<li><a data-toggle="pill" href="#reports-tab">Reports</a></li>
	</ul>

	<div class="tab-content">
		<div id="cdi-tab" class="tab-pane fade in active">
			<table id='CDIMembersTable' class='dataTable' data-group='CDI'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<div id="admin-tab" class="tab-pane fade">
			<table id='adminMembersTable' class='dataTable' data-group='admin'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<div id="demand-tab" class="tab-pane fade">
			<table id='demandMembersTable' class='dataTable' data-group='demand'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<div id="supply-tab" class="tab-pane fade">
			<table id='supplyMembersTable' class='dataTable' data-group='supply'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<div id="supply-x-tab" class="tab-pane fade">
			<table id='supplyXMembersTable' class='dataTable' data-group='supply-x'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<div id="rfs-tab" class="tab-pane fade">
			<table id='rfsMembersTable' class='dataTable' data-group='rfs'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<div id="rfs-ad-tab" class="tab-pane fade">
			<table id='rfsAdMembersTable' class='dataTable' data-group='rfs-ad'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
		<div id="reports-tab" class="tab-pane fade">
			<table id='reportsMembersTable' class='dataTable' data-group='reports'>
				<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<?php
	include_once 'includes/modalSaveResultModal.html';
?>
<?php
// Trace::pageLoadComplete($_SERVER['PHP_SELF']);