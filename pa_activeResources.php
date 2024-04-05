<?php
use itdq\Trace;

Trace::pageOpening($_SERVER['PHP_SELF']);

?>

<div class='container'>
<h2>List of VBAC Active Resources</h2>
</div>

<hr/>

<div class='container-fluid'>
<div style='width: 100%'>
<table id='leaverTable' >
<thead>
<tr><th>CNUM</th><th>Worker Id</th><th>Email Address</th><th>Notes ID</th><th>First Name</th><th>Last Name</th><th>PES Status</th><th>Tribe Name</th><th>Tribe Name Mapped</th><th>Squad Name</th><th>Assignment Type</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>CNUM</th><th>Worker Id</th><th>Email Address</th><th>Notes ID</th><th>First Name</th><th>Last Name</th><th>PES Status</th><th>Tribe Name</th><th>Tribe Name Mapped</th><th>Squad Name</th><th>Assignment Type</th></tr>
</tfoot>
</table>
</div>
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);