<?php

use itdq\DateClass;

set_time_limit(0);
ob_start();

$nextMonthObj = new \DateTime();
$thisMonthObj = new \DateTime();
$thisYear = $thisMonthObj->format('Y');
$thisMonth = $thisMonthObj->format('m');
$thisMonthObj->setDate($thisYear, $thisMonth, 01);
$thisMonthsClaimCutoff = DateClass::claimMonth($thisMonthObj->format('d-m-Y'));
$thisMonthsClaimCutoff->add(new \DateInterval('P1D'));

$nextMonthObj >= $thisMonthsClaimCutoff ? $nextMonthObj->add(new \DateInterval('P1M')) : null;
$oneMonth = new DateInterval('P1M');
$monthLabels = array();

for ($i = 0; $i < 6; $i++) {
    $monthLabels[] = $nextMonthObj->format('M_y');
    $nextMonthObj->add($oneMonth);    
}
?>
<table id='claimTable_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr>
<th>RFS ID</th><th>PRN</th><th>Project Title</th><th>Project Code</th><th>Requestor Name</th><th>Requestor Email</th><th>Value Stream</th><th>Business Unit</th>
<th>Link to PGMP</th><th>RFS Creator</th><th>RFS Created</th>
<th>Resource Ref</th><th>Organisation</th><th>Service</th><th>Description</th><th>Start Date</th><th>End Date</th>
<th>Total Hours</th><th>Resource Name</th><th>Request Creator</th><th>Request Created</th>
<th>Cloned From</th><th>Status</th><th>Rate Type</th><th>Hours Type</th><th>RFS Status</th><th>RFS Type</th>
<?php 
foreach ($monthLabels as $label) {
    ?><th><?=$label?></th><?php 
}
?>
</tr></thead>
<tbody>
</tbody>
<tfoot><tr>
<th>RFS ID</th><th>PRN</th><th>Project Title</th><th>Project Code</th><th>Requestor Name</th><th>Requestor Email</th><th>Value Stream</th><th>Business Unit</th>
<th>RFS Creator</th><th>RFS Created</th>
<th>Link to PGMP</th><th>Resource Ref</th><th>Organisation</th><th>Service</th><th>Description</th><th>Start Date</th><th>End Date</th>
<th>Total Hours</th><th>Resource Name</th><th>Request Creator</th><th>Request Created</th>
<th>Cloned From</th><th>Status</th><th>Rate Type</th><th>Hours Type</th><th>RFS Status</th><th>RFS Type</th>
<?php 
foreach ($monthLabels as $label) {
    ?><th><?=$label?></th><?php 
}
?>
</tr></tfoot></table>
<?php
