<?php
use itdq\Trace;
use rest\RRBespokeRateTable;

set_time_limit(0);

Trace::pageOpening($_SERVER['PHP_SELF']);

?>

<div class='container'>
<h3>Bespoke Rates Report</h3>
<p>Displays all distinct names of resources currently assigned to resource requests</p>
<form id='reportDates' class='form-horizontal'>
  <div class='form-group text-right' >
    <label for='selectRfs' class='col-md-1 control-label text-right'>RFS</label>
    <div class='col-md-2 text-left'>
      <select class='form-control select' id='selectRfs'
        name='selectRfs'
        data-placeholder="Select RFS" data-allow-clear="true"
        >
        <option value=''>Select RFS</option>
        <option value='All'>All</option>
      </select>
    </div>
  </div>
</form>
</div>

<div class='container-fluid'>
<div id='messageArea'></div>
<div id='bespokeRateTableDiv'>
<?php
  RRBespokeRateTable::buildHTMLTable();
?>
</div>
</div>

<?php
	include_once 'includes/modalSaveResultModal.html';
  include_once 'includes/modalDeleteResultModal.html';
  include_once 'includes/modalDeleteAssignment.html';
?>
</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
