<?php
use itdq\Trace;

Trace::pageOpening($_SERVER['PHP_SELF']);

?>

<div class='container'>
<h2>List of VBAC Active Resources</h2>

<div style='width: 75%'>
<table id='leaverTable' >
<thead>
<tr><th>Email Address</th><th>Notes ID</th><th>PES Status</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Email Address</th><th>Notes ID</th><th>PES Status</th></tr>
</tfoot>
</table>
</div>
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);