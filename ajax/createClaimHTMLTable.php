<?php
use rest\allTables;
use rest\resourceRequestTable;
use rest\resourceRequestRecord;
use rest\rfsRecord;

set_time_limit(0);
ob_start();
?>
<table id='claimTable_id' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr>
<th>RFS ID</th><th>PRN</th><th>Project Title</th><th>Project Code</th><th>Requestor Name</th><th>Requestor Email</th><th>Value Stream</th><th>Business Unit</th>
<th>Link to PGMP</th><th>RFS Creator</th><th>RFS Created</th>
<th>Resource Ref</th><th>Organisation</th><th>Service</th><th>Description</th><th>Start Date</th><th>End Date</th>
<th>Hrs Per Week</th><th>Resource Name</th><th>Request Creator</th><th>Request Created</th>
<th>Cloned From</th><th>Status</th>
<th>Jan_20</th><th>Feb_20</th><th>Mar_20</th><th>Apr_20</th><th>May_20</th><th>Jun_20</th><th>Jul_20</th><th>Aug_20</th><th>Sep_20</th><th>Oct_20</th><th>Nov_20</th><th>Dec_20</th>
<th>Jan_21</th><th>Feb_21</th><th>Mar_21</th><th>Apr_21</th><th>May_21</th><th>Jun_21</th><th>Jul_21</th><th>Aug_21</th><th>Sep_21</th><th>Oct_21</th><th>Nov_21</th><th>Dec_21</th>
</tr></thead>
<tbody>
</tbody>
<tfoot><tr>
<th>RFS ID</th><th>PRN</th><th>Project Title</th><th>Project Code</th><th>Requestor Name</th><th>Requestor Email</th><th>Value Stream</th><th>Business Unit</th>
<th>RFS Creator</th><th>RFS Created</th>
<th>Link to PGMP</th><th>Resource Ref</th><th>Organisation</th><th>Service</th><th>Description</th><th>Start Date</th><th>End Date</th>
<th>Hrs Per Week</th><th>Resource Name</th><th>Request Creator</th><th>Request Created</th>
<th>Cloned From</th><th>Status</th>
<th>Jan_20</th><th>Feb_20</th><th>Mar_20</th><th>Apr_20</th><th>May_20</th><th>Jun_20</th><th>Jul_20</th><th>Aug_20</th><th>Sep_20</th><th>Oct_20</th><th>Nov_20</th><th>Dec_20</th>
<th>Jan_21</th><th>Feb_21</th><th>Mar_21</th><th>Apr_21</th><th>May_21</th><th>Jun_21</th><th>Jul_21</th><th>Aug_21</th><th>Sep_21</th><th>Oct_21</th><th>Nov_21</th><th>Dec_21</th>
</tr></tfoot></table>
<?php
